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
                        $uid      = $productRaw->uid;
                        $title    = $productRaw->title;
                        $content  = $productRaw->description;
                        $abstract = '';

                        $isoCodeLang = $languages[$sysLanguageUid]->getIsoCodeA2();

                        if (trim($productRaw->appearance)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.appearance', $isoCodeLang).': '.$productRaw->appearance.PHP_EOL;
                        }
                        if (trim($productRaw->casNumber)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.cas_number', $isoCodeLang).': '.$productRaw->casNumber.PHP_EOL;
                        }
                        if (trim($productRaw->egNumber)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.eg_number', $isoCodeLang).': '.$productRaw->egNumber.PHP_EOL;
                        }
                        if (trim($productRaw->granulation)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.granulation', $isoCodeLang).': '.$productRaw->granulation.PHP_EOL;
                        }
                        if (trim($productRaw->bstbefo)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.bestbefor', $isoCodeLang).': '.$productRaw->bstbefor.PHP_EOL;
                        }
                        if (trim($productRaw->qualities)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.qualities', $isoCodeLang).': '.$productRaw->qualities.PHP_EOL;
                        }
                        if (trim($productRaw->spec)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.spec', $isoCodeLang).': '.$productRaw->spec.PHP_EOL;
                        }
                        if (trim($productRaw->physicalState)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.physical_state', $isoCodeLang).': '.$productRaw->physicalState.PHP_EOL;
                        }
                        if (trim($productRaw->chemicalProperties)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.chemical_properties', $isoCodeLang).': '.$productRaw->chemicalProperties.PHP_EOL;
                        }
                        if (trim($productRaw->molecularFormula)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.molecular_formula', $isoCodeLang).': '.$productRaw->molecularFormula.PHP_EOL;
                        }
                        if (trim($productRaw->chemicalName)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.chemical_name', $isoCodeLang).': '.$productRaw->chemicalName.PHP_EOL;
                        }
                        if (trim($productRaw->registration)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.registration', $isoCodeLang).': '.$productRaw->registration.PHP_EOL;
                        }
                        if (trim($productRaw->eNumber)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.e_number', $isoCodeLang).': '.$productRaw->eNumber.PHP_EOL;
                        }
                        if (trim($productRaw->grassState)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.grass_state', $isoCodeLang).': '.$productRaw->grassState.PHP_EOL;
                        }
                        if (trim($productRaw->container)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.container', $isoCodeLang).': '.$productRaw->container.PHP_EOL;
                        }
                        if (trim($productRaw->inci)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.inci', $isoCodeLang).': '.$productRaw->inci.PHP_EOL;
                        }
                        if (trim($productRaw->einecs)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.einecs', $isoCodeLang).': '.$productRaw->einecs.PHP_EOL;
                        }
                        if (trim($productRaw->meltingPoint)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.melting_point', $isoCodeLang).': '.$productRaw->meltingPoint.PHP_EOL;
                        }
                        if (trim($productRaw->durability)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.durability', $isoCodeLang).': '.$productRaw->durability.PHP_EOL;
                        }
                        if (trim($productRaw->storage)) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.qualities', $isoCodeLang).': '.$productRaw->storage.PHP_EOL;
                        }
                        if ($productRaw->originCountry) {
                            $abstract .= $this->languageUtility->translate('tx_ulrichproducts_domain_model_product.origin_country', $isoCodeLang).': '.$productRaw->originCountry->nameLocalized;
                        }

                        // Index-Modify-Hook
                        $this->modifyIndexHook($this->getTypeKey(), $result, $title, $content, $abstract);

                        // Make Index Object
                        $tempIndex = Index::getInstance();
                        $tempIndex->setTitle(strip_tags($title));
                        $tempIndex->setContent(preg_replace('!\s+!', ' ', strip_tags($content)));
                        $tempIndex->setAbstract(preg_replace('!\s+!', ' ', strip_tags($abstract)));
                        $tempIndex->setTarget($indexer->getTarget());
                        $tempIndex->setParams('&tx_ulrichproducts_pi[category]='.current($productRaw->categories)->localizedUid.'&tx_ulrichproducts_pi[product]='.$uid);
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
