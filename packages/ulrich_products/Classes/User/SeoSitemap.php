<?php
/**
 * ulrich_products - SeoSitemap.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 05.06.2018 - 09:04:42
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\User;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use Clickstorm\CsSeo\UserFunc\Sitemap;
use Abavo\UlrichProducts\Utility\ConfigHelper;
use Abavo\UlrichProducts\Domain\Repository\ProductRepository;
use Abavo\UlrichProducts\Domain\Model\Product;

/**
 *  SeoSitemap userFunc class
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class SeoSitemap implements SingletonInterface
{
    /**
     * @var array
     */
    protected $tsSetup = [];

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->tsSetup = ConfigHelper::getInstance()->setup;
    }

    /**
     * Get products method for
     * 
     * EXT:site_package/Configuration/TypoScript/setup/plugin.ts
     * plugin.tx_csseo.sitemap.extensions.products.getRecordsUserFunction
     */
    public function getProducts(array $extConf = [], Sitemap $sitemap)
    {
        // define template
        $productItemTemplate = ['uid' => null, 'lang' => ObjectAccess::getProperty($sitemap, 'tsfe')->sys_language_uid];

        // init repository
        $productRepository = ProductRepository::getInstance();
        if (isset($this->tsSetup['plugin']['tx_ulrichproducts']['persistence']['storagePid'])) {
            $productRepository->setDefaultQuerySettings($productRepository->createQuery()->getQuerySettings()->setStoragePageIds(
                    GeneralUtility::intExplode(',', $this->tsSetup['plugin']['tx_ulrichproducts']['persistence']['storagePid']))
            );
        }

        // Create result
        if ($products = $productRepository->findAll()) {

            $result = [];
            $items  = $products->toArray();
            array_walk($items,
                function($product) use (&$result, $productItemTemplate) {

                if ($product instanceof Product) {
                    $result[$product->getUid()]        = $productItemTemplate;
                    $result[$product->getUid()]['uid'] = $product->getUid().'&tx_ulrichproducts_pi[category]='.$product->getCategories()->current()->getLocalizedUid();
                }
            });

            return $result;
        }
    }
}