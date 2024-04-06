<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
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
 * PageIndexer
 *
 * @author mbruckmoser
 */
class PageIndexer extends BaseIndexer
{
    /**
     * pageRepository
     *
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * ttcontentRepository
     *
     * @var TtcontentRepository
     */
    protected $ttcontentRepository;

    function __construct($settings)
    {
        /*
         * Set Attributes, because seems in SingletonInterface @inject doesnt work
         * because of issue https://forge.typo3.org/issues/48544
         */
        $objectManager             = GeneralUtility::makeInstance(ObjectManager::class);
        $this->pageRepository      = $objectManager->get(PageRepository::class);
        $this->ttcontentRepository = $objectManager->get(TtcontentRepository::class);
        $this->settings            = $settings;
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $timeStart = microtime(true);
        $languages = explode(',', $this->settings['language']); // Get all Languages

        if (!$indexer) {
            throw new IndexException('PageIndexer\getData: No indexer given.');
        }

        // DokTypes
        $dokTypes = 1;
        if ($this->settings['customDokTypes']) {
            $dokTypes .= ','.implode(',', GeneralUtility::intExplode(',', $this->settings['customDokTypes']));
        }

        // Get all Pages from PID(s)
        foreach ($languages as $langUid) {

            $resourcesPages = $this->pageRepository->findPagesRecursiveByType($this->settings['pages'], $langUid, $dokTypes, true);

            // Exclude pages
            $excludePages = [];
            if ($this->settings['excludepages']) {
                $excludePages = $this->pageRepository->findPagesRecursiveByType($this->settings['excludepages'], $langUid, $dokTypes, true);
            }

            // Get all tt_content for pages
            $pageKeys = [...array_keys($resourcesPages['result']), ...array_filter(array_column($resourcesPages['result'], 'content_from_pid'), fn($a) => $a !== 0)];
            if (!empty($pageKeys)) {
                $CTypes             = GeneralUtility::trimExplode(',', $this->settings['customCTypes'], true);
                $resourcesTtcontent = $this->ttcontentRepository->findByPagesAndCType($pageKeys, $langUid, $CTypes, true);
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
                $content       = '';
                foreach ($ttContentKeys as $key) {
                    $tmpContent = $resourcesTtcontent['result'][$key];
                    $content    .= $tmpContent['header']." ".$tmpContent['bodytext']." ";
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
}