<?php
/**
 * ulrich_products - CategoryRepository.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 24.05.2018 - 08:47:05
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * CategoryRepository
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class CategoryRepository extends StdRepository
{

    /**
     * Get sys_category records as \Abavo\UlrichProducts\Domain\Model\Category by tablename and uid method
     * 
     * @param int $uid
     * @return array
     */
    public function findByTableAndUid($table = null, $uid = null)
    {
        // Check params
        if (!is_int($uid)) {
            throw new \Exception('uid is undefined in '.__METHOD__);
        }

        // Get sys_category by page uid
        $sysCategories = $this->queryBuilder
            ->select('sys_category.*')
            ->from('sys_category')
            ->join(
                'sys_category', 'sys_category_record_mm', 'categoryMM',
                $this->queryBuilder->expr()->eq(
                    'categoryMM.uid_local', $this->queryBuilder->quoteIdentifier('sys_category.uid')
                )
            )
            ->where(
                $this->queryBuilder->expr()->eq('categoryMM.tablenames', $this->queryBuilder->createNamedParameter($table, \PDO::PARAM_STR)),
                $this->queryBuilder->expr()->eq('sys_category.l10n_parent', $this->queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $this->queryBuilder->expr()->eq('categoryMM.uid_foreign', $this->queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetchAll();

        // Convert as models on result
        if ($sysCategories) {
            $result = [];
            array_walk($sysCategories,
                function($record) use (&$result) {
                if (($sysCategory = $this->findByUid((int) $record['uid'])) instanceof $this->objectType) {
                    $result[(int) $record['sorting']] = $sysCategory;
                }
            });

            if (count($result)) {
                ksort($result, SORT_ASC);
                return array_values($result);
            }
        }
    }
}