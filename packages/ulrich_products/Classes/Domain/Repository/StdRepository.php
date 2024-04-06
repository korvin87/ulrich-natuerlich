<?php
/**
 * ulrich_products - StdRepository.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 30.05.2018 - 10:37:11
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use Abavo\UlrichProducts\Utility\GetInstanceStaticTrait;

/**
 * StdRepository
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class StdRepository extends Repository
{

    use GetInstanceStaticTrait;
    /**
     * The querybuilder
     *
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder = null;

    /**
     * Initialize this repository
     */
    public function initializeObject()
    {
        $this->queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->getTableName());
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
     * convert queryBuilder´s result to a result with model class method
     *
     * @return array
     */
    public function convertQueryBuilderResult()
    {
        return $this->getDataMapper()->map($this->objectType, $this->queryBuilder->execute()->fetchAll());
    }
}
