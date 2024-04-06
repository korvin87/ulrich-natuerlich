<?php
/*
 * abavo_search
 * 
 * @copyright   2017 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Service;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use Abavo\AbavoSearch\User\SearchUtility;
use Abavo\AbavoSearch\Domain\Repository\IndexRepository;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SearchService
 *
 * @author mbruckmoser
 */
class SearchService
{
    /**
     * objectManager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * SearchUtility
     *
     * @var SearchUtility
     */
    protected $searchUtility;

    /**
     * indexRepository
     *
     * @var IndexRepository
     */
    protected $indexRepository;

    /**
     * The extension settings
     *
     * @var array
     */
    protected $settings = [];

    /**
     * The search conditions settings
     *
     * @var array
     */
    protected $searchConditions = [
        'search' => null,
        'start' => null,
        'length' => null,
        'L' => null,
        'feGroup' => null,
        'categories' => null,
        'facets' => null,
        'orderBy' => null,
        'sorting' => null
    ];

    /**
     * The result
     *
     * @var array
     */
    protected $result = [
        'result' => [],
        'queryCount' => 0,
        'queryTime' => 0,
        'allCount' => 0,
        'iteration' => null,
        'searchMethod' => ''
    ];

    /**
     * Is initialized
     */
    protected $isInitialized = false;

    /**
     * The hash for this class
     *
     * @var string
     */
    protected $classHash = '';

    /**
     * Constructor
     *
     * @param array $settings
     */
    public function __construct($settings = [], $searchConditions = [])
    {
        if (empty($settings)) {
            throw new \Exception('No settings for ' . self::class . ' given.');
        }

        // define basic settings
        $this->classHash = md5(get_class($this));
        $this->settings = $settings;
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->searchUtility = $this->objectManager->get(SearchUtility::class);

        // setup search conditions
        foreach (array_keys($this->searchConditions) as $condition) {
            if (!array_key_exists($condition, $searchConditions)) {
                throw new \Exception('search condition ' . $condition . ' not set');
            }
            $this->searchConditions[$condition] = $searchConditions[$condition];
        }

        // init completed
        $this->isInitialized = true;
        return $this;
    }

    /**
     * Is this class full initialized?
     *
     * @return boolean
     */
    public function isInitialized()
    {
        return $this->isInitialized;
    }

    /**
     * Get search results method
     *
     * Works all registrated search services and merge the results
     *
     * @return array
     * @throws \Exception
     */
    public function getSearchResults()
    {
        if (!$this->isInitialized()) {
            throw new \Exception(self::class . ' not correct initialized.');
        }

        // Work each registered service
        $serviceResults = [];
        $registeredServices = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['searchServiceClasses']);
        foreach ($registeredServices as $className) {

            // init service
            $searchServiceClass = $this->objectManager->get($className, $this->settings, $this->searchConditions);
            if (!method_exists($searchServiceClass, 'findForSearchService') && $searchServiceClass->isInitialized()) {
                throw new \Exception($searchServiceClass . ' is not a search service class because it doesn´t contain a findForSearchService method or service is not initialized.');
            }

            // exclude service if facet search Services is set and service hash isn't defined within.
            if (isset($this->searchConditions['facets']['searchServices']) && !in_array($searchServiceClass->getHash(), $this->searchConditions['facets']['searchServices'])) {
                continue;
            }

            // execute search service method
            try {
                $serviceResult = $searchServiceClass->findForSearchService($this->searchConditions, array_keys($this->result));
                if ($serviceResult) {
                    $serviceResults[$searchServiceClass->getHash()] = $serviceResult;
                }
            } catch (\Exception $ex) {
                GeneralUtility::makeInstance(FlashMessageService::class)
                    ->getMessageQueueByIdentifier('extbase.flashmessages.tx_abavosearch_piresults')
                    ->addMessage(
                        GeneralUtility::makeInstance(
                            FlashMessage::class,
                            $ex->getMessage(),
                            'Fehler in Search-Service: ' . $className,
                            AbstractMessage::WARNING,
                            true
                        )
                    );
            }
        }

        /*
         * Merge service results
         */
        foreach ($serviceResults as $serviceHash => $serviceResult) {
            $this->result['result'] = array_merge($this->result['result'], $serviceResult['result']);
            $this->result['queryCount'] += $serviceResult['queryCount'];
            $this->result['queryTime'] += $serviceResult['queryTime'];
            $this->result['allCount'] += $serviceResult['allCount'];
            $this->result['searchMethod'] .= (((boolean)$this->result['searchMethod'] && count($registeredServices) > 1) ? ',' : '') . $serviceHash . ':' . $serviceResult['searchMethod'];
        }

        /*
         * calclulate ranking only, if more than default SearchService is registered
         */
        if (count($registeredServices) > 1) {

            foreach ($this->result['result'] as $resultItem) {

                $similarity = [
                    'title' => $this->searchUtility->calculateSimilarty($resultItem->getTitle(), $this->searchConditions['search']),
                    'content' => $this->searchUtility->calculateSimilarty($resultItem->getContent(), $this->searchConditions['search'])
                ];

                // To give title a higher weight, we double it, if the value is under 50%
                if ($similarity['title'] <= (float)50) {
                    $similarity['title'] = $similarity['title'] * 2;
                }
                $averageSimilarityPercent = round(array_sum($similarity) / count($similarity), 2);
                $resultItem->setRanking($averageSimilarityPercent);
            }

            // Reorganize sorting
            $reorganizedResults = [];
            switch ($this->searchConditions['orderBy']) {

                case 'ranking':
                    foreach ($this->result['result'] as $resultItem) {
                        $key = (string)$resultItem->getRanking();
                        $reorganizedResults[$key] = $resultItem;
                    }
                    break;

                case'title':
                    foreach ($this->result['result'] as $resultItem) {
                        $key = preg_replace('/[^a-zA-Z0-9\']/', '', $resultItem->getTitle());
                        $reorganizedResults[$key] = $resultItem;
                    }
                    break;

                case'content':
                    foreach ($this->result['result'] as $resultItem) {
                        $key = strlen($resultItem->getContent());
                        $reorganizedResults[$key] = $resultItem;
                    }
            }

            // sort by key
            if ($this->searchConditions['sorting'] === 'desc') {
                krsort($reorganizedResults);
            } else {
                ksort($reorganizedResults);
            }

            // extract only given count by 'lenght' in search conditions
            $this->result['result'] = array_values(array_slice($reorganizedResults, 0, $this->searchConditions['length']));
        }

        // Set iteration
        $this->result['iteration'] = $this->getIteration();

        #\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->result, __METHOD__);
        return $this->result;
    }

    /**
     * Get iteration array
     *
     * @return array
     */
    private function getIteration()
    {
        // Make Iteration
        $iteration = [];
        $maxcount = 0;
        if ($this->searchConditions['start'] == false) {
            $startcount = 1;
            $maxcount = $this->searchConditions['length'];
        } else {
            $startcount = $this->searchConditions['start'] + 1;
            $maxcount = $this->searchConditions['start'] + $this->searchConditions['length'];
        }

        for ($i = $startcount; $i <= $maxcount; $i++) {
            $iteration[] = $i;
        }

        return $iteration;
    }

    /**
     * Returns the class hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->classHash;
    }
}