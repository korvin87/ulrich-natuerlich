<?php
/*
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use Abavo\AbavoSearch\Domain\Repository\IndexerRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Domain\TermManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * IndexCommandController
 *
 * @author mbruckmoser
 */
class IndexCommandController extends CommandController
{
    /**
     * indexerRepository
     *
     * @var IndexerRepository
     */
    protected $indexerRepository;

    /**
     * indexController
     *
     * @var IndexController
     */
    protected $indexController;

    /**
     * percistenceManager
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * lockFile
     * @var string
     */
    protected $lockFile = null;

    /**
     * @var $this->logger \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger = null;

    public const LOCK_FILENAME = 'abavo_search-IndexUpdateCommand.lock';

    /**
     *
     * @param \string $storagePid comma separated
     * @return bool
     */
    public function updateCommand($storagePid = '0')
    {
        try {
            /**
             * @var $this->logger \TYPO3\CMS\Core\Log\Logger
             */
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);

            //Write index.lock in tempfolder to prevent other users to start indexing
            $this->handleLockFile('create');

            // Get IndexerConfigurations
            if (!(boolean) $storagePid) {
                throw new IndexException('updateCommand: storagePid must be greater than 0.');
            }
            $querySettings = $this->indexerRepository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds(explode(',', $storagePid));
            $this->indexerRepository->setDefaultQuerySettings($querySettings);


            // Get Indexers
            $indexers    = $this->indexerRepository->findAll();
            $indexResult = [];

            if (count($indexers) == 0) {
                throw new IndexException('updateCommand: No indexer configurations found.');
            }

            foreach ($indexers as $indexer) {

                // Make Class Instance
                $config = $indexer->getConfig();
                if (!$config['settings'] || (!$config['settings']['class'])) {
                    throw new IndexException('updateCommand: No indexer class settings defined.');
                }

                $indexerClass = $config['settings']['class'];
                #\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($indexer,$indexerClass);
                if (class_exists($indexerClass)) {
                    $indexerClass = new $indexerClass($config['settings']);
                } else {
                    throw new IndexException('updateCommand: Class "'.$indexerClass.'" not defined. Check autoloader.');
                }

                // Check and execute getDate method
                if (!method_exists($indexerClass, 'getData')) {
                    throw new IndexException('updateCommand: No getData method in '.$config['settings']['class'].' defined.');
                }
                $data = $indexerClass->getData($indexer);

                // Check and execute getDuration method
                if (!method_exists($indexerClass, 'getDuration')) {
                    throw new IndexException('updateCommand: No getDuration method in '.$config['settings']['class'].' defined.');
                }
                $duration = $indexerClass->getDuration($indexer);


                // Cleanup Indexer´s index and add new indexes
                $doCleanIndex = $config['settings']['class'].'::DO_CLEAN_INDEX';
                if (defined($doCleanIndex) === false || (defined($doCleanIndex) === true && constant($doCleanIndex) === true)) {
                    if (!$this->indexController->cleanIndexForCommandController($indexer)) {
                        throw new IndexException('updateCommand: cleanIndex failed.');
                    }
                }
                if (!empty($data)) {
                    if (!$this->indexController->createIndexCollectionForCommandController($data)) {
                        throw new IndexException('updateCommand: createIndex failed.');
                    }
                }


                // Result
                $indexResult[$indexer->getUid()] = [
                    'title' => $indexer->getTitle(),
                    'indexCount' => is_countable($data) ? count($data) : 0,
                    'duration' => $duration
                ];
            }

            // Persist all data because of term-cleanup we cant wait
            $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
            $this->persistenceManager->persistAll();

            // Clean unused Index
            if (!$this->indexController->cleanUnusedIndexForCommandController($storagePid, array_keys($indexResult))) {
                throw new IndexException('updateCommand: cleanUnusedIndex failed.');
            }

            // Clean unused Terms
            $cleanUnusedTerms = TermManager::cleanUnusedTermsFromPidForCommandController($storagePid);
            if ($cleanUnusedTerms !== true) {
                $message = (is_object($cleanUnusedTerms)) ? 'updateCommand - Clean unused Terms: '.$cleanUnusedTerms->getMessage() : 'updateCommand: cleanUnusedTerms failed.';
                throw new IndexException($message);
            }

            /**
             *  Unlick LOCK-File and return results
             */
            $this->handleLockFile('delete');

            // Logging
            $this->logger->log(LogLevel::NOTICE, 'Results', $indexResult);

            // Return results
            return $indexResult;
            //
        } catch (\Exception $e) {
            // Debugger
            DebuggerUtility::var_dump(['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()], 'abavo_search Error');
            // Logger
            $this->logger->log(
                LogLevel::ERROR, $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'code' => $e->getCode(), 'trace' => $e->getTrace()]
            );
            return false;
        }
    }

    /**
     * Handle the LOCK-File
     * 
     * @param string $mode
     * @throws IndexException
     */
    private function handleLockFile($mode = 'create')
    {
        /**
         * Early exit in Development context
         */
        if (Environment::getContext()->isDevelopment()) {
            return;
        }
        $lockFilePath = 'typo3temp/var/locks/';
        if (version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '9.5', '>=') && Environment::isComposerMode()) {
            $lockFilePath = Environment::getProjectPath().'/var/lock/';
        }
        $lockFilePath = GeneralUtility::getFileAbsFileName($lockFilePath);
        if (!is_dir($lockFilePath)) {
            mkdir($lockFilePath);
        }
        $this->lockFile = $lockFilePath.self::LOCK_FILENAME;

        //Write index.lock in tempfolder to prevent other users to start indexing
        switch ($mode) {

            case 'create':

                if (file_exists($this->lockFile)) {

                    $dateTime = new \DateTime('-10 minutes');
                    if (filemtime($this->lockFile) > $dateTime->getTimestamp()) {
                        throw new IndexException('updateCommand: Indexing is currently running or a error has occurred. Please try again later or '.$lockFilePath);
                    } else {
                        // Reset LOCK-File
                        $this->handleLockFile('delete');
                        $this->handleLockFile('create');
                    }
                } else {
                    if (!touch($this->lockFile)) {
                        throw new IndexException('updateCommand: Error touching lock file '.$lockFilePath);
                    }
                }
                break;

            case 'delete':
                if (!unlink($this->lockFile)) {
                    throw new IndexException('updateCommand: Error unlinking lock file '.$lockFilePath);
                }
        }
    }

    public function injectIndexerRepository(IndexerRepository $indexerRepository): void
    {
        $this->indexerRepository = $indexerRepository;
    }

    public function injectIndexController(IndexController $indexController): void
    {
        $this->indexController = $indexController;
    }
}