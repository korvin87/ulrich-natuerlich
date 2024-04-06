<?php
/**
 * ulrich_products - SearchTermService.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 05.06.2018 - 11:11:48
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Model\Product;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use Abavo\UlrichProducts\Utility\GetInstanceStaticTrait;
use Abavo\UlrichProducts\Utility\ConfigHelper;
use Abavo\UlrichProducts\Domain\Repository\ProductRepository;
use Abavo\AbavoSearch\Domain\Model\AutocompleteItem;

/**
 * SearchTermService
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class SearchTermService implements SingletonInterface
{
    /**
     * @var ProductRepository
     */
    protected $productRepository = null;

    /**
     * @var UriBuilder 
     */
    protected $uriBuilder = null;

    /**
     * @var array
     */
    protected $tsSetup = [];

    /**
     * Get instance trait
     */
    use GetInstanceStaticTrait;

    /**
     * the constructor
     */
    public function __construct()
    {
        $this->tsSetup = ConfigHelper::getInstance()->setup;

        $this->productRepository = ProductRepository::getInstance();
        if (isset($this->tsSetup['plugin']['tx_ulrichproducts']['persistence']['storagePid'])) {
            $this->productRepository->setDefaultQuerySettings($this->productRepository->createQuery()->getQuerySettings()->setStoragePageIds(
                    GeneralUtility::intExplode(',', $this->tsSetup['plugin']['tx_ulrichproducts']['persistence']['storagePid']))
            );
        }

        $this->uriBuilder = GeneralUtility::makeInstance(ObjectManager::class)->get(UriBuilder::class);
    }

    /**
     * Find for term autocomplete method
     *
     * @param array $settings
     * @return array
     * @throws \Exception
     */
    public function findForAutocomplete($settings = [])
    {
        // defaults
        $return = [];
        $term   = strtolower(trim($settings['term']));

        if (strlen($settings['term']) >= 3) {

            $products = $this->productRepository->findForSearchTermService($settings['term'], $GLOBALS['TSFE']->sys_language_uid);

            if ($products->count()) {
                foreach ($products as $product) {

                    // Generate Label
                    $label           = $product->getTitle();
                    $additionalLabel = [];
                    if ($product->getCasNumber()) {
                        $additionalLabel[] .= 'CAS: '.$product->getCasnumber();
                    }
                    if ($product->getInci()) {
                        $additionalLabel[] .= 'INCI: '.$product->getInci();
                    }
                    if ($product->getEgNumber()) {
                        $additionalLabel[] .= 'EG: '.$product->getEgNumber();
                    }
                    if (count($additionalLabel)) {
                        $label .= ' ('.implode(', ', $additionalLabel).')';
                    }

                    if ($category = $product->getCategories()->current()) {
                        $uri = $this->uriBuilder->setTargetPageUid($this->tsSetup['plugin']['tx_ulrichproducts']['settings']['productPiPid'])
                            ->uriFor('show', ['category' => $category->getLocalizedUid(), 'product' => $product->getUid()], 'Product', 'UlrichProducts');
                    }

                    $item = new AutocompleteItem($label, $product->getTitle(), $data, $uri); //$product->getTitle();
                    /*
                      // Possible feature, but keep how to keep history clean?
                      if (strpos(strtolower($product->getTitle()), $settings['term']) === false) {
                      $item .= ' '.trim(strip_tags($settings['term']));
                      }
                     */

                    $return[] = $item;
                }
            }
        }

        return $return;
    }
}
