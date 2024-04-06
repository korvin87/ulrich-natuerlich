<?php
/**
 * ulrich_products - Category.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 01.06.2018 - 09:19:18
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Configuration;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Abavo\UlrichProducts\Domain\Repository\CategoryRepository;
use Abavo\UlrichProducts\Utility\ConfigHelper;
use Abavo\UlrichProducts\Domain\Repository\ProductRepository;
use Abavo\UlrichProducts\Domain\Model\Product;

/**
 * Category userFunc class
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class Category implements SingletonInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var array
     */
    protected $tsSetup = [];

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->tsSetup       = ConfigHelper::getInstance()->setup;
    }

    /**
     * make a category menu
     * 
     * https://docs.typo3.org/typo3cms/TyposcriptReference/ContentObjects/Hmenu/Index.html#example-creating-hierarchical-menus-of-custom-links
     * 
     * @param array $content
     * @param array $conf
     * @return array
     */
    public function getMenu($content, $conf)
    {
        $menuArray = [];

        // Init Repository
        $categoryRepository = CategoryRepository::getInstance();
        if (isset($conf['_altSortField'])) {
            $categoryRepository->setDefaultOrderings([$conf['_altSortField'] => QueryInterface::ORDER_ASCENDING]);
        }

        if ($categories = $categoryRepository->findByPid($this->tsSetup['plugin']['tx_ulrichproducts']['persistence']['storagePid'])->toArray()) {

            // Init cObj
            $uriBuilder         = $this->objectManager->get(UriBuilder::class)->setTargetPageUid($this->tsSetup['plugin']['tx_ulrichproducts']['settings']['productPiPid']);
            $currentCategoryUid = (int) GeneralUtility::_GP('tx_ulrichproducts_pi')['category'] ?? null;

            // Generate menu items
            array_walk($categories,
                function($category) use (&$menuArray, $uriBuilder, $currentCategoryUid) {
                $menuArray[] = [
                    'title' => $category->getTitle(),
                    '_OVERRIDE_HREF' => $uriBuilder->uriFor('list', ['category' => $category->getLocalizedUid()], 'Product', 'UlrichProducts', 'pi'),
                    'ITEM_STATE' => ($currentCategoryUid === $category->getLocalizedUid()) ? 'ACT' : 'NO',
                    '_SAFE' => true // set for multilang use; depends on https://stackoverflow.com/questions/34941181/typo3-hmenu-not-working-in-foreign-language#answer-34960709
                ];
            });
        }

        return $menuArray;
    }

    /**
     * Get category uid by product uid method
     * 
     * EXT:site_package/Configuration/TypoScript/setup/config.ts
     * config.recordLinks.tx_ulrichproducts.typolink.additionalParams.preCObject.20
     * 
     * @param array $content
     * @param array $conf
     * @return int
     */
    public function getCategoryUidByProductUid($content = null, $conf = [])
    {
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        if ($productUid = $cObj->cObjGetSingle($conf['10'], $conf['10.'])) {
            if (($product = ProductRepository::getInstance()->findByUid($productUid)) instanceof Product) {
                if ($product->getCategories()->count()){
                    return $product->getCategories()->current()->getLocalizedUid();
                }
            }
        }
    }
}