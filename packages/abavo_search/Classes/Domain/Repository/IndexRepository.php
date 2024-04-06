<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Repository;

use Abavo\AbavoSearch\Domain\Model\Indexer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use Abavo\AbavoSearch\User\DatabaseUtility;

/**
 * The repository for Indexes
 * 
 * @author mbruckmoser
 */
class IndexRepository extends BaseRepository
{
    protected $searchConditions = [];

    /**
     * Get search method by search string
     *
     * @param string $searchString
     * @return string
     */
    private function getSearchModeBySearchString($searchString)
    {
        $stringLen = (int) strlen($searchString);
        $result    = $this->connection->executeQuery('show variables like "ft_min_word_len"')->fetch();

        if (isset($result['Value'])) {
            return ($stringLen >= $result['Value']) ? 'matchagainst' : 'like';
        }
    }

    /**
     * Set search base condtions
     *
     * @param string $searchString
     * @param int $sysLanguageUid
     * @param string $storagePids
     */
    private function setSearchBaseConditions($searchString, $sysLanguageUid, $storagePids)
    {

        // Search Methods
        $this->searchConditions = [
            // LIKE
            'like' => [
                'ranking' => '((ix.title LIKE ?)*10 + (ix.content LIKE ?)*5 + (ix.abstract LIKE ?)*2)/maxScore*100 + (ixconf.priority*10)',
                'from' => '(SELECT MAX((title LIKE ?)*10 + (content LIKE ?)*5 + (abstract LIKE ?)*2) AS maxScore FROM tx_abavosearch_domain_model_index) as maxScoreTable',
                'where' => '( ix.title LIKE ? OR ix.content LIKE ? OR ix.abstract LIKE ? )',
                'constraints' => [
                    //ranking
                    '%'.$searchString.'%',
                    '%'.$searchString.'%',
                    '%'.$searchString.'%',
                    //from
                    '%'.$searchString.'%',
                    '%'.$searchString.'%',
                    '%'.$searchString.'%',
                    //where
                    '%'.$searchString.'%',
                    '%'.$searchString.'%',
                    '%'.$searchString.'%',
                    [-1, $sysLanguageUid],
                    $storagePids
                ]
            ],
            // MATCH AGAINST
            // partial word searching, only support prefix, not support suffix: http://stackoverflow.com/questions/21682506/mysql-partial-word-searching
            'matchagainst' => [
                'ranking' => '((MATCH (ix.title) AGAINST (?)*2 + MATCH (ix.content,ix.abstract) AGAINST (?)) / maxScore)*100 + (ixconf.priority*10)',
                'from' => '(SELECT MAX(MATCH (title) AGAINST (?)*2 + MATCH (content,abstract) AGAINST (?)) AS maxScore FROM tx_abavosearch_domain_model_index) as maxScoreTable',
                'where' => '(MATCH (ix.title) AGAINST (? IN BOOLEAN MODE) OR MATCH (ix.content,ix.abstract) AGAINST (? IN BOOLEAN MODE))',
                'constraints' => [
                    //ranking
                    $searchString.'*',
                    $searchString.'*',
                    //from
                    $searchString.'*',
                    $searchString.'*',
                    //where
                    $searchString.'*',
                    $searchString.'*',
                    [-1, $sysLanguageUid],
                    $storagePids
                ]
            ]
        ];
    }

    /**
     * Find for search
     *
     * @param string $searchString
     * @param int $start
     * @param int $length
     * @param int $sysLanguageUid
     * @param string $fegroup
     * @param array $categories
     * @param array $facets
     * @param string $orderby
     * @param string $sorting
     * @param array $returnStructure
     * @return array
     */
    public function findForSearch($searchString = null, $start = false, $length = false, $sysLanguageUid = 0, $fegroup = '0', $categories = [0], $facets = [], $orderby = 'ranking',
                                  $sorting = 'desc', $returnStructure = [])
    {
        $return = null;

        $this->validateSearchParams($searchString, $start, $length, $sysLanguageUid, $fegroup, $categories, $facets, $orderby, $sorting);

        // Start init
        if ($searchString != '') {
            $timeStart = microtime(true);

            //Switch between SearchMethods and set SearchConditions
            $searchMethod = $this->getSearchModeBySearchString($searchString);
            $this->setSearchBaseConditions($searchString, $sysLanguageUid, $this->createQuery()->getQuerySettings()->getStoragePageIds());

            // Make Query
            $sql = 'SELECT ix.*,'.$this->searchConditions[$searchMethod]['ranking'].' AS ranking

                    FROM tx_abavosearch_domain_model_index AS ix
                    INNER JOIN tx_abavosearch_domain_model_indexer AS ixconf ON (ix.indexer = ixconf.uid)
                    ,'.$this->searchConditions[$searchMethod]['from'].'

                    WHERE '.$this->searchConditions[$searchMethod]['where'].'
                    
                    AND ix.sys_language_uid IN ?
                    AND ix.pid IN ?';

            // Indexer-condition
            if (array_key_exists('indexers', $facets) && !empty($facets['indexers'])) {
                $sql .= ' AND ix.indexer IN ('.implode(',', $facets['indexers']).')';
            }

            // Merge categories from facets
            if (!empty($facets) && array_key_exists('categories', $facets)) {
                $categories = array_merge($categories, $facets['categories']);
            }
            // Categories REGEX
            if (!empty($categories)) {
                $sql .= "\n".'AND CONCAT(",", ix.categories, ",") REGEXP ",('.implode('|', $categories).'),"';
            }

            // FeGroup REGEX
            $arrFeGroups = ($fegroup != null) ? array_merge(['0'], explode(',', $fegroup)) : ['0'];
            $sql         .= "\n".'AND CONCAT(",", ix.fegroup, ",") REGEXP ",('.implode('|', $arrFeGroups).'),"';

            // Order/Limit
            $order = "\n ORDER BY ".$orderby.' '.$sorting;
            $limit = "\n LIMIT ".$start.','.$length;

            // Set constraints
            $constraints = $this->searchConditions[$searchMethod]['constraints'];

            /*
             *  Limit-Query-Statemaent
             */
            $sqlString = $sql.$order.$limit;
            DatabaseUtility::replacePlaceholders($sqlString, $constraints);
            //print_r($sqlString);

            /*
             *  Execute Querys
             */
            $allCount    = $this->getFullCountForSearch($sql, $constraints); //Dev-Hint: Performance improvement: write this result in session?
            $queryResult = $this->connection->executeQuery($sqlString);

            $result = [];
            if ($queryResult->rowCount()) {
                $result = $this->objectManager->get(DataMapper::class)->map($this->objectType, $queryResult->fetchAll());
            }

            /*
             *  Result
             */
            $return = array_combine(
                $returnStructure,
                [
                $result,
                count($result),
                (microtime(true) - $timeStart),
                $allCount,
                null,
                $searchMethod
                ]
            );

            /*
             * FAL Workaround for stupid core exception #1314516809: File /user_upload/myfile.ext does not exist.
             * So we check here dirty if the refence is a valid object
             */
            if ($return['result']) {
                $cleanResult = [];
                foreach ($return['result'] as $resultItem) {

                    $type = $resultItem->getIndexer()->toArray()[0]->getType();
                    if ($type === 'Fal') {
                        if ($resultItem->getFilereference() !== null) {
                            array_push($cleanResult, $resultItem);
                        } else {
                            $return['allCount'] --;
                        }
                    } else {
                        array_push($cleanResult, $resultItem);
                    }
                }
                $return['result'] = $cleanResult;
            }
        }

        return $return;
    }

    /**
     * Get full count for search
     *
     * @param string $sql
     * @param mixed $constraints
     * @return int
     */
    private function getFullCountForSearch($sql = null, $constraints = null)
    {
        if ($sql && $constraints) {
            $query = $this->createQuery();

            DatabaseUtility::replacePlaceholders($sql, $constraints);
            $query->statement($sql);

            // Execute Query
            return count($query->execute(true)); // Because of bug in $query->Count() returns count of all records in table
        }
    }

    /**
     * Validate Search params
     *
     * @param string $searchString
     * @param int $start
     * @param int $length
     * @param int $sysLanguageUid
     * @param string $fegroup
     * @param array $categories
     * @param array $facets
     * @param string $orderby
     * @param string $sorting
     */
    private function validateSearchParams(&$searchString, &$start, &$length, &$sysLanguageUid, &$fegroup, &$categories, &$facets, &$orderby, &$sorting)
    {
        // Get MySQL connection resourcehandle
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionByName('Default');
        $mysqli     = $connection->getWrappedConnection()->getWrappedResourceHandle();


        $searchString   = $mysqli->real_escape_string($searchString);
        $start          = (int) $start;
        $length         = (int) $length;
        $sysLanguageUid = (int) $sysLanguageUid;
        $fegroup        = $mysqli->real_escape_string($fegroup);

        // Categories
        if (!empty($categories)) {
            foreach ($categories as $key => $category) {
                $categories[$key] = (int) $category;
            }
        }

        // Facets
        if (!empty($facets)) {
            foreach ($facets as $facetType => $rawFacets) {
                foreach ($rawFacets as $key => $tempFacet) {
                    $facets[$facetType][$key] = $mysqli->real_escape_string(trim($tempFacet));
                }
            }
        }

        // orderby
        $orderby = $mysqli->real_escape_string(trim($orderby));
        if (!in_array($orderby, ['ranking', 'title', 'content'])) {
            $orderby = 'ranking';
        }

        // sorting
        $sorting = $mysqli->real_escape_string(trim($sorting));
        if (!in_array($sorting, ['asc', 'desc'])) {
            $sorting = 'desc';
        }
    }

    /**
     * Find youngest index
     *
     * @param int $pid
     * @return array
     */
    public function findYoungestIndex($pid = 0)
    {
        $sql   = 'SELECT * FROM tx_abavosearch_domain_model_index';
        $where = ((boolean) $pid) ? ' WHERE pid='.(int) $pid : '';
        $sql   .= $where.' ORDER BY datetime DESC LIMIT 1';

        $result = $this->connection->executeQuery($sql)->fetch();

        return (!empty($result)) ? current($result) : null;
    }

    /**
     * Cleanup by indexer
     *
     * @param Indexer $indexer
     */
    public function cleanUpByIndexer(Indexer $indexer)
    {
        if ((boolean) $indexer->getUid()) {
            $this->queryBuilder->delete('tx_abavosearch_domain_model_index')
                ->where(
                    $this->queryBuilder->expr()->eq('pid', $this->queryBuilder->createNamedParameter($indexer->getStoragepid(), \PDO::PARAM_INT)),
                    $this->queryBuilder->expr()->eq('indexer', $this->queryBuilder->createNamedParameter($indexer->getUid(), \PDO::PARAM_INT))
                )
                ->execute();

            $this->connection->executeQuery('ALTER TABLE tx_abavosearch_domain_model_index AUTO_INCREMENT=1');
        }
    }

    /**
     * Cleanup by unused index
     *
     * @param string $pids
     * @param array $uids
     */
    public function cleanUpByUnuesedIndex($pids = '0', $uids = [0])
    {
        $string = null;
        $this->queryBuilder
            ->delete('tx_abavosearch_domain_model_index')
            ->where(
                $this->queryBuilder->expr()->eq('pid', $this->queryBuilder->createNamedParameter(implode(',', GeneralUtility::intExplode($pids, $string, true)))),
                $this->queryBuilder->expr()->eq('indexer', $this->queryBuilder->createNamedParameter(implode(',', array_map('intval', $uids))))
            )
            ->execute();

        $this->connection->executeQuery('ALTER TABLE tx_abavosearch_domain_model_index AUTO_INCREMENT=1');
    }

    /**
     * Cleanup by unused index
     *
     * @param string $pids
     * @param array $uids
     */
    public function removeByValidRefids($validRefIds = [], $indexerUid = null)
    {
        if ((boolean) $indexerUid) {
            $this->queryBuilder
                ->delete('tx_abavosearch_domain_model_index')
                ->where(
                    $this->queryBuilder->expr()->eq('indexer', $this->queryBuilder->createNamedParameter($indexerUid, \PDO::PARAM_INT))
            );

            if (!empty($validRefIds)) {
                $strValidRefIds = "'".implode("','", $validRefIds)."'";
                $this->queryBuilder->andWhere(
                    $this->queryBuilder->expr()->notIn('refid', $strValidRefIds)
                );
            }

            $this->queryBuilder->execute();

            $this->connection->executeQuery('ALTER TABLE tx_abavosearch_domain_model_index AUTO_INCREMENT=1');
        }
    }
}