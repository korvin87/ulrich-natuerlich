<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The repository for Pages
 *
 * @author mbruckmoser
 */
class TtcontentRepository extends Repository
{
    public CONST DEFAULT_CTYPES = ['textmedia', 'text', 'textpic', 'image', 'bullets', 'table', 'html', 'header', 'uploads', 'shortcut'];

    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Find by pages AndCType
     * 
     * @param int $pages
     * @param int $languageUid
     * @param array $CTypes
     * @param boolean $returnPids
     * @deprecated since TYPO3 9.5 LTS use ContentIndexer instead
     * @return type
     */
    public function findByPagesAndCType($pages = [], $languageUid = 0, $CTypes = [], $returnPids = false)
    {
        trigger_error(__METHOD__ .' with PageIndexer doenst work for TYPO3 9.5 anymore. Please use ContentIndexer instead.', E_USER_DEPRECATED);

        if (!empty($pages)) {

            // Merge CTypes
            $CTypes = array_merge(self::DEFAULT_CTYPES, $CTypes);

            $timeStart = microtime(true);

            // Define QuerySettings
            $query         = $this->createQuery();
            $querySettings = $query->getQuerySettings();
            $querySettings->setRespectStoragePage(false);

            // Multilang Settings
            $querySettings->setLanguageUid($languageUid);
            $querySettings->setLanguageOverlayMode(true);
            $querySettings->setRespectSysLanguage(true);

            $query->setQuerySettings($querySettings);

            /*
             * Make Query Constraints
             * FieldNames specified in Configuration/setup.txt
             */
            $query->matching(
                $query->logicalAnd([
                    $query->in(
                        'contentType', $CTypes
                    ),
                    $query->in(
                        'pid', $pages
                    )
                ])
            );

            // Put PagePids in separate result, for page to tt_content index-mapping
            $result     = $query->execute(true);
            $resultPids = ($returnPids) ? array_map(fn($ttcontent) => $ttcontent['pid'], $result) : null;

            /*
             *  Additional work each content
             */
            foreach ($result as $key => $content) {
                // Get Related Record (ContentFromContent)
                if ($content['CType'] == 'shortcut') {
                    $result[$key]['bodytext'] = $this->findByRelation($querySettings, $content['records'], $CTypes);
                }
                // Unset header with header_layout 100
                if ($content['header_layout'] === '100') {
                    $result[$key]['header'] = '';
                }
            }

            return [
                'result' => $result,
                'resultPids' => $resultPids,
                'queryCount' => count($result),
                'queryTime' => (microtime(true) - $timeStart)
            ];
        }
    }

    /**
     * Find by relation method
     *
     * @param QuerySettingsInterface $querySettings
     * @param string $relation
     * @param array $CTypes
     * @return array
     */
    private function findByRelation($querySettings = null, $relation = '', $CTypes = [])
    {
        // Define QuerySettings
        $query = $this->createQuery();
        $query->setQuerySettings($querySettings);

        // Get relation records
        $content       = '';
        $recordRecords = explode(',', $relation);
        foreach ($recordRecords as $recordRecord) {

            $recordFullString = $recordRecord;
            $recordRecord     = explode('_', $recordRecord);
            end($recordRecord);

            $recordIdent = current($recordRecord);
            $recordTable = str_replace('_'.$recordIdent, '', $recordFullString);

            if ($recordTable === 'tt_content') {
                // Define query
                $sql = 'SELECT bodytext

                    FROM tt_content

                    WHERE
                    uid='.(int) $recordIdent.'
                    AND deleted=0
                    AND hidden=0
                    AND starttime<=UNIX_TIMESTAMP()
                    AND (endtime=0 OR endtime>UNIX_TIMESTAMP())

                    LIMIT 1';

                $query->statement($sql);

                // Append each content
                foreach ($query->execute(true) as $record) {
                    $content .= $record['bodytext']."\r\n";
                }
            } else if ($recordTable === 'pages') {
                $query->matching(
                    $query->logicalAnd([
                        $query->in(
                            'contentType', $CTypes
                        ),
                        $query->in(
                            'pid', explode(',', $recordIdent)
                        )
                    ])
                );

                $result = $query->execute(true);

                foreach ($result as $entry) {
                    if ($entry['header_layout'] !== '100') {
                        $content .= $entry['header']."\r\n";
                    }
                    $content .= $entry['bodytext']."\r\n";
                }
            }
        }

        return trim($content);
    }
}
