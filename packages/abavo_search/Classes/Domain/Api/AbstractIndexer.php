<?php
/*
 * abavo_search
 *
 * @copyright   2018 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

use Abavo\AbavoSearch\Indexers\BaseIndexer;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use Abavo\AbavoSearch\Domain\Model\Index;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Controller\ApiController;
use Abavo\AbavoSearch\User\LanguageUtility;

/**
 * AbstractIndexer
 *
 * @author mbruckmoser
 */
abstract class AbstractIndexer extends BaseIndexer
{
    public const INDEX_PROPERTIES = ['title', 'content', 'abstract'];
    public const PAGE_TYPENUM     = 180617;

    /**
     * @var ObjectManager 
     */
    protected $objectManager = null;

    /**
     * @var LanguageUtility
     */
    protected $languageUtility = null;

    /**
     * the API url
     *
     * @var string
     */
    protected $apiUrl = '';

    /**
     * @var Indexer
     */
    protected $indexer = null;

    /**
     * The JsonView configuration
     * @var ConfigurationInterface
     */
    protected $jsonViewConfiguration = null;

    /**
     * the referenceTable
     *
     * @var string
     */
    protected $referenceTable = null;

    /**
     * The constructor
     */
    public function __construct($settings)
    {
        parent::__construct($settings);

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
        // Switch differnt ways to get JSON response by application context
        if (Environment::getContext()->isDevelopment()) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            if (strlen(curl_error($ch))) {
                throw new \Exception(curl_error($ch));
            }

            $response = curl_exec($ch);
        } else {
            $response = GeneralUtility::getUrl($this->apiUrl);
        }

        // Validate JSON response and decode it if possible
        if ($this->isJson($response)) {
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
        if (VersionNumberUtility::convertVersionNumberToInteger(GeneralUtility::makeInstance(Typo3Version::class)->getVersion()) >= 9_005_000) {
            /**
             * @var UriBuilder
             */
            $uriBuilder = $this->objectManager->get(UriBuilder::class);

            $this->apiUrl = $uriBuilder->setTargetPageUid($this->indexer->getTarget())
                ->setTargetPageType(self::PAGE_TYPENUM)
                ->setCreateAbsoluteUri(true)
                ->setArguments(['L' => $languageUid, 'tx_abavosearch_pidata' => ['indexer' => $this->indexer->getUid()]])
                ->buildFrontendUri();
        } else {
            // BaseUrl
            if (($baseUrl = getenv('TYPO3_BASE_URL')) !== false) {
                $url = $baseUrl.'index.php?id='.(int) $this->indexer->getTarget();
            } else {
                throw new \Exception('The constant TYPO3_BASE_URL in your .env is undefined.');
            }

            // PageType
            $urlParts['type'] = self::PAGE_TYPENUM;

            // Language
            $urlParts['L'] = (int) $languageUid;

            // Query
            $urlParts['tx_abavosearch_pidata'] = [
                'indexer' => $this->indexer->getUid()
            ];

            // Validate/set apiUrl
            if (filter_var($url .= GeneralUtility::implodeArrayForUrl('', $urlParts, '', true, true), FILTER_VALIDATE_URL)) {
                $this->apiUrl = $url;
            } else {
                throw new \Exception("Invalid api url: $url");
            }
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

        if (!$this->indexer = $indexer) {
            throw new IndexException(__METHOD__.': No indexer given.');
        }

        // get data
        if ($sysLanguageUids = GeneralUtility::intExplode(',', $this->settings['language'], true)) {

            // System-Languages
            $languages = $this->languageUtility->getLanguages();

            // Work each sys_language_uid
            foreach ($sysLanguageUids as $sysLanguageUid) {

                // Setup ApiUrl
                $this->setApiUrlByLanguange($sysLanguageUid);

                // Requesting API
                $records = $this->requestApiData();

                // Init jsonViewConfiguration
                if ($this->jsonViewConfiguration === null) {
                    if (isset($records->data->jsonViewConfigurationClass) && Utility::isJsonViewConfigurationClassValid($records->data->jsonViewConfigurationClass)) {
                        $this->jsonViewConfiguration = GeneralUtility::makeInstance($records->data->jsonViewConfigurationClass);
                    } else {
                        throw new \Exception('Unable to instance JsonViewConfiguration');
                    }
                }

                // Get referenceTable value
                if ($this->referenceTable === null) {
                    if (property_exists($repository = $this->jsonViewConfiguration->getRepositoryBackend()->getRepository(), 'objectType')) {
                        $this->referenceTable = $this->objectManager->get(DataMapper::class)->convertClassNameToTableName(ObjectAccess::getProperty(
                                $repository, 'objectType'
                        ));
                    } else {
                        throw new \Exception('Unable to determind referenceTable');
                    }
                }

                /*
                 *  Create index data
                 */
                if (isset($records->data->items) && is_array($records->data->items) && !empty($records->data->items)) {

                    $identiferProperty = $this->jsonViewConfiguration->getIndexConfiguration()->getIdentifer();

                    array_walk($records->data->items,
                        function($recordRaw) use ($indexer, $languages, $sysLanguageUid, $refTable, $identiferProperty) {

                        // Index-Modify-Hook
                        $this->modifyIndexHook($this->getTypeKey(), $recordRaw, $title, $content, $abstract);

                        // Identifier                        
                        if (isset($recordRaw->{$identiferProperty})) {
                            $identifer = $recordRaw->{$identiferProperty};
                        } else {
                            throw new \Exception('No identifer value given in result');
                        }

                        /*
                         *  Make Index Object
                         */
                        $tempIndex = Index::getInstance()
                            ->setTarget($indexer->getTarget())
                            ->setParams($this->parseParamsForRecordRaw($recordRaw))
                            ->setSysLanguageUid($recordRaw->sysLanguageUid ?? $sysLanguageUid)
                            ->setRefid($this->referenceTable.self::REFID_SEPERARTOR.$identifer);

                        foreach (self::INDEX_PROPERTIES as $property) {
                            $tempIndex->{'set'.ucfirst($property)}($this->getIndexPropertyValueByNameForRecordRaw($property, $recordRaw, $sysLanguageUid));
                        }

                        // FE GROUP
                        $feGroup = self::FE_GROUP_DEFAULT;
                        if (isset($recordRaw->feGroup) && trim($recordRaw->feGroup)) {
                            $feGroup = $recordRaw->feGroup;
                        }
                        $tempIndex->setFegroup($feGroup);

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

    /**
     * Get index property value by name
     * 
     * @param string $name
     * @param int $sysLanguageUid
     * @return string
     */
    private function getIndexPropertyValueByNameForRecordRaw(string $name, \stdClass $recordRaw = null, int $sysLanguageUid = 0)
    {
        if (!($indexConfiguration = $this->jsonViewConfiguration->getIndexConfiguration()) instanceof IndexConfiguration) {
            throw new \Exeception('Unable to get instance of IndexConfiguration from JsonViewConfiguration');
        }

        // define vars
        $getter    = 'get'.ucfirst($name);
        $conf      = $indexConfiguration->{$getter}();
        if ($languages = $this->languageUtility->getLanguages()) {
            $isoCodeLang = $languages[$sysLanguageUid]->getIsoCodeA2();
        }


        // Make field values
        if (isset($conf['fields'])) {

            $tempArray = [];
            foreach ($conf['fields'] as $field) {
                if (isset($recordRaw->{$field}) && (boolean) trim($recordRaw->{$field})) {

                    // Special case: field "abstract"
                    $value = '';
                    if (isset($conf['langfile']) && $isoCodeLang) {
                        $labelKey = $conf['langfile'].':'.$this->referenceTable.'.'.GeneralUtility::camelCaseToLowerCaseUnderscored($field);
                        if ($value    = $this->languageUtility->translate($labelKey, $isoCodeLang)) {
                            $value .= ': ';
                        }
                    }
                    $value .= $recordRaw->{$field};

                    $tempArray[] = $value;
                }
            }

            $separator = (count($tempArray) > 1 && isset($conf['separator'])) ? $conf['separator'] : '';

            return strip_tags(implode($separator, $tempArray)); #return preg_replace('!\s+!', ' ', strip_tags(implode($separator, $tempArray)));
        }
    }

    /**
     * Parse params for record raw
     * 
     * @param \stdClass $recordRaw
     * @return string
     */
    private function parseParamsForRecordRaw(\stdClass $recordRaw)
    {
        $params = $this->jsonViewConfiguration->getIndexConfiguration()->getParams();

        foreach ((array) $recordRaw as $field => $value) {
            if (!is_object($value) && !is_array($value)) {
                $params = str_replace('{FIELD_'.strtoupper($field).'}', $value, $params);
            }
        }

        return $params;
    }
}