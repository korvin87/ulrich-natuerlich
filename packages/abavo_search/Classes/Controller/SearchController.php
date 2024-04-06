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
use Abavo\AbavoSearch\Domain\Repository\TermRepository;
use Abavo\AbavoSearch\Domain\Repository\TtcontentRepository;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use Abavo\AbavoSearch\Domain\Repository\IndexerRepository;
use Abavo\AbavoSearch\User\SessionHandler;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use Abavo\AbavoSearch\User\SearchUtility;
use Abavo\AbavoSearch\Domain\TermManager;
use Abavo\AbavoSearch\Domain\StatManager;
use Abavo\AbavoSearch\Domain\SynonymManager;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use Abavo\AbavoSearch\Domain\Service\SearchService;
use Abavo\AbavoSearch\Domain\StopWord;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\User\GeneralUtilityHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;

/**
 * SearchController
 *
 * @author mbruckmoser
 */
class SearchController extends ActionController
{
    /**
     * termRepository
     *
     * @var TermRepository
     */
    protected $termRepository;

    /**
     * ttcontentRepository
     *
     * @var TtcontentRepository
     */
    protected $ttcontentRepository;

    /**
     * categoryRepository
     *
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * indexerRepository
     *
     * @var IndexerRepository
     */
    protected $indexerRepository;

    /**
     * sessionHandler
     *
     * @var SessionHandler
     */
    protected $sessionHandler;

    /**
     * FE User Repository
     *
     * @var FrontendUserRepository
     */
    protected $feuserRepository;

    /**
     * FE Group Repository
     *
     * @var FrontendUserGroupRepository
     */
    protected $fegroupRepository;

    /**
     * SearchUtility
     *
     * @var SearchUtility
     */
    protected $searchUtility;

    /**
     * stopWord
     *
     * @var StopWord
     */
    protected $stopWord;

    /**
     * Term manager
     *
     * @var TermManager
     */
    protected $termManager;

    /**
     * stat manager
     *
     * @var StatManager
     */
    protected $statManager;

    /**
     * Synonym manager
     *
     * @var SynonymManager
     */
    protected $synonymManager;

    /**
     * EXTKEY
     *
     * @var string
     */
    protected $extKey = '';

    public function initializeAction()
    {
        parent::initializeAction();
        $this->extKey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName());

        // Define settings
        $this->settings['piData']               = $this->configurationManager->getContentObject()->data;
        $this->settings['controllerActionName'] = $this->request->getControllerActionName();
        $relevantParametersForCachingFromPageArguments = [];
        $pageArguments = $GLOBALS['REQUEST']->getAttribute('routing');
        $queryParams = $pageArguments->getDynamicArguments();
        if (!empty($queryParams) && ($pageArguments->getArguments()['cHash'] ?? false)) {
            $queryParams['id'] = $pageArguments->getPageId();
            $relevantParametersForCachingFromPageArguments = GeneralUtility::makeInstance(CacheHashCalculator::class)->getRelevantParameters(HttpUtility::buildQueryString($queryParams));
        }
        $this->settings['sysLanguageUid'] = $relevantParametersForCachingFromPageArguments;
        $this->settings['storagePids']          = '';

        /*
         * Set storage pids in following order
         *
         * 1. TS-Setup persistence.storagePid
         * 2. Plugin['pages']
         * 3. CurrentPid
         */
        $pids = '';
        $conf = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, $this->request->getControllerExtensionName());
        if (array_key_exists('persistence', $conf) && array_key_exists('storagePid', $conf['persistence']) && (boolean) $conf['persistence']['storagePid']) {
            $pids = $conf['persistence']['storagePid'];
        }

        // Get plug-in´s pages
        $pluginData = $this->settings['piData'];
        if (is_array($pluginData) && array_key_exists('pages', $pluginData) && (boolean) $pluginData['pages']) {
            $pids = $pluginData['pages'];
        }

        // CurrentPid if no pids (fallback if nothing is set)
        if (empty($pids)) {
            $pids = (int) $GLOBALS['TSFE']->id;
        }

        // Finaly set storage pids for repositories/settings
        if ((boolean) trim($pids)) {
            $this->settings['storagePids'] = $pids;
            $this->categoryRepository->setDefaultQuerySettings($this->categoryRepository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $pids)));
            $this->indexerRepository->setDefaultQuerySettings($this->indexerRepository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $pids)));
            $this->termRepository->setDefaultQuerySettings($this->termRepository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $pids)));
            $this->ttcontentRepository->setDefaultQuerySettings($this->ttcontentRepository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $pids)));
        }
    }

    /**
     * action searchform
     *
     * @return void
     */
    public function formAction(): ResponseInterface
    {
        $formFacets            = $orderby               = $sorting               = $length                = [];
        $orderbyPreSelection   = false;
        $sortingByPreSelection = false;
        $lengthByPreSelection  = false;
        $search                = '';

        try {
            /*
             *  Cleanup _GPmerged
             *  Using this way because we cant get by $this->request from other plugin's arguments
             */
            $_GPvars = GeneralUtilityHelper::removeXSSRecursive(GeneralUtility::_GPmerged('tx_abavosearch_piresults'));

            if (is_array($_GPvars)) {

                // search
                if (array_key_exists('search', $_GPvars)) {
                    $search = htmlspecialchars(strip_tags(trim($_GPvars['search'])), ENT_QUOTES, 'UTF-8');
                    $search = GeneralUtilityHelper::truncateStringRespectWord($search, $this->settings['maxSearchStrinLen']);
                }

                // facets
                if (array_key_exists('facets', $_GPvars)) {
                    $allPostFacets = $_GPvars['facets'];
                    foreach ($allPostFacets as $key => $postFacets) {
                        foreach ($postFacets as $tempFacet) {
                            $formFacets[$key][] = $tempFacet;
                        }
                    }
                }

                // orderby
                if (array_key_exists('orderby', $_GPvars)) {
                    $orderbyPreSelection = trim($_GPvars['orderby']);
                }

                // sorting
                if (array_key_exists('sorting', $_GPvars)) {
                    $sortingByPreSelection = trim($_GPvars['sorting']);
                }

                // length
                if (array_key_exists('length', $_GPvars)) {
                    $lengthByPreSelection = (int) trim($_GPvars['length']);
                }
            }

            /*
             *  Get searchword from session or use $_GPvars
             */
            $sessonSavedSearch = $this->sessionHandler->restoreFromSession('search')->lastSearchWord;
            if ((boolean) $search === false && (boolean) $sessonSavedSearch === true) {
                $search = $sessonSavedSearch;
            }

            /*
             *  Make advanced search form
             */
            if ($this->settings['advancedForm']) {
                $searchServices = $this->searchUtility->getRegisteredSearchServicesAsFacet();
                $facets         = [
                    'categories' => [],
                    'indexers' => [],
                    'searchService' => [
                        'items' => $searchServices,
                        'multiServiceSearch' => (boolean) $searchServices
                    ]
                ];

                // Types
                foreach ($this->indexerRepository->findAll() as $indexer) {
                    if (!array_key_exists($indexer->getType(), $facets['indexers'])) {
                        array_push($facets['indexers'], $indexer);
                    }
                }

                // Categories
                foreach ($this->categoryRepository->findByParent($this->settings['rootFacetCategory']) as $category) {
                    $categoryChild = $this->categoryRepository->findByParent($category)->toArray();
                    if ($categoryChild) {
                        $facets['categories'][$category->getTitle()] = $categoryChild;
                    }
                }
            }

            /*
             *  OrderBy
             */
            $orderByOptions = [];
            if ($this->settings['advancedFormOrderByFields']) {
                foreach (GeneralUtility::trimExplode(',', $this->settings['advancedFormOrderByFields']) as $optionField) {
                    $orderByOptions[$optionField] = LocalizationUtility::translate('advancedForm.orderby.'.$optionField, $this->extKey);
                }
            }
            $orderby = (object) [
                    'selected' => $orderbyPreSelection,
                    'options' => $orderByOptions
            ];

            /*
             *  Sorting
             */
            $sorting = (object) [
                    'selected' => $sortingByPreSelection,
                    'options' => [
                        'desc' => LocalizationUtility::translate('advancedForm.sorting.desc', $this->extKey),
                        'asc' => LocalizationUtility::translate('advancedForm.sorting.asc', $this->extKey)
                    ]
            ];

            /*
             *  Length
             */
            $length = (object) [
                    'selected' => $lengthByPreSelection,
                    'options' => [10, 15, 30, 50, 100]
            ];
        } catch (\Exception $e) {
            $this->addFlashMessage($e->getMessage(), 'Exception in formAction', AbstractMessage::ERROR);
        }

        /*
         *  Assign data to view
         */
        $this->view->assignMultiple([
            'search' => $search,
            'piData' => $this->configurationManager->getContentObject()->data,
            'terms' => $this->termRepository->findAll(),
            'facets' => $facets,
            'formFacets' => $formFacets,
            'orderby' => $orderby,
            'sorting' => $sorting,
            'length' => $length
        ]);
        return $this->htmlResponse();
    }

    /**
     * action search
     *
     * @param string $search
     * @param int $page
     * @return void
     */
    public function searchAction($search = null, $page = 1): ResponseInterface
    {
        $queryResult         = $categories          = $facets              = $synonyms            = $synonymReplacements = [];
        $maxSearchStrinLen   = false;
        $stopWordMode        = false;
        $orderby             = 'ranking';
        $sorting             = 'desc';
        $length              = (int) $this->settings['itemsPerPage'];
        $l10nFile            = 'LLL:EXT:'.$this->extKey.'/Resources/Private/Language/messages.xlf';

        try {
            if ($search) {

                /*
                 *  Security cleanups
                 */
                $search = htmlspecialchars(strip_tags(trim($search)), ENT_QUOTES, 'UTF-8');
                if (strlen($search) > (int) $this->settings['maxSearchStrinLen']) {
                    $search = GeneralUtilityHelper::truncateStringRespectWord($search, $this->settings['maxSearchStrinLen']);
                    $this->addFlashMessage(LocalizationUtility::translate(
                            $l10nFile.':exception.maxSearchStrinLen', $this->extKey, [$this->settings['maxSearchStrinLen']]),
                        LocalizationUtility::translate($l10nFile.':exception', $this->extKey), AbstractMessage::INFO
                    );
                }

                // Get facets
                if ($this->request->hasArgument('facets')) {
                    foreach ($this->request->getArgument('facets') as $facetType => $formFacets) {
                        foreach ($formFacets as $tempFacet) {
                            $facets[$facetType][] = GeneralUtilityHelper::removeXSSRecursive($tempFacet);
                        }
                    }
                }

                // OrderBy
                if ($this->request->hasArgument('orderby')) {
                    $orderby = GeneralUtilityHelper::removeXSSRecursive($this->request->getArgument('orderby'));
                }

                // Sorting
                if ($this->request->hasArgument('sorting')) {
                    $sorting = GeneralUtilityHelper::removeXSSRecursive($this->request->getArgument('sorting'));
                }

                // length
                if ($this->request->hasArgument('length') && (int) $this->request->getArgument('length') <= 100) {
                    $length = GeneralUtilityHelper::removeXSSRecursive($this->request->getArgument('length'));
                }

                /*
                 *  Check search string len
                 */
                if (strlen($search) < ((int) $this->settings['minSearchStrinLen'])) {
                    throw new IndexException(LocalizationUtility::translate($l10nFile.':minsearchlen', $this->extKey, [(int) $this->settings['minSearchStrinLen']]));
                } else {
                    $words        = GeneralUtility::trimExplode(' ', $search, true);
                    $removedWords = [];
                    $originalLen  = strlen($search);

                    if (is_countable($words) ? count($words) : 0) {
                        foreach ($words as $key => $word) {
                            if (strlen($word) < ((int) $this->settings['minSearchStrinLen'])) {
                                unset($words[$key]);
                                array_push($removedWords, $word);
                            }
                        }
                        $search = implode(' ', $words);
                    }

                    if ($originalLen > strlen($search)) {
                        $this->addFlashMessage(
                            LocalizationUtility::translate($l10nFile.':exception.minSearchStrinLen', $this->extKey, [implode(', ', $removedWords), $this->settings['minSearchStrinLen']]),
                            LocalizationUtility::translate($l10nFile.':exception', $this->extKey), AbstractMessage::INFO
                        );
                    }
                }

                /*
                 *  Check search string for stop words
                 */
                if ($removedWords = $this->stopWord->checkSearchForStopWords($search, $this->settings['stopWordList'], $this->settings['whiteWordList'])) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate($l10nFile.':stopword', $this->extKey, [$removedWords]), LocalizationUtility::translate($l10nFile.':exception', $this->extKey),
                        AbstractMessage::INFO
                    );
                }

                /*
                 *  Save searchString to session
                 */
                $this->sessionHandler->writeToSession((object) ['lastSearchWord' => $search], 'search');

                /*
                 *  Get synonyms of search string and adding to them
                 */
                $searchWithoutSynonyms = $search;
                $this->synonymManager->addSynonymsToSearch($search, $synonyms, $synonymReplacements);
            }

            /*
             *  Set data request offset
             */
            $start = ($page > 1) ? ((int) (($page - 1) * $length)) : false;

            /*
             *  Get Categories of plugin/tt_content object
             */
            $piData = $this->ttcontentRepository->findByUid($this->configurationManager->getContentObject()->data['uid']);
            foreach ($piData->getCategories() as $category) {
                $categories[] = $category->getUid();
            }

            /*
             * Get recursive fe_groups
             */
            $feuserObj = $this->feuserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);

            if ($feuserObj) {
                $fegroupsObj = $feuserObj->getUsergroup();
                $fegroupsArr = $this->getRecursiveFegroups($fegroupsObj, []);
                $fegroups = implode(',', $fegroupsArr);
            } else {
                $fegroups = null;
            }

            /*
             *  Find results with search string
             */
            $searchConditions = [
                'search' => $search,
                'start' => $start,
                'length' => $length,
                'L' => $GLOBALS['TSFE']->sys_language_uid,
                'feGroup' => $fegroups,
                'categories' => $categories,
                'facets' => $facets,
                'orderBy' => $orderby,
                'sorting' => $sorting
            ];
            $queryResult      = $this->objectManager->get(SearchService::class, $this->settings, $searchConditions)->getSearchResults();

            /*
             *  Update Term-Listing and Stats
             */
            if ((boolean) $queryResult['allCount'] && $page === 1) {
                $this->termManager->updateTerms($search, $GLOBALS['TSFE']->fe_user->user['usergroup'], $queryResult['result']);
                if ((boolean) $this->settings['statsEnabled']) {
                    $this->statManager->updateStats($search, $GLOBALS['TSFE']->sys_language_uid);
                }
            }
        } catch (\Exception $e) {
            $this->addFlashMessage($e->getMessage(), LocalizationUtility::translate($l10nFile.':exception', $this->extKey), AbstractMessage::INFO);
        }


        /*
         *  Assign Data to view
         */
        $this->view->assignMultiple([
            'piData' => $piData,
            'search' => $search,
            'facets' => $facets,
            'orderby' => $orderby,
            'sorting' => $sorting,
            'searchWithoutSynonyms' => $searchWithoutSynonyms,
            'synonyms' => $synonyms,
            'synonymReplacements' => $synonymReplacements,
            'results' => $queryResult['result'],
            'iteration' => $queryResult['iteration'],
            'queryTime' => $queryResult['queryTime'],
            'searchMethod' => $queryResult['searchMethod'],
            'paginationSettings' => [
                'itemsPerPage' => $length,
                'allCount' => $queryResult['allCount'],
                'currentPage' => $page
            ]
        ]);
        return $this->htmlResponse();
    }

    /**
     * get recursive fegroups of current feuser
     *
     * @param object $fegroupsObj
     * @param array $fegroupsArr
     * @return array
     */
    protected function getRecursiveFegroups($fegroupsObj, $fegroupsArr)
    {
        foreach ($fegroupsObj as $fegroup) {
            $fegroupUid = $fegroup->getUid();
            $fegroupObj = $this->fegroupRepository->findByUid($fegroupUid);
            $subgroupsObj = $fegroupObj->getSubgroup();

            array_push($fegroupsArr, $fegroupUid);

            if ($subgroupsObj) {
                $fegroupsArr = $this->getRecursiveFegroups($subgroupsObj, $fegroupsArr);
            }
        }

        return array_unique($fegroupsArr);
    }

    public function injectTermRepository(TermRepository $termRepository): void
    {
        $this->termRepository = $termRepository;
    }

    public function injectTtcontentRepository(TtcontentRepository $ttcontentRepository): void
    {
        $this->ttcontentRepository = $ttcontentRepository;
    }

    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function injectIndexerRepository(IndexerRepository $indexerRepository): void
    {
        $this->indexerRepository = $indexerRepository;
    }

    public function injectSessionHandler(SessionHandler $sessionHandler): void
    {
        $this->sessionHandler = $sessionHandler;
    }

    public function injectFeuserRepository(FrontendUserRepository $feuserRepository): void
    {
        $this->feuserRepository = $feuserRepository;
    }

    public function injectFegroupRepository(FrontendUserGroupRepository $fegroupRepository): void
    {
        $this->fegroupRepository = $fegroupRepository;
    }

    public function injectSearchUtility(SearchUtility $searchUtility): void
    {
        $this->searchUtility = $searchUtility;
    }

    public function injectStopWord(StopWord $stopWord): void
    {
        $this->stopWord = $stopWord;
    }

    public function injectTermManager(TermManager $termManager): void
    {
        $this->termManager = $termManager;
    }

    public function injectStatManager(StatManager $statManager): void
    {
        $this->statManager = $statManager;
    }

    public function injectSynonymManager(SynonymManager $synonymManager): void
    {
        $this->synonymManager = $synonymManager;
    }
}