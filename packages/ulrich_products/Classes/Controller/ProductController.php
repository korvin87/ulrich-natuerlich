<?php
/**
 * Projekt (EXT:ulrich_products) - ProductController.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 23.05.2018 - 16:19:00
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Controller;

use Psr\Http\Message\ResponseInterface;
use Abavo\UlrichProducts\Domain\Repository\ProductRepository;
use TYPO3\CMS\Extbase\Property\TypeConverter;
use Abavo\UlrichProducts\Domain\Model\Category;
use Abavo\UlrichProducts\Domain\Model\Product\ApiQuery;
use Abavo\UlrichProducts\Domain\Model\Product\Filter;
use Abavo\UlrichProducts\Domain\Model\Product;

/**
 * ProductController
 */
class ProductController extends ActionController
{
    /**
     * productRepository
     *
     * @var ProductRepository
     */
    protected $productRepository = null;

    /**
     * action list
     *
     * @param Category $category The sys_category
     * @param int $branch the branch (pageUid)
     * @return void
     */
    public function listAction(Category $category = null, $branch = null): ResponseInterface
    {
        // Set cacheIdentifier
        $cacheIdentifier = $this->makeCacheIdentifier($branch.'|'.(($category) ? $category->getLocalizedUid() : 0));
        if ($this->cacheInstance->has($cacheIdentifier)) {
            // Cache exist, get $variables
            $this->variables = $this->cacheInstance->get($cacheIdentifier);
        } else {
            // Init objects
            $this->variables['category']      = $category;
            $this->variables['apiQuery']      = ApiQuery::getInstance($category, $branch);
            $this->variables['productFilter'] = Filter::getInstance();

            // Get products count for each character
            $products = ($category instanceof Category) ? $this->productRepository->findByCategoryAndBranch($category, $branch) : $this->productRepository->findAll();
            if ($products) {
                foreach ($products as $product) {
                    $char = mb_substr($product->getTitle(), 0, 1);
                    $this->variables['productFilter']->updateMenu($char);
                }
            }

            // Safe variables in cache
            $this->cacheInstance->set($cacheIdentifier, $this->variables, [$this->actionMethodName]);
        }

        // Assign data to view
        $this->view->assignMultiple($this->variables);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param Category $category The sys_category
     * @param Product $product
     * @return void
     */
    public function showAction(Category $category = null, Product $product): ResponseInterface
    {
        $this->variables['category'] = $category;
        $this->variables['product']  = $product;
        $this->view->assignMultiple($this->variables);
        return $this->htmlResponse();
    }

    public function injectProductRepository(ProductRepository $productRepository): void
    {
        $this->productRepository = $productRepository;
    }
}