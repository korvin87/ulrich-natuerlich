<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Indexers;

use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Abavo\AbavoSearch\Domain\Repository\IndexRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Domain\Model\Index;

/**
 * FalIndexer
 *
 * @author mbruckmoser
 */
class FalIndexer extends BaseIndexer
{
    public CONST SERVER_TOOL_PATHS = '/usr/bin/,/usr/local/bin/';
    public CONST LOCAL_TEMP_PATH   = 'typo3temp/tx_abavosearch/';
    public CONST DO_CLEAN_INDEX    = false;

    /**
     * fileExtension to script mapping
     *
     * @var array
     */
    protected $fileExtensionHandlingMapping = [];

    /**
     * @var ResourceStorage
     */
    protected $storage;

    /**
     * ConnectionPool
     *
     * @var ConnectionPool
     */
    protected $connectionPool = null;

    /**
     * @var IndexRepository
     */
    protected $indexRepository;

    /**
     * The constructor
     * 
     * @param array $settings
     * @throws IndexException
     */
    function __construct($settings)
    {

        /*
         * Set Attributes, because seems in SingletonInterface @inject doesnt work
         * because of issue https://forge.typo3.org/issues/48544
         */
        $this->settings        = $settings;
        $this->storage         = GeneralUtility::makeInstance(ResourceFactory::class)->getStorageObject($this->settings['falStorage']);
        $this->connectionPool  = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->indexRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(IndexRepository::class);

        // Prevent timeout?
        if (array_key_exists('preventServerTimeout', $settings) && (boolean) $settings['preventServerTimeout'] && $this->storage->getDriverType() !== 'Local') {
            set_time_limit(0);
        }

        // Define FileExtensionHandling
        if ($this->settings['fileExtensionHandlingMapping'] != '') {

            $xmlfile = GeneralUtility::getFileAbsFileName($this->settings['fileExtensionHandlingMapping']);
            if (!file_exists($xmlfile)) {
                throw new IndexException('FalIndexer\Config: No XML configuration given.');
            }
            $xmlstr = file_get_contents($xmlfile);
            $xml    = new \SimpleXMLElement($xmlstr);

            foreach ($xml->mappings->mapping as $mapping) {

                $this->fileExtensionHandlingMapping[(string) $mapping['id']] = [
                    'script' => isset($mapping->script) ? (string) GeneralUtility::getFileAbsFileName($mapping->script) : null,
                    'tools' => isset($mapping->tools) ? GeneralUtility::trimExplode(',', (string) $mapping->tools) : null,
                    'cleanRegex' => isset($mapping->cleanRegex) ? [
                    'pattern' => isset($mapping->cleanRegex->pattern) ? (string) $mapping->cleanRegex->pattern : null,
                    'replace' => isset($mapping->cleanRegex->replace) ? (string) $mapping->cleanRegex->replace : null,
                    ] : null
                ];
            }
        }
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $timeStart   = microtime(true);
        $files       = [];
        $validRefIds = [];

        if (!$indexer) {
            throw new IndexException('FalIndexer\getData: No indexer given.');
        }

        // Prepare repository
        $this->indexRepository->setDefaultQuerySettings($this->indexRepository->createQuery()->getQuerySettings()->setStoragePageIds([$indexer->getStoragepid()]));

        $directorys        = GeneralUtility::trimExplode(',', $this->settings['paths']);
        $directoryExcludes = GeneralUtility::trimExplode(',', $this->settings['excludepaths']);
        if (empty($directorys)) {
            throw new IndexException('FalIndexer\getData: No directory given.');
        }

        // Define server tool paths
        $serverToolExecutablePaths = GeneralUtility::trimExplode(',', self::SERVER_TOOL_PATHS);
        if (!in_array($this->settings['toolPath'], $serverToolExecutablePaths)) {
            array_push($serverToolExecutablePaths, $this->settings['toolPath']);
        }

        // Get all files from directorys by file extension
        $this->getFilesFromFal($files, $directorys, $directoryExcludes, $this->settings['fileExtensions']);

        foreach ($files as $file) {
            $localTempFileName = null;

            // Execute parse script by file extension
            if (!array_key_exists($file->getExtension(), $this->fileExtensionHandlingMapping)) {
                throw new IndexException('FalIndexer\getData: File extension "'.$file->getExtension().'" not supported.');
            }

            $fileContents = [];
            $absFileName  = GeneralUtility::getFileAbsFileName(urldecode($file->getPublicUrl()));

            // Escape naw_securedl interceptor
            if (ExtensionManagementUtility::isLoaded('naw_securedl') === true) {
                $fileNameArray = explode('&file=', $absFileName);

                if (count($fileNameArray) === 2) {
                    $absFileName = GeneralUtility::getFileAbsFileName($fileNameArray[1]);
                }
            }

            // Append to valid refids
            $refid         = 'sys_file'.self::REFID_SEPERARTOR.$file->getProperty('uid');
            $validRefIds[] = $refid;

            // Is file modified or indexed in the past?
            $fileMetaData = $file->_getMetaData();
            if ($file->getModificationTime() < (int) $fileMetaData['tx_abavosearch_index_tstamp']) {
                continue;
            }

            /**
             * Supporting remote drivers like Amazon S3
             * Strategy: If index does not exists oder modified in the past, we transfer the file to the temporary local folder for indexing and remove them after process.
             */
            if ($this->storage->getDriverType() !== 'Local') {
                try {
                    // Write file content to local temp folder
                    if (file_exists($localTempFileName = GeneralUtility::getFileAbsFileName(self::LOCAL_TEMP_PATH.md5($file->getCombinedIdentifier().'|'.$file->getCreationTime()).'_'.$file->getSha1().'.'.$file->getExtension()))) {
                        unlink($localTempFileName);
                    }

                    if (!file_exists($localTempFileName)) {
                        $fileContents = $file->getContents();
                        if (file_put_contents($localTempFileName, $fileContents)) {
                            touch($localTempFileName, $file->getModificationTime());
                        } else {
                            throw new \Exception('Error while writing file '.$localTempFileName);
                        }
                    }

                    // Was file correct transfered?
                    if (file_exists($localTempFileName) && sha1(file_get_contents($localTempFileName)) === sha1($fileContents)) {
                        $absFileName = $localTempFileName;
                    } else {
                        throw new \Exception('Tranfer of file to '.$localTempFileName.' was failed');
                    }
                } catch (\Exception $ex) {
                    DebugUtility::debug($ex->getTraceAsString(), $ex->getMessage());
                    return;
                }
            }

            // Validate file
            if (!file_exists($absFileName)) {
                throw new IndexException('FalIndexer\getData: File does not exist:'.$file->getPublicUrl());
            }
            $script = $this->fileExtensionHandlingMapping[$file->getExtension()]['script'];
            if (!file_exists($script)) {
                throw new IndexException('FalIndexer\getData: Script file does not exist:'.$script);
            }

            // is server tool executable?
            $serverToolExecutablePath = null;

            if ($tools = $this->fileExtensionHandlingMapping[$file->getExtension()]['tools']) {

                foreach ($tools as $tool) {
                    foreach ($serverToolExecutablePaths as $path) {

                        if (is_executable($path.$tool) === true) {
                            $serverToolExecutablePath = $path;
                            break;
                        }
                    }
                }
            }

            if ($serverToolExecutablePath === null) {
                throw new IndexException('FalIndexer\getData: Server tools not set in FalIndexFileExtensionHandling.xml or in "'.implode(',', $serverToolExecutablePaths).'" not executable.');
            }

            // Execute command
            $command = 'bash '.$script.' '.escapeshellarg($serverToolExecutablePath).' '.escapeshellarg($absFileName);
            CommandUtility::exec($command, $fileContents, $commandCode);

            // Remove temporary file
            if ($localTempFileName && file_exists($localTempFileName)) {
                unlink($localTempFileName);
            }

            // Read content
            $content = '';
            foreach ($fileContents as $line) {
                trim($line);
                if ($line != '') {
                    $content .= trim($line)."\n";
                }
            }

            // Cleanup content
            $pattern = $this->fileExtensionHandlingMapping[$file->getExtension()]['cleanRegex']['pattern'];
            $replace = $this->fileExtensionHandlingMapping[$file->getExtension()]['cleanRegex']['replace'];
            $content = ($pattern && $replace) ? trim(preg_replace($pattern, $replace, $content)) : $content;

            // Make Index Object
            if (!empty($fileContents)) {

                // Set Title
                $title = ($file->hasProperty('title')) ? $file->getProperty('title') : '';
                if ($title == '') {
                    $title = str_replace(['-', '_'], ' ', $file->getNameWithoutExtension());
                    $title = preg_replace("/[^a-zßäöüÄÖÜA-Z0-9 ]/", "", $title);
                }

                // Index-Modify-Hook
                $abstract = $file->getProperty('description');
                $this->modifyIndexHook('Fal', $file, $title, $content, $abstract);

                // Set Index properties
                $isIndexNew = false;
                if (!($tempIndex  = $this->indexRepository->findOneByTarget($file->getCombinedIdentifier())) instanceof Index) {
                    $isIndexNew = true;
                    $tempIndex  = Index::getInstance();
                }

                $tempIndex->setTitle(strip_tags($title));
                $tempIndex->setContent(preg_replace('!\s+!', ' ', strip_tags($content)));
                $tempIndex->setAbstract(preg_replace('!\s+!', ' ', strip_tags($abstract)));
                $tempIndex->setTarget($file->getCombinedIdentifier()); //<f:link.page pageUid="{f:uri.image(src:file,treatIdAsReference:1)}" target="_blank" />
                $tempIndex->setFegroup($this->settings['feGroup'] ?: self::FE_GROUP_DEFAULT);
                $tempIndex->setSysLanguageUid((int) $this->settings['language']);
                $tempIndex->setRefid($refid);

                //Set additional fields
                $this->setAdditionalFields($tempIndex, $indexer);

                // Set metadata
                $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_file_metadata');
                $queryBuilder->update('sys_file_metadata')
                    ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($fileMetaData['uid'], \PDO::PARAM_INT)))
                    ->set('tx_abavosearch_indexer', $indexer->getUid())
                    ->set('tx_abavosearch_index_tstamp', time())
                    ->execute();

                // Add index to list
                if ($isIndexNew) {
                    $this->data[] = $tempIndex;
                } else {
                    // Update stored index
                    $this->indexRepository->update($tempIndex);
                }
            }
        }

        // Remove unused index
        $this->indexRepository->removeByValidRefids($validRefIds, $indexer->getUid());

        // Set duration time
        $this->duration[$indexer->getUid()] = (microtime(true) - $timeStart);

        return $this->data;
    }

    /**
     * fetches files recurively using FAL
     *
     * @param array $files
     * @param array $directorys
     * @param array $directoryExcludes
     * @param array $fileExtensions
     */
    private function getFilesFromFal(&$files, $directorys = [], $directoryExcludes = [], $fileExtensions = '')
    {

        $directoryExcludesList = implode(',', $directoryExcludes);

        foreach ($directorys as $directory) {

            $folder = $this->storage->getFolder($directory);

            if ($folder->getName() != '_temp_' && $this->storage->isPublic()) {
                $filesInFolder = $folder->getFiles();

                if (is_countable($filesInFolder) ? count($filesInFolder) : 0) {
                    foreach ($filesInFolder as $file) {
                        if (GeneralUtility::inList($fileExtensions, $file->getExtension())) {
                            $files[] = $file;
                        }
                    }
                }

                // do recursion
                $subfolders = $folder->getSubFolders();

                if (is_countable($subfolders) ? count($subfolders) : 0) {
                    foreach ($subfolders as $subfolder) {

                        // Exlude $directoryExcludes from index
                        if (GeneralUtility::inList($directoryExcludesList, $subfolder->getIdentifier()) != true) {
                            $this->getFilesFromFal($files, [$subfolder->getIdentifier()], $directoryExcludes, $fileExtensions);
                        }
                    }
                }
            }
        }
    }
}