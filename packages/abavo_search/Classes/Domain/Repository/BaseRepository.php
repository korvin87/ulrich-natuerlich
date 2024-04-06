<?php
/*
 * abavo_search
 *
 * @copyright   2018 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * BaseRepository
 *
 * @author mbruckmoser
 */
class BaseRepository extends Repository
{
    /**
     * The connection
     *
     * @var Connection $connection
     */
    protected $connection = null;

    /**
     * The querybuilder
     *
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder = null;

    /**
     * The enable fields for this repository for manual queries
     *
     * @var string
     */
    protected $enableFields = '';

    /**
     * Initialize this repository
     */
    public function initializeObject()
    {
        $this->enableFields = $this->objectManager->get(PageRepository::class)->enableFields($this->getTableName());
        $connectionPool     = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->queryBuilder = $connectionPool->getQueryBuilderForTable($this->getTableName());
        $this->connection   = $connectionPool->getConnectionForTable($this->getTableName());
    }

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->objectManager->get(DataMapper::class)->convertClassNameToTableName($this->objectType);
    }

    /**
     * Get enable fields
     * 
     * @return string
     */
    public function getEnableFields()
    {
        return $this->enableFields;
    }

    /**
     * get a instance of this class
     * 
     * @return static::class
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(static::class);
    }
}