<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Abavo\AbavoSearch\User\GeneralUtilityHelper;
use Abavo\AbavoSearch\User\DatabaseUtility;

/**
 * The repository for Pages
 * 
 * @author mbruckmoser
 */
class PageRepository extends Repository
{
    /**
     * fePageRepository
     *
     * @var \TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    protected $fePageRepository;

    /**
     * Find pages recursive by type
     *
     * @param string $pages commaseparated pids
     * @param int $languageUid
     * @param string $dokTypes commasepareted doktype list
     * @param boolean $returnPids
     * @deprecated since TYPO3 9.5 LTS use ContentIndexer instead
     * @return array
     */
    public function findPagesRecursiveByType($pages = '', $languageUid = 0, $dokTypes = '1', $returnPids = false)
    {
        trigger_error(__METHOD__ .' with PageIndexer doenst work for TYPO3 9.5 anymore. Please use ContentIndexer instead.', E_USER_DEPRECATED);

        /**
         * Workaround for TYPO3 <= 8.7 using extbase repositories in cli mode @ Classes/Database/Query/Restriction/FrontendGroupRestriction.php in line 36.
         * @global type $GLOBALS['TSFE']
         * @name $TSFE 
         */
        if ((version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '8.7', '<=')) && !isset($GLOBALS['TSFE']->gr_list)) {
            $GLOBALS['TSFE']          = new \stdClass;
            $GLOBALS['TSFE']->gr_list = '0,-1';
        }

        if ($pages != '') {

            $timeStart  = microtime(true);
            $result     = $resultPids = [];
            $query      = $this->createQuery();


            // DEFAULT LANGUAGE
            if ((int) $languageUid == 0) {

                // Define QuerySettings
                $querySettings = $query->getQuerySettings();
                $querySettings->setRespectStoragePage(false);
                $querySettings->setLanguageUid($languageUid);
                /*
                 *  HINT: This following settings doesnt work because of issues
                 *  https://forge.typo3.org/issues/45873
                 *  https://forge.typo3.org/issues/47192
                 *  so we make a alternative query if sys_language_uid > 0
                 */
                //$querySettings->setLanguageOverlayMode('hideNonTranslated');
                //$querySettings->setRespectSysLanguage(false);
                //$querySettings->setLanguageMode('strict');
                $query->setQuerySettings($querySettings);


                /*
                 * Make Query Constraints
                 * FieldNames specified in Configuration/setup.txt
                 */
                $query->matching(
                    $query->logicalAnd([
                        $query->in(
                            'doktype', GeneralUtility::intExplode(',', $dokTypes)
                        ),
                        $query->in(
                            'uid', $this->getTreeList($pages)
                        ),
                        $query->equals(
                            'no_search', 0)
                    ])
                );

                // Put PagePids in separate result, for page to tt_content index-mapping
                $result     = $query->execute(true);
                $this->getRootlineFields($result);
                $resultPids = ($returnPids) ? array_map(fn($page) => $page['uid'], $result) : null;


                // ALL OTHER LANGUAGES
            } else {

                $sql = 'SELECT

                    o.title,
                    o.subtitle,
                    o.nav_title,
                    o.media,
                    o.keywords,
                    o.description,
                    o.abstract,
                    o.pid,

                    p.uid,
                    p.fe_group,
                    p.categories,
                    p.content_from_pid

                    FROM '.((version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '8.7', '<=')) ? 'pages_language_overlay' : 'pages').' AS o
                    INNER JOIN pages AS p ON (o.pid=p.uid)

                    WHERE o.sys_language_uid=?
                    AND o.pid IN ?
                    AND o.t3ver_state<=0

                    AND p.deleted=0
                    AND p.hidden=0

                    AND o.deleted=0
                    AND o.hidden=0
                    AND o.starttime<=UNIX_TIMESTAMP()
                    AND (o.endtime=0 OR o.endtime>UNIX_TIMESTAMP())

                    AND p.no_search=0';

                $constraints = [
                    $languageUid,
                    $this->getTreeList($pages)
                ];

                DatabaseUtility::replacePlaceholders($sql, $constraints);
                $query->statement($sql);

                // Put PagePids in separate result, for page to tt_content index-mapping
                $result     = $query->execute(true);
                $this->getRootlineFields($result);
                $resultPids = ($returnPids) ? array_map(fn($page) => $page['uid'], $result) : null;
            }

            return [
                'result' => array_combine($resultPids, $result),
                'queryTime' => (microtime(true) - $timeStart)
            ];
        }
    }

    public function getTreeList($pages = '0', $deep = 9999)
    {

        // Get PageTreeList
        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
        $treeList       = [];

        $pages = GeneralUtility::intExplode(',', $pages);
        if (!empty($pages)) {
            foreach ($pages as $pid) {
                $pids     = explode(',', $queryGenerator->getTreeList((int) $pid, (int) $deep, 0, 1));
                $treeList = array_merge($pids, $treeList);
            }
        }

        return $treeList;
    }

    /**
     * Push defined field values the rootline up to a page
     *
     * @param array $result The page results array
     * @param string $fields The rootlineFields commasepareted. NOTE: Only fields with posibility to have commasepareted list are mergeable.
     */
    public function getRootlineFields(&$result = [], $fields = 'fe_group')
    {
        if (!empty($result)) {
            // Get all fields page tree up
            $fields = GeneralUtility::trimExplode(',', $fields);

            foreach ($result as $key => $page) {

                // Get rootline values
                $rootline = GeneralUtility::makeInstance(RootlineUtility::class, $page['uid'], '')->get();
                $values   = [];
                foreach ($rootline as $item) {

                    // Work each item
                    foreach ($fields as $field) {

                        $val = $item[$field];

                        //Exclude '0' values
                        if ((boolean) $val) {

                            // Set if empty else merge
                            if (!(boolean) $page[$field]) {
                                $page[$field] = $val;
                            } else {

                                // Exist id?
                                $currentValues = GeneralUtility::trimExplode(',', $page[$field]);
                                if (!in_array($val, $currentValues)) {
                                    $page[$field].= ','.$val;
                                }
                            }
                        }
                    }
                }


                // Write back modified result
                $result[$key] = $page;
            }
        }
    }

    public function injectFePageRepository($fePageRepository): void
    {
        $this->fePageRepository = $fePageRepository;
    }
}