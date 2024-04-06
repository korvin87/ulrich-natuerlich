<?php
/*
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Abavo\AbavoSearch\Domain\Repository\IndexerRepository;
use Abavo\AbavoSearch\Domain\Repository\IndexRepository;
use Abavo\AbavoSearch\Domain\Repository\StatRepository;
use Abavo\AbavoSearch\User\AjaxResponse;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use Abavo\AbavoSearch\User\Diagnose;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\AbavoSearch\User\GeneralUtilityHelper;
use Abavo\AbavoSearch\Domain\Exception\IndexException;

/**
 * BackendController
 *
 * @author mbruckmoser
 */
class BackendController extends ActionController
{
    /**
     * indexerRepository
     *
     * @var IndexerRepository
     */
    protected $indexerRepository;

    /**
     * indexRepository
     *
     * @var IndexRepository
     */
    protected $indexRepository;

    /**
     * statRepository
     *
     * @var StatRepository
     */
    protected $statRepository;

    /**
     * indexCommandController
     *
     * @var IndexCommandController
     */
    protected $indexCommandController;

    /**
     *  AJAX responseHelper
     *
     * @var AjaxResponse
     */
    protected $ajaxResponseHelper;

    /**
     *  TypoScript settings
     *
     * @var array
     */
    protected $settings = [];

    /**
     * info of selected page
     *
     * @var array
     */
    protected $pageInfo = [];

    /**
     * ConnectionPool
     *
     * @var ConnectionPool
     */
    protected $connectionPool = null;

    /**
     * $indexingProgress
     *
     * @var array
     */
    protected $indexingProgress = ['active' => false];

    protected function initializeAction()
    {
        $this->pageInfo = BackendUtility::readPageAccess((int) GeneralUtility::_GP('id'), $GLOBALS['BE_USER']->getPagePermsClause(1));

        $configurationManager      = GeneralUtility::makeInstance(BackendConfigurationManager::class);
        $this->settings            = $configurationManager->getConfiguration($this->request->getControllerExtensionName(), $this->request->getPluginName());
        $this->settings['extConf'] = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($this->request->getControllerExtensionKey());
    }

    /**
     * action overview
     *
     * @return void
     */
    public function overviewAction()
    {

        // Is indexer current runing?
        $indexingProgress = $this->getIndexingProgressState($this->pageInfo['uid']);

        // Find all indexer with count
        $indexers = [];
        foreach ($this->indexerRepository->findByPid($this->pageInfo['uid']) as $indexer) {
            $indexers[] = [
                'obj' => $indexer,
                'count' => $this->indexRepository->countByIndexer($indexer)
            ];
        }

        // Get storage pages
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $queryResult  = $queryBuilder->select('uid')
            ->from('pages')
            ->where($queryBuilder->expr()->eq('module', $queryBuilder->createNamedParameter('abavo_search')))
            ->execute();

        $searchStoragesLinks = [];
        if ($queryResult->rowCount()) {
            foreach ($queryResult->fetchAll() as $page) {
                $searchStoragesLinks[$page['uid']] = [
                    'uid' => $page['uid'],
                    'title' => $page['title'],
                    'uri' => $this->uriBuilder->setArguments(['id' => (int) $page['uid']])->build(),
                    'isCurrentPage' => ((int) $page['uid'] === (int) $this->pageInfo['uid'])
                ];
            }
        }

        // Is a page selected?
        if ((!(boolean) $this->pageInfo['uid'] || !array_key_exists($this->pageInfo['uid'], $searchStoragesLinks)) && count($searchStoragesLinks)) {
            $this->redirectToUri(current($searchStoragesLinks)['uri']);
        }

        // Assign data to view
        $this->view->assignMultiple([
            'searchStoragesLinks' => $searchStoragesLinks,
            'beUser' => $GLOBALS['BE_USER']->user,
            'pageInfo' => $this->pageInfo,
            'indexers' => $indexers,
            'indexingProgress' => $indexingProgress
        ]);
    }

    /**
     * action diagnosis
     *
     * @return void
     */
    public function diagnosisAction(): ResponseInterface
    {
        /*
         * Get statuses
         */
        $statuses = GeneralUtility::makeInstance(Diagnose::class)->getStatus();
        foreach ($statuses as $status) {
            $title = ($status->getValue()) ? $status->getTitle().': '.$status->getValue() : $status->getTitle();
            $this->addFlashMessage($status->getMessage(), $title, $status->getSeverity());
        }

        /*
         * Last Errors from logfile
         */
        $errorLog = [];
        $logFile  = GeneralUtility::getFileAbsFileName('uploads/tx_abavosearch/Log/IndexCommandController.log');
        if (file_exists($logFile)) {
            $handleLogFile = fopen($logFile, 'r');
            if (file_exists($logFile)) {
                while (!feof($handleLogFile)) {
                    $parseLine = fgets($handleLogFile);
                    if ((boolean) strpos($parseLine, ' [ERROR] ')) {

                        $tempTime = explode(' [ERROR] ', $parseLine)[0];

                        $tempDetail = '';
                        if (!empty($tempTime)) {
                            $tempFullInfo    = explode('component="Abavo.AbavoSearch.Controller.IndexCommandController": ', $parseLine)[1];
                            $arrTempFullInfo = explode(' - ', $tempFullInfo);
                            $tempMessage     = $arrTempFullInfo[0];
                            $tempTrace       = $arrTempFullInfo[1];
                        }
                        array_push($errorLog, array_combine(['time', 'message', 'trace'], [$tempTime, $tempMessage, $tempTrace]));
                    }
                }
                fclose($handleLogFile);
                $errorLog = [
                    'allCount' => count($errorLog),
                    'items' => array_slice(array_reverse($errorLog), 0, 5)// Get only 5 last errors
                ];
            }
        }


        /*
         *  Assign data to view
         */
        $this->view->assignMultiple([
            'beUser' => $GLOBALS['BE_USER']->user,
            'errorLog' => $errorLog
        ]);
        return $this->htmlResponse();
    }

    /**
     * action updateindex
     *
     * @return void
     */
    public function updateindexAction(): ResponseInterface
    {
        try {
            // Is a page selected?
            if (!(boolean) $this->pageInfo['uid']) {
                throw new IndexException('No page selected. Please choose a page from tree first');
            }

            $indexResults = $this->indexCommandController->updateCommand($this->pageInfo['uid']);
            if (empty($indexResults)) {
                throw new IndexException('No index results.');
            }
        } catch (\Exception $e) {
            $this->addFlashMessage($e->getMessage(), 'Exception in updateindexAction', AbstractMessage::ERROR);
        }


        // Assign data to view
        $this->view->assignMultiple([
            'beUser' => $GLOBALS['BE_USER']->user,
            'pageInfo' => $this->pageInfo,
            'indexResults' => $indexResults
        ]);
        return $this->htmlResponse();
    }

    /**
     * action indexingstate
     *
     * @return void
     */
    public function indexingstateAction(): ResponseInterface
    {
        // Assign data to view
        $this->view = $this->ajaxResponseHelper->getJsonView($this->controllerContext, $this->getIndexingProgressState());
        return $this->htmlResponse();
    }

    /**
     * action indexingProgressState
     *
     * @return void
     */
    private function getIndexingProgressState($pid = 0)
    {
        // Get IndexingInfos
        $lockFilePath = 'typo3temp/var/locks/';
        if (version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '9.5', '>=') && Environment::isComposerMode()) {
            $lockFilePath = Environment::getProjectPath().'/var/lock/';
        }
        $lockFile = GeneralUtility::getFileAbsFileName($lockFilePath.IndexCommandController::LOCK_FILENAME);
        if (file_exists($lockFile)) {
            $this->indexingProgress = [
                'active' => true,
                'timestamp' => filemtime($lockFile)
            ];
        }

        // Get youngest index
        $this->indexingProgress['youngestIndex'] = $this->indexRepository->findYoungestIndex($pid);

        return $this->indexingProgress;
    }

    /**
     * action cleanupAction
     *
     * @return void
     */
    public function cleanupAction(): ResponseInterface
    {
        $formcheck = false;

        // Admin-Check
        if (!(boolean) $GLOBALS['BE_USER']->user['admin']) {
            die('Access denied.');
        }

        // Truncate search index?
        if ((boolean) $this->request->hasArgument('tx_abavosearch_domain_model_index') && (boolean) $this->request->getArgument('tx_abavosearch_domain_model_index')) {
            $this->connectionPool->getConnectionForTable('tx_abavosearch_domain_model_index')->truncate('tx_abavosearch_domain_model_index');
            $this->connectionPool->getQueryBuilderForTable('sys_file_metadata')->update('sys_file_metadata')->set('tx_abavosearch_indexer', null)->set('tx_abavosearch_index_tstamp', null)->execute();
            $this->addFlashMessage('Table tx_abavosearch_domain_model_index was truncated and columns of EXT:abavo_search in sys_file_metadata reseted.', 'Table truncate', AbstractMessage::OK);
            $formcheck = true;
        }

        // Truncate autocompete terms?
        if ((boolean) $this->request->hasArgument('tx_abavosearch_domain_model_term') && (boolean) $this->request->getArgument('tx_abavosearch_domain_model_term')) {
            $this->connectionPool->getConnectionForTable('tx_abavosearch_domain_model_term')->truncate('tx_abavosearch_domain_model_term');
            $this->addFlashMessage('Table tx_abavosearch_domain_model_term was truncated', 'Table truncate', AbstractMessage::OK);
            $formcheck = true;
        }

        // Remove lockFile?
        $lockFilePath = 'typo3temp/var/locks/';
        if (version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '9.5', '>=') && Environment::isComposerMode()) {
            $lockFilePath = Environment::getProjectPath().'/var/lock/';
        }
        $lockFile = GeneralUtility::getFileAbsFileName($lockFilePath.IndexCommandController::LOCK_FILENAME);
        if ((boolean) $this->request->hasArgument('lockFile') && (boolean) $this->request->getArgument('lockFile')) {
            unlink($lockFile);
            $formcheck = true;
        }
        $lockFileExist = file_exists($lockFile);

        // Remove logFile?
        $logFile = GeneralUtility::getFileAbsFileName(current(current($GLOBALS['TYPO3_CONF_VARS']['LOG']['Abavo']['AbavoSearch']['Controller']['IndexCommandController']['writerConfiguration']))['logFile']);
        if ((boolean) $this->request->hasArgument('logFile') && (boolean) $this->request->getArgument('logFile')) {
            unlink($logFile);
            $formcheck = true;
        }
        if (file_exists($logFile)) {
            $logFileSize = GeneralUtilityHelper::FileSizeConvert(filesize($logFile));
        } else {
            $logFileSize = 0;
        }

        // Set shell script permissions
        if ((boolean) $this->request->hasArgument('setShellScriptsPermissions') && (boolean) $this->request->getArgument('setShellScriptsPermissions') && $this->request->hasArgument('setShellScriptsPermissionsValue')) {
            $shellScripts = GeneralUtility::getFilesInDir(GeneralUtility::getFileAbsFileName('EXT:abavo_search/Resources/Private/Scripts/'), 'sh', true);

            $chmod = (int) $this->request->getArgument('setShellScriptsPermissionsValue');
            try {
                foreach ($shellScripts as $shellScript) {
                    chmod($shellScript, $chmod);
                }
            } catch (\Exception $ex) {
                $this->addFlashMessage($ex->getMessage(), 'Set shell script permissions', AbstractMessage::ERROR);
            }
        }


        // Nothing selected
        if (!$formcheck) {
            $this->addFlashMessage('Nothing was selected. Please choose an option.', 'abavo_search CleanUp', AbstractMessage::INFO);
        }

        // Assign data to view
        $this->view->assignMultiple([
            'beUser' => $GLOBALS['BE_USER']->user,
            'logFileSize' => $logFileSize,
            'lockFileExist' => $lockFileExist
        ]);
        return $this->htmlResponse();
    }

    /**
     * action statsAction
     *
     * @return void
     */
    public function statsAction(): ResponseInterface
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_language');
        $queryResult  = $queryBuilder->select('*')
            ->from('sys_language')
            ->add('orderBy', 'title ASC')
            ->execute();

        $languages = [];
        while ($row       = $queryResult->fetch()) {
            $languages[] = (object) [
                    'title' => htmlspecialchars($row['title']),
                    'uid' => htmlspecialchars($row['uid']),
                    'flag' => htmlspecialchars($row['flag'])
            ];
        }


        // Assign data to view
        $this->view->assignMultiple([
            'beUser' => $GLOBALS['BE_USER']->user,
            'stats' => $stats,
            'pid' => $this->pageInfo['uid'],
            'languages' => $languages,
            'T3Compatibility' => (VersionNumberUtility::convertVersionNumberToInteger(GeneralUtility::makeInstance(Typo3Version::class)->getVersion()) < 7_002_000)
        ]);
        return $this->htmlResponse();
    }

    /**
     * action DataTables
     *
     * @return void
     */
    public function datatableAction(): ResponseInterface
    {
        // Get Arguments from jQuery Datatable
        $arguments = array_merge($this->request->getArguments(), GeneralUtility::_GET());

        // Create search filter for fields
        $arrSearch      = [];
        $fullSearchWord = $arguments['search']['value'];

        if ($fullSearchWord != '') {
            $arrSearch['val'] = '%'.$arguments['search']['value'].'%';
        }

        $arrFilter = [];
        if (array_key_exists('columns', $arguments)) {
            foreach ($arguments['columns'] as $column) {
                if ($column['search']['value'] != '') {
                    $arrFilter[$column['data']] = $column['search']['value'];
                }
            }
        }

        // Count total records
        $recordsTotal = $this->statRepository->countAll();

        // Record ordering
        $queryOrderBy = '';
        if (array_key_exists('order', $arguments) && !empty($arguments['order'])) {
            $i = 1;
            foreach ($arguments['order'] as $key => $ordering) {
                $queryOrderBy .= $arguments['columns'][$arguments['order'][$key]['column']]['data'].' '.$arguments['order'][$key]['dir'];
                $queryOrderBy .= ($i < (is_countable($arguments['order']) ? count($arguments['order']) : 0)) ? ',' : '';
                $i++;
            }
        }

        // Get records
        $filteredStats = $this->statRepository->findAllForDataTables($arguments['start'], $arguments['length'], $arguments['id'], $arrSearch, $arrFilter, $queryOrderBy);
        $stats         = $filteredStats['records'];

        // Make data array
        $data = [
            'draw' => (int) $arguments['draw'],
            'recordsTotal' => (int) $recordsTotal,
            'recordsFiltered' => (!empty($arrSearch) || !empty($arrFilter)) ? $filteredStats['recordsCount'] : $recordsTotal,
            'data' => $stats
        ];


        // Assign data to view
        $this->view = $this->ajaxResponseHelper->getJsonView($this->controllerContext, $data);
        return $this->htmlResponse();
    }

    public function injectIndexerRepository(IndexerRepository $indexerRepository): void
    {
        $this->indexerRepository = $indexerRepository;
    }

    public function injectIndexRepository(IndexRepository $indexRepository): void
    {
        $this->indexRepository = $indexRepository;
    }

    public function injectStatRepository(StatRepository $statRepository): void
    {
        $this->statRepository = $statRepository;
    }

    public function injectIndexCommandController(IndexCommandController $indexCommandController): void
    {
        $this->indexCommandController = $indexCommandController;
    }

    public function injectAjaxResponseHelper(AjaxResponse $ajaxResponseHelper): void
    {
        $this->ajaxResponseHelper = $ajaxResponseHelper;
    }

    public function injectConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
    }
}