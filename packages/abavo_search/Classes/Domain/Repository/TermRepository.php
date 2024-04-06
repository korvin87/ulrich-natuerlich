<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Abavo\AbavoSearch\User\GeneralUtilityHelper;
use Abavo\AbavoSearch\User\DatabaseUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The repository for Terms
 * 
 * @author mbruckmoser
 */
class TermRepository extends BaseRepository
{
    protected $defaultOrderings = [
        'search' => QueryInterface::ORDER_ASCENDING,
    ];

    public function findForTermManagerDoesTermExist($searchString = '', $fegroup = '')
    {
        $query = $this->createQuery();

        // FeGroup REGEX
        $arrFeGroups   = ($fegroup != null) ? array_merge(['0'], explode(',', $fegroup)) : ['0'];
        $feGroupsREGEX = ',('.implode('|', $arrFeGroups).'),';

        // Make Query
        $sql = 'SELECT *

                FROM tx_abavosearch_domain_model_term

                WHERE MATCH (search) AGAINST (? IN BOOLEAN MODE)
                
                AND sys_language_uid IN ?
                AND CONCAT(",", `fegroup`, ",") REGEXP ?
                AND pid IN ?
                LIMIT 1';

        $constraints = [
            DatabaseUtility::mysqlEscapeMimic($searchString).'*',
            [-1, $query->getQuerySettings()->getLanguageUid()],
            $feGroupsREGEX,
            $query->getQuerySettings()->getStoragePageIds()];

        // Query-Statement
        DatabaseUtility::replacePlaceholders($sql, $constraints);
        $query->statement($sql);

        return (boolean) count($query->execute(true));
    }

    public function findForAutocomplete($term = '', $sysLanguageUid = 0, $fegroup = '', $pid = '')
    {
        $query       = $this->createQuery();
        $storagePids = array_merge(
            $query->getQuerySettings()->getStoragePageIds(), GeneralUtility::trimExplode(',', DatabaseUtility::mysqlEscapeMimic($pid), true)
        );

        // FeGroup REGEX
        $arrFeGroups   = ($fegroup != null) ? array_merge(['0'], explode(',', $fegroup)) : ['0'];
        $feGroupsREGEX = ',('.implode('|', $arrFeGroups).'),';

        // Make Query
        $sql = 'SELECT

                DISTINCT search

                FROM tx_abavosearch_domain_model_term
                
                WHERE MATCH (search) AGAINST (? IN BOOLEAN MODE)
                AND sys_language_uid IN ?
                AND CONCAT(",", `fegroup`, ",") REGEXP ?
                AND pid IN ?

                GROUP BY search, fegroup
                ORDER BY search ASC

                LIMIT 15';

        $constraints = [
            DatabaseUtility::mysqlEscapeMimic($term).'*',
            [-1, (int) $sysLanguageUid],
            $feGroupsREGEX,
            $storagePids
        ];

        // Query-Statement
        DatabaseUtility::replacePlaceholders($sql, $constraints);
        $query->statement($sql);

        return $query->execute(true);
    }

    public function cleanTermsFormPid($pid = 0)
    {
        // Make Query
        $query         = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(true);
        $querySettings->setStoragePageIds([(int) $pid]);
        $querySettings->setRespectSysLanguage(false);
        $query->setQuerySettings($querySettings);

        // Index-Query-Statement
        $indexQuery = $this->createQuery();
        $sqlString  = 'SELECT * FROM tx_abavosearch_domain_model_index WHERE refid=? AND pid IN (?)';

        // Query-Exectute
        $terms = $query->execute();
        foreach ($terms as $term) {

            // Query-Statement
            $sql         = $sqlString;
            $constraints = [$term->getRefid(), (int) $pid];
                
            DatabaseUtility::replacePlaceholders($sql, $constraints);
            $indexQuery->statement($sql);

            $index = $indexQuery->execute();

            // Remove term if no index reference exist
            if (!(boolean) count($indexQuery->execute(true))) {
                $this->remove($term);
            }
        }

        // Keep auto_increment low
        $this->connection->executeQuery('ALTER TABLE tx_abavosearch_domain_model_term AUTO_INCREMENT=1');

        return true;
    }
}