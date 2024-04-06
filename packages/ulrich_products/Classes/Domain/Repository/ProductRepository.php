<?php
/**
 * Projekt (EXT:ulrich_products) - ProductRepository.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 23.05.2018 - 16:19:00
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 *
 */

namespace Abavo\UlrichProducts\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use Abavo\UlrichProducts\Domain\Model\Product;
use Abavo\UlrichProducts\Domain\Model\Product\ApiQuery;
use Abavo\UlrichProducts\Domain\Model\Category;

/**
 * The repository for Products
 */
class ProductRepository extends StdRepository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
        'casNumber' => QueryInterface::ORDER_ASCENDING,
        'egNumber' => QueryInterface::ORDER_ASCENDING
    ];

    /**
     * Find for API Query
     *
     * @param ApiQuery $apiQuery
     * @return ObjectStorage
     */
    public function findForApiQuery(ApiQuery $apiQuery)
    {
        // Create query
        $query  = $this->createQuery();
        $result = new ObjectStorage;

        // Constraints
        $constraints = [];
        if ($apiQuery->getCategory()) {
            $constraints[] = $query->contains('categories', $apiQuery->getCategory());
        }

        if ($apiQuery->getBranch()) {
            $constraints[] = $query->contains('branch', $apiQuery->getBranch());
        }

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        $resultArray = $query->execute()->toArray();
        if (count($resultArray)) {
            uasort($resultArray, fn($a, $b) => $a->getTitle() <=> $b->getTitle());

            // Filter by first character
            $filteredArray = [];
            if ($apiQuery->getChar()) {
                foreach ($resultArray as $product) {
                    if (strncmp($product->getTitle(), $apiQuery->getChar(), 1) === 0) {
                        $filteredArray[] = $product;
                    }
                }
                $resultArray = $filteredArray;
            }

            // Extract result by offset/limit
            if ($apiQuery->getLimit() !== 0) {
                $resultArray = (count($resultArray)) ? array_splice($resultArray, $apiQuery->getOffset(), $apiQuery->getLimit()) : [];
            }
        }

        if (count($resultArray)) {
            foreach ($resultArray as $product) {
                $result->attach($product);
            }
        }

        // Return result
        return $result;
    }

    /**
     * Find by category and branch
     *
     * @param Category $category
     * @param int $branch
     * @return type
     */
    public function findByCategoryAndBranch(Category $category, $branch = null)
    {
        // Create query
        $query = $this->createQuery();

        // Constraints
        $constraints = [];
        if (isset($category)) {
            $constraints[] = $query->contains('categories', $category);
        }

        if (isset($branch)) {
            $constraints[] = $query->contains('branch', $branch);
        }

        if (count($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        // Execute query
        return $query->execute();
    }

    /**
     * Find for SearchTermService
     *
     * @param string $term
     * @param int $languageUid
     * @return QueryResult<Product>
     */
    public function findForSearchTermService($term = '', $languageUid = 0)
    {
        // Create query
        $query = $this->createQuery()->setLimit(20);

        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectSysLanguage(false);


        // Constraints
        $constraints = [];
        if ($term) {
            $constraints[] = $query->like('title', '%'.$term.'%', false);
            $constraints[] = $query->like('casNumber', '%'.$term.'%', false);
            $constraints[] = $query->like('egNumber', '%'.$term.'%', false);
            $constraints[] = $query->like('inci', '%'.$term.'%', false);
        }

        if (count($constraints)) {
            $query->matching(
                $query->logicalAnd([$query->logicalOr($constraints), $query->logicalOr([$query->equals('sys_language_uid', (int)$languageUid), $query->equals('sys_language_uid', -1)])])
            );
        }

        $results = $query->execute();

        // Execute query
        return $results;
    }
}
