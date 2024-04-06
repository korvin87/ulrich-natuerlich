<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Indexers;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use Abavo\AbavoSearch\Domain\Model\Indexer;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Abavo\AbavoSearch\Domain\Exception\IndexException;
use Abavo\AbavoSearch\Domain\Model\Index;

/**
 * UrlIndexer
 *
 * @author mbruckmoser
 */
class UrlIndexer extends BaseIndexer
{
    /**
     * @var ContentObjectRenderer
     */
    protected $cObj;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    function __construct($settings)
    {
        $this->settings = $settings;

        // Init Frondend rendering
        $this->initTSFE();
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        // Init queryBuilder
        $connectionPool     = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
    }

    /**
     *
     * @param Indexer $indexer
     * @return array
     */
    public function getData(Indexer $indexer)
    {
        $timeStart = microtime(true);
        $resources = [];
        $altTitle  = '';

        if (!$indexer) {
            throw new IndexException('UrlIndexer\getData: No indexer given.');
        }

        // get URLList from flexform
        $urlList = GeneralUtility::trimExplode("\n", $this->settings['urllist']);
        foreach ($urlList as $url) {
            $result   = [];
            $altTitle = '';

            // make typolink for request
            $requestURL = $this->cObj->typolink_URL(['parameter' => $url, 'forceAbsoluteUrl' => true]);

            /*
             *  get addtional data if URL is a PageUid
             */
            if (is_numeric($url) || strpos($url, ',') !== false) {
                $pid = (int) $url;

                $urlParts = GeneralUtility::trimExplode(',', $url);
                if (is_countable($urlParts) ? count($urlParts) : 0) {
                    $pid              = $urlParts[0];
                    $sys_language_uid = 0;

                    // extract language-param
                    foreach ($urlParts as $part) {
                        if (strpos($part, '&L=') !== false) {
                            $sys_language_uid = (int) GeneralUtility::trimExplode('&L=', $part)[1];
                            break;
                        }
                    }
                }

                if ((boolean) $pid) {
                    $results = [];
                    if ((boolean) $sys_language_uid) {
                        $results = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('title', 'pages_language_overlay', 'pid='.(int) $pid.' AND sys_language_uid='.(int) $sys_language_uid, '', '', '1');
                    } else {
                        $results = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('title', 'pages', 'uid='.(int) $pid, '', '', '1');
                    }

                    if (!empty($results)) {
                        // get pages title to force title on index
                        $altTitle = current($results)['title'];
                    }
                }
            }


            // make request/parse content
            $arrURL         = GeneralUtility::trimExplode('#', $requestURL);
            $contentWrapper = ((is_countable($arrURL) ? count($arrURL) : 0) === 2) ? $arrURL[1] : '';
            $result         = $this->urlParser($requestURL, $contentWrapper, $altTitle);

            // push positive result
            if (!empty($result)) {
                $result['url'] = $url;
                array_push($resources, $result);
            }
        }

        // make index for all resources
        foreach ($resources as $result) {

            // Index-Modify-Hook
            $title    = $result['title'];
            $content  = $result['content'];
            $abstract = (array_key_exists('meta', $result) && array_key_exists('abstract', $result['meta'])) ? $result['meta']['abstract'] : '';
            $this->modifyIndexHook('Url', $result, $title, $content, $abstract);

            // Make Index Object
            $tempIndex = Index::getInstance();
            $tempIndex->setTitle(strip_tags($title));
            $tempIndex->setContent(preg_replace('!\s+!', ' ', strip_tags($content)));
            $tempIndex->setAbstract(preg_replace('!\s+!', ' ', strip_tags($abstract)));
            $tempIndex->setTarget($result['url']);
            $tempIndex->setFegroup($this->settings['feGroup'] ?: self::FE_GROUP_DEFAULT);
            $tempIndex->setSysLanguageUid((int) $this->settings['language']);
            #$tempIndex->setRefid('example_table'.self::REFID_SEPERARTOR.$result['uid']);
            //Set additional fields
            $this->setAdditionalFields($tempIndex, $indexer);

            // Add index to list
            $this->data[] = $tempIndex;
        }

        // Set duration time
        $this->duration[$indexer->getUid()] = (microtime(true) - $timeStart);

        return $this->data;
    }

    private function urlParser($url = '', $contentWrapper = '', $altTitle = '')
    {
        $result = $body   = [];

        if ($url != '') {
            try {
                // use fopen
                $content = file_get_contents($url);

                // get all meta informations
                preg_match_all("|<meta[^>]+name=\"([^\"]*)\"[^>]"."+content=\"([^\"]*)\"[^>]+>|i", $content, $matches, PREG_PATTERN_ORDER);
                if (array_key_exists(1, $matches) && array_key_exists(1, $matches) && (count($matches[1]) === count($matches[2]))) {
                    $result['meta'] = array_combine($matches[1], $matches[2]);
                }

                // DOM-PARCER
                $document = new \DOMDocument;
                $mock     = new \DOMDocument;
                libxml_use_internal_errors(true); // for invalid html content
                $document->loadHTML($content);

                // get page title / set altTitle
                if ((boolean) trim($altTitle)) {
                    $result['title'] = $altTitle;
                } else {
                    $titlelist = $document->getElementsByTagName('title');
                    if ($titlelist->length > 0) {
                        $result['title'] = $titlelist->item(0)->nodeValue;
                    }
                }

                // what content to get?
                if ($contentWrapper != '') {
                    $body = $document->getElementById($contentWrapper); // reduce content to DOMID´s content
                } else {
                    $body = $document->getElementsByTagName('body')->item(0);
                }

                // get content
                if (!empty($body)) {
                    foreach ($body->childNodes as $child) {
                        $mock->appendChild($mock->importNode($child, true));
                    }
                    $content = $mock->saveHTML();
                }

                /*
                 * CLEANUP
                 */
                if ($content != '') {
                    // remove inline javascript
                    $content           = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
                    // remove html tags
                    $content           = strip_tags($content);
                    // remove all line breaks
                    $content           = preg_replace("/\s\s+/", " ", $content);
                    // replace multi whitespaces to one
                    $content           = trim(preg_replace('/ {2,}/', ' ', $content));
                    $result['content'] = $content;
                }
            } catch (\Exception $ex) {
                DebuggerUtility::var_dump($ex->getMessage(), 'UrlIndexer Error');
            }
        }

        return $result;
    }

    private function initTSFE($id = 1, $typeNum = 0)
    {
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new TimeTracker(false);
            GeneralUtility::makeInstance(TimeTracker::class)->start();
        }
        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $GLOBALS['TYPO3_CONF_VARS'], $id, $typeNum);
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->getConfigArray();

        if (ExtensionManagementUtility::isLoaded('realurl')) {
            $rootline             = BackendUtility::BEgetRootLine($id);
            $host                 = BackendUtility::firstDomainRecord($rootline);
            $_SERVER['HTTP_HOST'] = $host;
        }
    }
}