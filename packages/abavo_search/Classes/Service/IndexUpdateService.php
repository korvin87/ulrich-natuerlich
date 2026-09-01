<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Abavo\AbavoSearch\Service;

use Abavo\AbavoSearch\Controller\IndexController;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Domain\Repository\IndexerRepository;
use Abavo\AbavoSearch\Domain\TermManager;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * IndexUpdateService
 *
 * Runs the abavo_search indexer against one or more storage pids. This is a
 * plain service extracted from the removed Extbase CommandController and is
 * consumed by:
 *   - the CLI command Abavo\AbavoSearch\Command\UpdateIndexCommand
 *   - the backend module (BackendController::updateindexAction)
 *
 * On failure the service throws; callers decide how to surface the error.
 */
class IndexUpdateService
{
    public const LOCK_FILENAME = 'abavo_search-IndexUpdateCommand.lock';

    protected IndexerRepository $indexerRepository;
    protected IndexController $indexController;
    protected PersistenceManager $persistenceManager;
    protected Logger $logger;
    protected ?string $lockFile = null;

    public function __construct(
        IndexerRepository $indexerRepository,
        IndexController $indexController,
        PersistenceManager $persistenceManager,
        LogManager $logManager
    ) {
        $this->indexerRepository  = $indexerRepository;
        $this->indexController    = $indexController;
        $this->persistenceManager = $persistenceManager;
        $this->logger             = $logManager->getLogger(self::class);
    }

    /**
     * Run all configured indexers for the given storage pid(s).
     *
     * @param string $storagePid one pid, or a comma-separated list
     * @return array<int, array{title: string, indexCount: int, duration: mixed}>
     * @throws \Exception on any indexing failure (also cleans up its lock file)
     */
    public function updateIndex(string $storagePid): array
    {
        // Indexing can take longer than a browser session — keep going even
        // when the operator closes the tab, and don't get cut off by the
        // request's max_execution_time. Combined with the finally/shutdown
        // cleanup below, this stops the backend "running" badge from getting
        // stuck when the browser gives up mid-run.
        @ignore_user_abort(true);
        @set_time_limit(0);

        try {
            $this->handleLockFile('create');

            if ((int)$storagePid <= 0) {
                throw new IndexException('updateIndex: storagePid must be greater than 0.');
            }

            $querySettings = $this->indexerRepository->createQuery()->getQuerySettings();
            $querySettings->setStoragePageIds(explode(',', $storagePid));
            $this->indexerRepository->setDefaultQuerySettings($querySettings);

            $indexers    = $this->indexerRepository->findAll();
            $indexResult = [];

            if (count($indexers) === 0) {
                throw new IndexException('updateIndex: No indexer configurations found.');
            }

            foreach ($indexers as $indexer) {
                $config = $indexer->getConfig();
                if (empty($config['settings']) || empty($config['settings']['class'])) {
                    throw new IndexException('updateIndex: No indexer class settings defined.');
                }

                $indexerClass = $config['settings']['class'];
                if (!class_exists($indexerClass)) {
                    throw new IndexException('updateIndex: Class "' . $indexerClass . '" not defined. Check autoloader.');
                }
                $indexerInstance = new $indexerClass($config['settings']);

                if (!method_exists($indexerInstance, 'getData')) {
                    throw new IndexException('updateIndex: No getData method in ' . $indexerClass . ' defined.');
                }
                $data = $indexerInstance->getData($indexer);

                if (!method_exists($indexerInstance, 'getDuration')) {
                    throw new IndexException('updateIndex: No getDuration method in ' . $indexerClass . ' defined.');
                }
                $duration = $indexerInstance->getDuration($indexer);

                // Cleanup this indexer's index unless the class opts out via DO_CLEAN_INDEX = false.
                $doCleanIndex = $indexerClass . '::DO_CLEAN_INDEX';
                if (!defined($doCleanIndex) || constant($doCleanIndex) === true) {
                    if (!$this->indexController->cleanIndexForCommandController($indexer)) {
                        throw new IndexException('updateIndex: cleanIndex failed.');
                    }
                }
                if (!empty($data)) {
                    if (!$this->indexController->createIndexCollectionForCommandController($data)) {
                        throw new IndexException('updateIndex: createIndex failed.');
                    }
                }

                $indexResult[$indexer->getUid()] = [
                    'title'      => $indexer->getTitle(),
                    'indexCount' => is_countable($data) ? count($data) : 0,
                    'duration'   => $duration,
                ];
            }

            // Persist everything now so the term-cleanup below sees a clean state.
            $this->persistenceManager->persistAll();

            if (!$this->indexController->cleanUnusedIndexForCommandController($storagePid, array_keys($indexResult))) {
                throw new IndexException('updateIndex: cleanUnusedIndex failed.');
            }

            $cleanUnusedTerms = TermManager::cleanUnusedTermsFromPidForCommandController($storagePid);
            if ($cleanUnusedTerms !== true) {
                $message = is_object($cleanUnusedTerms)
                    ? 'updateIndex - Clean unused Terms: ' . $cleanUnusedTerms->getMessage()
                    : 'updateIndex: cleanUnusedTerms failed.';
                throw new IndexException($message);
            }

            $this->logger->log(LogLevel::NOTICE, 'Results', $indexResult);

            return $indexResult;
        } catch (\Exception $e) {
            $this->logger->log(
                LogLevel::ERROR,
                $e->getMessage(),
                ['file' => $e->getFile(), 'line' => $e->getLine(), 'code' => $e->getCode()]
            );
            throw $e;
        } finally {
            // Release the lock no matter how we leave — successful return,
            // caught exception, or fatal error that unwinds through here.
            $this->releaseLockFile();
        }
    }

    /**
     * Best-effort unlink. Safe to call multiple times.
     */
    private function releaseLockFile(): void
    {
        if ($this->lockFile !== null && file_exists($this->lockFile)) {
            @unlink($this->lockFile);
        }
    }

    /**
     * Absolute path (with trailing slash) of the directory holding the lock file.
     * Exposed so callers (backend module) can locate the same lock without duplicating logic.
     */
    public static function getLockFilePath(): string
    {
        $lockFilePath = Environment::isComposerMode()
            ? Environment::getProjectPath() . '/var/lock/'
            : 'typo3temp/var/locks/';
        return GeneralUtility::getFileAbsFileName($lockFilePath);
    }

    /**
     * Absolute path of the lock file itself.
     */
    public static function getLockFile(): string
    {
        return self::getLockFilePath() . self::LOCK_FILENAME;
    }

    /**
     * Create or delete the indexing lock file.
     *
     * @throws IndexException on a lock that is younger than 10 minutes, or on I/O errors
     */
    private function handleLockFile(string $mode): void
    {
        // No locking in Development context — keeps devs from being blocked by a stale lock.
        if (Environment::getContext()->isDevelopment()) {
            return;
        }

        $lockFilePath = self::getLockFilePath();
        if (!is_dir($lockFilePath)) {
            GeneralUtility::mkdir_deep($lockFilePath);
        }
        $this->lockFile = $lockFilePath . self::LOCK_FILENAME;

        switch ($mode) {
            case 'create':
                if (file_exists($this->lockFile)) {
                    $tenMinutesAgo = (new \DateTime('-10 minutes'))->getTimestamp();
                    if (filemtime($this->lockFile) > $tenMinutesAgo) {
                        throw new IndexException(
                            'updateIndex: Indexing is currently running or an error has occurred. Please try again later or ' . $lockFilePath
                        );
                    }
                    // Stale lock — reset it.
                    $this->handleLockFile('delete');
                    $this->handleLockFile('create');
                    return;
                }
                if (!touch($this->lockFile)) {
                    throw new IndexException('updateIndex: Error touching lock file ' . $lockFilePath);
                }
                // Last-resort cleanup: released on any script termination
                // (uncaught fatal, SIGTERM from FPM, etc.) that skips finally.
                $lockFileForShutdown = $this->lockFile;
                register_shutdown_function(static function () use ($lockFileForShutdown): void {
                    if (file_exists($lockFileForShutdown)) {
                        @unlink($lockFileForShutdown);
                    }
                });
                break;

            case 'delete':
                if (file_exists($this->lockFile) && !unlink($this->lockFile)) {
                    throw new IndexException('updateIndex: Error unlinking lock file ' . $lockFilePath);
                }
                break;
        }
    }
}
