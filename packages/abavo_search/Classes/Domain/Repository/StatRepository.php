<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use \TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Abavo\AbavoSearch\User\GeneralUtilityHelper;
use Abavo\AbavoSearch\User\DatabaseUtility;

/**
 * The repository for Stat
 *
 * @author mbruckmoser
 */
class StatRepository extends Repository
{
    protected $defaultOrderings = [
        'hits' => QueryInterface::ORDER_DESCENDING
    ];

    /**
     * Find For Stat Manager Method
     *
     * @param string $searchString
     * @param string $type A valid stat type. Possible value: 'expression', 'term', 'record'
     * @return void
     */
    public function findForStatManager($searchString = '', $type = '', $sysLanguageUid = 0)
    {
        $validTypes = ['expression', 'term', 'record'];

        // is type valid?
        if (in_array($type, $validTypes) && trim($searchString) != '') {

            // Create query
            $query = $this->createQuery();
            $query->setLimit(1);
            $query->setQuerySettings($query->getQuerySettings()->setRespectSysLanguage(false));

            // Make constraints
            $query->matching(
                $query->logicalAnd([
                    $query->equals('type', $type),
                    $query->equals('val', $searchString, false),
                    $query->in('sys_language_uid', [$sysLanguageUid])
                ])
            );

            // Execute query
            return $query->execute()->getFirst();
        } else {
            return false;
        }
    }

    /**
     * Finds orders for AJAX-DataTable-Request
     * @param int $start offset start
     * @param int $length offset length
     * @param int $pid PageId
     * @param array $arrSearch filter options
     * @param array $arrFilter filter options
     * @param string $order SQL Order by
     * @return array Orders
     */
    public function findAllForDataTables($start = false, $length = false, $pid = 0, $arrSearch = ['%'], $arrFilter = [], $order = 'hits DESC')
    {
        // Make query
        $query = $this->createQuery();
        if (empty($arrSearch)) {
            $arrSearch = ['%'];
        }
        $sql = 'SELECT * FROM tx_abavosearch_domain_model_stat WHERE val LIKE ? AND pid='.(int) $pid;

        // Language filter
        if (array_key_exists('sys_language_uid', $arrFilter)) {
            $sql.= ' AND sys_language_uid='.(int) $arrFilter['sys_language_uid'];
        }

        // Type filter
        if (array_key_exists('type', $arrFilter)) {
            $sql.= ' AND type="'.DatabaseUtility::mysqlEscapeMimic($arrFilter['type']).'"';
        }

        // Order by
        $order = trim($order);
        if ($order != '') {
            $sql.= ' ORDER BY '.DatabaseUtility::mysqlEscapeMimic($order);
        }

        // Query-Offset
        $sql.= ($start !== false) ? ' LIMIT '.(int) $start : '';
        $sql.= ($length !== false && $start !== false) ? ','.(int) $length : '';

        // Merge query with search values
        DatabaseUtility::replacePlaceholders($sql, $arrSearch);
        $query->statement($sql);

        // Return
        return [
            'records' => $stats         = $query->execute(true),
            'recordsCount' => count($stats)
        ];
    }
}