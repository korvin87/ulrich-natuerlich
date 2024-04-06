<?php
/*
 * abavo_form
 *
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
/**
 * The base repository
 */
class BaseRepository extends Repository
{

    public function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    public function getTableName()
    {
        return $this->objectManager->get(DataMapper::class)->convertClassNameToTableName($this->objectType);
    }
}