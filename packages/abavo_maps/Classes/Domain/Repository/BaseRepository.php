<?php

namespace TYPO3\AbavoMaps\Domain\Repository;

/*
 * abavo_maps
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\AbavoMaps\User\GeneralUtilityHelper;

/**
 * The repository for Adresses as Markers
 */
class BaseRepository extends Repository
{
    /*
     * Find one db entry by ukey and table name
     *
     * @return object
     */

    public function findByUkey($repository = null, $ukey = 0)
    {

        if (is_object($repository) && (boolean) $ukey) {

            // Make query and get single domain object with query object from foreign repository
            $table = $this->objectManager->get(DataMapper::class)->convertClassNameToTableName($repository->objectType);
            $query = $repository->createQuery();
            $query->getQuerySettings()->setRespectStoragePage(FALSE);
            
            $query->statement('SELECT
                uid,
                latitude,
                longitude

                FROM '.GeneralUtilityHelper::mysql_escape_mimic($table).'

                WHERE

                MD5( CONCAT(uid,"/","'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'].'") ) = "'.GeneralUtilityHelper::mysql_escape_mimic($ukey).'"

                LIMIT 1
            ');


            $results = $query->execute();
            return (!empty($results)) ? $results[0] : null;
        } else {
            return null;
        }
    }
}