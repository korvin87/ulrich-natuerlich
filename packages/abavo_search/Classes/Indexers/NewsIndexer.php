<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Indexers;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Information\Typo3Version;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Domain\Model\Index;
use GeorgRinger\News\Domain\Repository\NewsRepository;

/**
 * NewsIndexer
 *
 * @author mbruckmoser
 */
class NewsIndexer extends BaseIndexer
{
    /**
     * @var GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    function __construct($settings)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /**
         * Workaround for TYPO3 <= 8.7 using extbase repositories in cli mode @ Classes/Database/Query/Restriction/FrontendGroupRestriction.php in line 36.
         * @global type $GLOBALS['TSFE']
         * @name $TSFE 
         */
        if ((version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), '8.7', '<=')) && !isset($GLOBALS['TSFE']->gr_list)) {
            $GLOBALS['TSFE']          = new \stdClass;
            $GLOBALS['TSFE']->gr_list = '0,-1';
        }

        $this->newsRepository = $objectManager->get(NewsRepository::class);

        // Init queryBuilder
        $connectionPool     = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->queryBuilder = $connectionPool->getQueryBuilderForTable('sys_category');

        $this->settings = $settings;
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $timeStart = microtime(true);

        if (!$indexer) {
            throw new IndexException('NewsIndexer\getData: No indexer given.');
        }

        // Define query settings
        $querySettings = $this->newsRepository->createQuery()->getQuerySettings();
        $querySettings->setStoragePageIds(explode(',', $this->settings['pages']));
        $querySettings->setRespectSysLanguage(true);
        $querySettings->setLanguageMode('strict');

        // Get records for each languange
        foreach (GeneralUtility::intExplode(',', $this->settings['language']) as $sys_language_uid) {

            // Set repository to uid
            $querySettings->setLanguageUid($sys_language_uid);
            $this->newsRepository->setDefaultQuerySettings($querySettings);

            // Get all news records (as raw records because of extbase translation bugs:-()
            $resources = $this->newsRepository->createQuery()->execute(true);

            // Set category filter
            if ($this->settings['categories']) {
                $categoryFilterArray = GeneralUtility::intExplode(',', $this->settings['categories']);
            } else {
                $categoryFilterArray = false;
            }

            foreach ($resources as $result) {
                if (is_array($categoryFilterArray)) {

                    $queryBuilder = clone $this->queryBuilder;
                    $dbresult     = $queryBuilder->select('sys_category.*')
                            ->from('sys_category')
                            ->join('sys_category', 'sys_category_record_mm', 'cmm', $queryBuilder->expr()->eq('sys_category.uid', $queryBuilder->quoteIdentifier('cmm.uid_local')))
                            ->where(
                                $queryBuilder->expr()->eq('cmm.tablenames', $queryBuilder->createNamedParameter('tx_news_domain_model_news')),
                                $queryBuilder->expr()->eq('cmm.fieldname', $queryBuilder->createNamedParameter('categories')),
                                $queryBuilder->expr()->eq('cmm.uid_foreign', $queryBuilder->createNamedParameter($result['uid'], \PDO::PARAM_INT))
                            )->execute();

                    $categoryUids = [];
                    if ($dbresult->rowCount() > 0) {
                        while ($row = $dbresult->fetch()) {
                            $categoryUids[] = $row['uid'];
                        }
                    }

                    $dbresult    = array_intersect($categoryUids, $categoryFilterArray);
                    $indexRecord = !empty($dbresult);
                } else {
                    $indexRecord = true;
                }

                if ($indexRecord === true) {
                    // Workaround for correct locale
                    $uid = ($result['_LOCALIZED_UID'] ?: $result['uid']);

                    // Index-Modify-Hook
                    $title    = $result['title'];
                    $content  = $result['bodytext'];
                    $abstract = $result['teaser'];
                    $this->modifyIndexHook('News', $this->newsRepository->findByUid($uid), $title, $content, $abstract);

                    // Make Index Object
                    $tempIndex = Index::getInstance();
                    $tempIndex->setTitle(strip_tags($title));
                    $tempIndex->setContent(preg_replace('!\s+!', ' ', strip_tags($content)));
                    $tempIndex->setAbstract(preg_replace('!\s+!', ' ', strip_tags($abstract)));
                    $tempIndex->setParams('&tx_news_pi1[news]='.$result['uid'].'&tx_news_pi1[controller]=News&tx_news_pi1[action]=detail');
                    $tempIndex->setTarget($indexer->getTarget());
                    $tempIndex->setFegroup($result['fe_group'] ?: self::FE_GROUP_DEFAULT);
                    $tempIndex->setSysLanguageUid($result['sys_language_uid']);
                    $tempIndex->setRefid('tx_news_domain_model_news'.self::REFID_SEPERARTOR.$uid);

                    //Set additional fields
                    $this->setAdditionalFields($tempIndex, $indexer);

                    // Add index to list
                    $this->data[] = $tempIndex;
                }
            }
        }


        // Set duration time
        $this->duration[$indexer->getUid()] = (microtime(true) - $timeStart);

        return $this->data;
    }
}
