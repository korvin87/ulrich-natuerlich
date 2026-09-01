<?php
/*
 * ulrich_products
 *
 * @copyright   2018 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\UlrichProducts\Domain\Model\Product;

use Abavo\AbavoSearch\Indexers\BaseIndexer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use Abavo\AbavoSearch\Domain\Model\Index;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\UlrichProducts\Controller\ApiController;
use Abavo\UlrichProducts\Domain\Model\Product;
use Abavo\UlrichProducts\Utility\LanguageUtility;
use Abavo\UlrichProducts\Utility\ConfigHelper;

/**
 * SearchIndexer
 *
 * @author mbruckmoser
 */
class SearchIndexer extends BaseIndexer
{
    public const CONFIG_FLEXFORM           = 'FILE:EXT:ulrich_products/Configuration/FlexForms/ProductIndexerConfig.xml';
    public const CONFIG_PRODUCT_PROPERTIES = 'uid, title, description, categories, media, contact, uri, appearance, casNumber, egNumber, granulation, bstbefor, qualities, originCountry, spec, physical_state, chemical_properties, molecular_formula, chemical_name, registration, e_number, grass_state, container, inci, einecs, melting_point, durability, storage';
    public const API_URL_PAGETYPENUM       = 2505;

    /**
     * @var ObjectManager 
     */
    protected $objectManager = null;

    /**
     * @var LanguageUtility
     */
    protected $languageUtility = null;

    /**
     * @var array
     */
    protected $tsSetup = [];

    /**
     * the API url
     *
     * @var string
     */
    protected $apiUrl = '';

    /**
     * The constructor
     */
    public function __construct($settings)
    {
        $this->settings = $settings;

        $this->objectManager   = GeneralUtility::makeInstance(ObjectManager::class);
        $this->languageUtility = LanguageUtility::getInstance();
    }

    /**
     * Requesting API data
     * 
     * @return \stdClass
     * @throws \Exception
     */
    private function requestApiData()
    {
        if ($this->isJson($response = GeneralUtility::getUrl($this->apiUrl))) {
            $responseData = json_decode($response, null, 512, JSON_THROW_ON_ERROR);
            if (isset($responseData->status) && $responseData->status === ApiController::STATE_OK) {
                return $responseData;
            } else {
                throw new \Exception($responseData->message ?? 'Undefined error on requesting JSON data.');
            }
        } else {
            throw new \Exception("The request on $this->apiUrl has no valid JSON data.");
        }
    }

    /**
     * set the full API-Url
     * 
     * @return string
     * @throws \Exception
     */
    private function setApiUrlByLanguange($languageUid = 0)
    {
        $urlParts = [];
        // BaseUrl
        $url = getenv('TYPO3_BASE_URL').'index.php?id=1';

        // PageType
        $urlParts['type'] = self::API_URL_PAGETYPENUM;

        // Language
        $urlParts['L'] = (int) $languageUid;

        // Query
        $urlParts['tx_ulrichproducts_api'] = [
            'query' => [
                'char' => '',
                'category' => 0,
                'limit' => 0,
                'offset' => 0,
                'productProperties' => self::CONFIG_PRODUCT_PROPERTIES
            ]
        ];

        // Validate/set apiUrl
        if (filter_var($url .= GeneralUtility::implodeArrayForUrl('', $urlParts, '', true, true), FILTER_VALIDATE_URL)) {
            return $this->apiUrl = $url;
        } else {
            throw new \Exception('TypoScript setup plugin.tx_ulrichproducts.settings.pageTypeApi.products not set.');
        }
    }

    /**
     * Check if string is a valid JSON string
     * 
     * @param string $JsonString
     * @return boolean
     */
    private function isJson($JsonString)
    {
        json_decode($JsonString);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $timeStart = microtime(true);

        if (!$indexer) {
            throw new IndexException(__METHOD__.': No indexer given.');
        }

        // get data
        if ($sysLanguageUids = GeneralUtility::intExplode(',', $this->settings['language'], true)) {

            // System-Languages
            $languages = $this->languageUtility->getLanguages();

            // Work each sys_language_uid
            foreach ($sysLanguageUids as $sysLanguageUid) {

                $this->setApiUrlByLanguange($sysLanguageUid);
                $produts = $this->requestApiData();

                if (isset($produts->data->items) && is_array($produts->data->items) && !empty($produts->data->items)) {

                    array_walk($produts->data->items,
                        function($productRaw) use ($indexer, $languages, $sysLanguageUid) {
                        /*
                         * STEP 2: DEFINE INDEX DATA
                         */
                        // Raw stdClass from the HTTP API — treat every property
                        // as optional to avoid warning-to-exception fatals.
                        $uid      = $productRaw->uid ?? null;
                        $title    = $productRaw->title ?? '';
                        $content  = $productRaw->description ?? '';
                        $abstract = '';

                        // $languages[$sysLanguageUid] can be missing (no static_languages
                        // row matching config.locale_all for uid 0, or a sys_language
                        // row without a static_lang_isocode FK) — fall back to an
                        // empty ISO code rather than fatal on null->getIsoCodeA2().
                        $isoCodeLang = (isset($languages[$sysLanguageUid]) && is_object($languages[$sysLanguageUid]))
                            ? $languages[$sysLanguageUid]->getIsoCodeA2()
                            : '';

                        if (trim((string)($productRaw->appearance ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.appearance', $isoCodeLang).': '.$productRaw->appearance.PHP_EOL;
                        }
                        if (trim((string)($productRaw->casNumber ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.cas_number', $isoCodeLang).': '.$productRaw->casNumber.PHP_EOL;
                        }
                        if (trim((string)($productRaw->egNumber ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.eg_number', $isoCodeLang).': '.$productRaw->egNumber.PHP_EOL;
                        }
                        if (trim((string)($productRaw->granulation ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.granulation', $isoCodeLang).': '.$productRaw->granulation.PHP_EOL;
                        }
                        if (trim((string)($productRaw->bstbefor ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.bestbefor', $isoCodeLang).': '.$productRaw->bstbefor.PHP_EOL;
                        }
                        if (trim((string)($productRaw->qualities ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.qualities', $isoCodeLang).': '.$productRaw->qualities.PHP_EOL;
                        }
                        if (trim((string)($productRaw->spec ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.spec', $isoCodeLang).': '.$productRaw->spec.PHP_EOL;
                        }
                        if (trim((string)($productRaw->physicalState ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.physical_state', $isoCodeLang).': '.$productRaw->physicalState.PHP_EOL;
                        }
                        if (trim((string)($productRaw->chemicalProperties ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.chemical_properties', $isoCodeLang).': '.$productRaw->chemicalProperties.PHP_EOL;
                        }
                        if (trim((string)($productRaw->molecularFormula ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.molecular_formula', $isoCodeLang).': '.$productRaw->molecularFormula.PHP_EOL;
                        }
                        if (trim((string)($productRaw->chemicalName ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.chemical_name', $isoCodeLang).': '.$productRaw->chemicalName.PHP_EOL;
                        }
                        if (trim((string)($productRaw->registration ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.registration', $isoCodeLang).': '.$productRaw->registration.PHP_EOL;
                        }
                        if (trim((string)($productRaw->eNumber ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.e_number', $isoCodeLang).': '.$productRaw->eNumber.PHP_EOL;
                        }
                        if (trim((string)($productRaw->grassState ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.grass_state', $isoCodeLang).': '.$productRaw->grassState.PHP_EOL;
                        }
                        if (trim((string)($productRaw->container ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.container', $isoCodeLang).': '.$productRaw->container.PHP_EOL;
                        }
                        if (trim((string)($productRaw->inci ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.inci', $isoCodeLang).': '.$productRaw->inci.PHP_EOL;
                        }
                        if (trim((string)($productRaw->einecs ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.einecs', $isoCodeLang).': '.$productRaw->einecs.PHP_EOL;
                        }
                        if (trim((string)($productRaw->meltingPoint ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.melting_point', $isoCodeLang).': '.$productRaw->meltingPoint.PHP_EOL;
                        }
                        if (trim((string)($productRaw->durability ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.durability', $isoCodeLang).': '.$productRaw->durability.PHP_EOL;
                        }
                        if (trim((string)($productRaw->storage ?? ''))) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.qualities', $isoCodeLang).': '.$productRaw->storage.PHP_EOL;
                        }
                        if (!empty($productRaw->originCountry)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.origin_country', $isoCodeLang).': '.($productRaw->originCountry->nameLocalized ?? '');
                        }

                        // Index-Modify-Hook
                        $this->modifyIndexHook($this->getTypeKey(), $result, $title, $content, $abstract);

                        // Products with no title cannot be indexed — the index
                        // table has a NOT NULL constraint on `title`. Skip.
                        if (trim(strip_tags((string)$title)) === '') {
                            return;
                        }

                        // Make Index Object
                        $tempIndex = Index::getInstance();
                        $tempIndex->setTitle(strip_tags($title));
                        $tempIndex->setContent(preg_replace('!\s+!', ' ', strip_tags($content)));
                        $tempIndex->setAbstract(preg_replace('!\s+!', ' ', strip_tags($abstract)));
                        $tempIndex->setTarget($indexer->getTarget());
                        // The API renders categories via Extbase JsonView's
                        // _descendAll on an ObjectStorage — the JSON payload
                        // is an *object* keyed by ObjectStorage hashes, not a
                        // plain array. is_array() alone missed that shape and
                        // left every product's params without a category, so
                        // the route generator refused to build the URL
                        // (category segment requires .+).
                        $categoriesRaw = $productRaw->categories ?? null;
                        if (is_object($categoriesRaw)) {
                            $categoriesRaw = get_object_vars($categoriesRaw);
                        }
                        $firstCategory = is_array($categoriesRaw) && $categoriesRaw !== [] ? reset($categoriesRaw) : null;
                        $categoryUid = is_object($firstCategory) ? ($firstCategory->localizedUid ?? '') : (is_array($firstCategory) ? ($firstCategory['localizedUid'] ?? '') : '');

                        // Products without a resolvable category can't render
                        // a valid detail URL — skip them rather than persist a
                        // broken row that would blow up at link-build time.
                        if ($categoryUid === '' || (int)$categoryUid <= 0) {
                            return;
                        }

                        $tempIndex->setParams('&tx_ulrichproducts_pi[category]='.$categoryUid.'&tx_ulrichproducts_pi[product]='.$uid.'&tx_ulrichproducts_pi[action]=show&tx_ulrichproducts_pi[controller]=Product');
                        $tempIndex->setFegroup('0');
                        $tempIndex->setSysLanguageUid($sysLanguageUid);

                        /*
                         * STEP 3: DEFINE REFERENCE TABLE
                         */
                        $tempIndex->setRefid($this->objectManager->get(DataMapper::class)->convertClassNameToTableName(Product::class).self::REFID_SEPERARTOR.$uid);

                        //Set additional fields
                        $this->setAdditionalFields($tempIndex, $indexer);

                        // Add index to list
                        $this->data[] = $tempIndex;
                    });
                }
            }
        }


        // Set duration time
        $this->duration[$indexer->getUid()] = (microtime(true) - $timeStart);

        return $this->data;
    }
}
