<?php
/*
 * abavo_search
 *
 * @copyright   2018 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Indexers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Abavo\AbavoSearch\Domain\Model\Index;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Domain\Repository\PageRepository;
use Abavo\AbavoSearch\Domain\Repository\TtcontentRepository;

/**
 * ContentIndexer
 *
 * @author mbruckmoser
 */
class ContentIndexer extends BaseIndexer
{
    /**
     * pageRepository
     *
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * fePageRepository
     *
     * @var \TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    protected $fePageRepository;

    /**
     * ttcontentRepository
     *
     * @var TtcontentRepository
     */
    protected $ttcontentRepository;

    /**
     * the constructor
     * 
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        /*
         * Set Attributes, because seems in SingletonInterface @inject doesnt work
         * because of issue https://forge.typo3.org/issues/48544
         */
        $objectManager             = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageRepository      = $objectManager->get(PageRepository::class);
        $this->fePageRepository    = $objectManager->get(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
        $this->ttcontentRepository = $objectManager->get(TtcontentRepository::class);
        $this->settings            = $settings;
    }

    /**
     * Get data method
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $timeStart = microtime(true);
        $languages = GeneralUtility::intExplode(',', $this->settings['language']); // Get all Languages

        if (!$indexer) {
            throw new IndexException(__METHOD__.'\getData: No indexer given.');
        }

        // DokTypes
        $dokTypes = '1';
        if ($this->settings['customDokTypes']) {
            $dokTypes .= ','.implode(',', GeneralUtility::intExplode(',', $this->settings['customDokTypes']));
        }

        // Get all Pages from PID(s)
        foreach ($languages as $langUid) {

            $resourcesPages = $this->getPagesRecursiveByType($this->settings['pages'], $langUid, $dokTypes);

            // Exclude pages
            $excludePages = [];
            if ($this->settings['excludepages']) {
                $excludePages = $this->getPagesRecursiveByType($this->settings['excludepages'], $langUid, $dokTypes);
            }

            // Get all tt_content for pages
            $pageKeys = [...array_keys($resourcesPages['result']), ...array_filter(array_column($resourcesPages['result'], 'content_from_pid'), fn($a) => (int) $a !== 0)];

            if (!empty($pageKeys)) {
                $CTypes             = GeneralUtility::trimExplode(',', $this->settings['customCTypes'], true);
                $resourcesTtcontent = $this->getContentsByPagesAndCType($pageKeys, $langUid, $CTypes, true);
            }

            // work all pages ($key = pageId, page['uid'] = target)
            foreach ($resourcesPages['result'] as $pid => $page) {

                // Remove excludePages
                if (is_array($excludePages['result']) && array_key_exists($pid, $excludePages['result'])) {
                    unset($resourcesPages['result'][$pid]);
                    continue;
                }

                /*
                 *  Generate Page Content
                 */

                //content_from_pid modify
                if ($page['content_from_pid'] > 0) {
                    // target
                    $pid = $page['content_from_pid'];

                    // fe_group
                    if ($contentFromPid = $this->pageRepository->findByUid($page['content_from_pid'])) {
                        $page['fe_group'] = $contentFromPid->getFegroup();
                    }
                }

                // Get Categories
                if ((boolean) $page['categories']) {
                    $tempCategories = [];
                    $categories     = $this->pageRepository->findByUid($page['uid'])->getCategories();
                    if ((boolean) (is_countable($categories) ? count($categories) : 0)) {
                        foreach ($categories as $category) {
                            array_push($tempCategories, $category->getUid());
                        }
                        $page['categories'] = implode(',', $tempCategories);
                    }
                } else {
                    $page['categories'] = '';
                }

                // Get TT_CONTENT
                $ttContentKeys = array_keys($resourcesTtcontent['resultPids'], $pid);

                $content = '';
                foreach ($ttContentKeys as $key) {
                    $tmpContent = $resourcesTtcontent['result'][$key];
                    if (is_string($tmpContent['header'])) {
                        $content .= $tmpContent['header'] . " ";
                    }
                    if (is_string($tmpContent['bodytext'])) {
                        $content .= $tmpContent['bodytext'];
                    }
                }

                // Index-Modify-Hook
                $title    = $page['title'];
                $title    .= ($page['subtitle']) ? ' &ndash; '.$page['subtitle'] : '';
                $abstract = $page['abstract'];
                $this->modifyIndexHook('Page', $page, $title, $content, $abstract);

                // Make Index Object
                $tempIndex = Index::getInstance();
                $tempIndex->setTitle(strip_tags($title));
                $tempIndex->setContent(preg_replace('!\s+!', ' ', strip_tags($content)));
                $tempIndex->setAbstract(preg_replace('!\s+!', ' ', strip_tags($abstract)));
                $tempIndex->setTarget($page['uid']);
                $tempIndex->setFegroup($page['fe_group'] ?: self::FE_GROUP_DEFAULT);
                $tempIndex->setSysLanguageUid($langUid);
                $tempIndex->setCategories($page['categories']);
                $tempIndex->setRefid('pages'.self::REFID_SEPERARTOR.$page['uid']);

                //Set additional fields
                $this->setAdditionalFields($tempIndex, $indexer);

                // Add index to list
                $this->data[] = $tempIndex;
            }
        }

        // Set duration time
        $this->duration[$indexer->getUid()] = (microtime(true) - $timeStart);

        return $this->data;
    }

    /**
     * Find pages recursive by type
     *
     * @param string $pages commaseparated pids
     * @param int $languageUid
     * @param string $dokTypes commasepareted doktype list
     * @param boolean $returnPids
     * @return array
     */
    public function getPagesRecursiveByType($pages = '', $languageUid = 0, $dokTypes = '1')
    {

        if ($pages != '') {

            $timeStart             = microtime(true);
            $contentPidBasedResult = [];
            $query                 = $this->pageRepository->createQuery();

            // Define QuerySettings
            $querySettings = $query->getQuerySettings()
                ->setRespectStoragePage(false)
                ->setLanguageUid($languageUid);

            $query->setQuerySettings($querySettings);

            /*
             * Make Query Constraints
             * FieldNames specified in Configuration/setup.txt
             */
            $query->matching(
                $query->logicalAnd([
                    $query->in('uid', $this->pageRepository->getTreeList($pages)),
                    $query->in('doktype', GeneralUtility::intExplode(',', $dokTypes)),
                    $query->equals('no_search', 0)
                ])
            );

            // Execute query
            $result = $query->execute(true);
            $this->pageRepository->getRootlineFields($result);

            if (is_countable($result) ? count($result) : 0) {
                foreach ($result as $record) {
                    $contentPidBasedResult[(($record['l10n_parent'] !== 0) ? $record['l10n_parent'] : $record['uid'])] = $record;
                }
            }

            return [
                'result' => $contentPidBasedResult, // Put PagePids in separate result, for page to tt_content index-mapping
                'queryTime' => (microtime(true) - $timeStart)
            ];
        }
    }

    /**
     * Find by pages AndCType
     * 
     * @param int $pages
     * @param int $languageUid
     * @param array $CTypes
     * @param boolean $returnPids
     * @return type
     */
    public function getContentsByPagesAndCType($pages = [], $languageUid = 0, $CTypes = [], $returnPids = false)
    {

        if (!empty($pages)) {

            // Merge CTypes
            $CTypes = array_merge(TtcontentRepository::DEFAULT_CTYPES, $CTypes);

            $timeStart = microtime(true);

            // Define QuerySettings
            $query         = $this->ttcontentRepository->createQuery();
            $querySettings = $query->getQuerySettings()
                ->setLanguageUid($languageUid)
                ->setStoragePageIds($pages);

            $query->setQuerySettings($querySettings);

            /*
             * Make Query Constraints
             * FieldNames specified in Configuration/setup.txt
             */
            $query->matching(
                $query->logicalAnd([
                    $query->in('contentType', $CTypes)
                ])
            );

            // Execute query
            $result = $query->execute(true);

            /*
             *  Additional work each content
             */
            foreach ($result as $key => $content) {
                // Get Related Record (ContentFromContent)
                if ($content['CType'] === 'shortcut') {
                    $result[$key]['bodytext'] = $this->ttcontentRepository->findByRelation($querySettings, $content['records'], $CTypes);
                }
                // Unset header with header_layout 100
                if ($content['header_layout'] === '100') {
                    $result[$key]['header'] = '';
                }
            }

            return [
                'result' => $result,
                'resultPids' => array_column($result, 'pid'), // Put PagePids in separate result, for page to tt_content index-mapping
                'queryCount' => count($result),
                'queryTime' => (microtime(true) - $timeStart)
            ];
        }
    }
}
