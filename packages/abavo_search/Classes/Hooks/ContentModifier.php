<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoSearch\Hooks;

use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ContentModifier
{
    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * The Search words array
     * @var array
     */
    protected $swordList = [];

    /**
     * The extension configuration
     *
     * @var array
     */
    protected $extConf = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {

            // Get TypoScript config
            if (array_key_exists('tx_abavosearch.', $GLOBALS['TSFE']->config['config'])) {
                $this->extConf = $GLOBALS['TSFE']->config['config']['tx_abavosearch.']['contentModifier.'];
                
                // Early exit if contentModifier is disabled
                if ((boolean) $this->extConf['enable'] === false) {
                    return;
                }
            } else {
                // Early exit if no configuration is given
                return;
            }

            // Get all swords from GET/POST in tx_abavosearch_sword_list[] and remove XSS
            $gp = GeneralUtility::_GPmerged($this->extConf['param']);
            if ((boolean) (is_countable($gp) ? count($gp) : 0)) {
                foreach ($gp as $sword) {
                    $sword = preg_replace('/[^A-Za-zßäöüÄÖÜ 0-9\-]/', '', $sword);
                    if (strlen($sword) >= 3) {
                        array_push($this->swordList, $sword);
                    }
                }
            }

            // Init logger
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
        }
    }

    /**
     *  If there are no INTincScripts to include
     */
    public function noCache(&$params, &$obj)
    {
        if (!$GLOBALS['TSFE']->isINTincScript()) {
            return; // stop
        }
        // call main replace function
        $this->replaceContent($params, $obj);
    }

    /**
     * If there are any INTincScripts to include
     */
    public function cache(&$params, &$obj)
    {
        if ($GLOBALS['TSFE']->isINTincScript()) {
            return; // stop
        }
        // call main replace function
        $this->replaceContent($params, $obj);
    }

    /**
     *  Main Function
     */
    public function main(&$params, &$obj)
    {
        return $params;
    }

    /**
     * Content replace Function
     */
    public function replaceContent(&$params, &$obj)
    {
        try {
            // Are search words given?
            if ((boolean) count($this->swordList) && (boolean) count($this->extConf)) {

                /*
                 *  Config checkup
                 */
                if ($this->extConf['regex'] === '' || $this->extConf['wrap'] === '') {
                    throw new \Exception('TS-Template config not given');
                }

                /*
                 *  Prepare replacements
                 */
                $htmlWrap = GeneralUtility::trimExplode('###SWORD###', $this->extConf['wrap']);
                $sWords   = implode('|', $this->swordList);
                $regex    = str_replace('###SWORDS###', $sWords, '/'.$this->extConf['regex'].'/i');


                /*
                 *  Use DOMParcer to modify content from target dom element if given
                 */
                if (array_key_exists('modifyDOMId', $this->extConf) && $this->extConf['modifyDOMId'] !== '') {

                    /**
                     *  Read HTML-Document
                     *  libxml_use_internal_errors(true) because sometime links generated in content:
                     *  http://stackoverflow.com/questions/1685277/warning-domdocumentloadhtml-htmlparseentityref-expecting-in-entity
                     */
                    libxml_use_internal_errors(true);
                    $doc                  = new \DOMDocument();
                    // FIX wrong encoding: https://stackoverflow.com/questions/8218230/php-domdocument-loadhtml-not-encoding-utf-8-correctly#comment-61073414
                    $doc->loadHTML('<?xml encoding="utf-8" ?>'.$params['pObj']->content);
                    $doc->validateOnParse = true;

                    $innerHTML = '';
                    $node      = $doc->getElementById($this->extConf['modifyDOMId']);
                    foreach ($node->childNodes as $child) {
                        $innerHTML .= $node->ownerDocument->saveHTML($child);
                    }

                    // Replace value from node
                    $node->nodeValue = preg_replace($regex, $htmlWrap[0].'$1'.$htmlWrap[1], $innerHTML);

                    // Write modified HTML back (https://stackoverflow.com/questions/8218230/php-domdocument-loadhtml-not-encoding-utf-8-correctly#answer-20675396)
                    $params['pObj']->content = html_entity_decode($doc->saveHTML($doc->documentElement));
                } else {
                    // Write modified HTML back (default usage)
                    $params['pObj']->content = preg_replace($regex, $htmlWrap[0].'$1'.$htmlWrap[1], $params['pObj']->content);
                }
            }
        } catch (\Exception $ex) {

            /*
             *  Exception is only for BE-Admins visible
             */
            if (($GLOBALS['BE_USER'] !== null) && $GLOBALS['BE_USER']->isAdmin() === true) {
                DebuggerUtility::var_dump(['message' => $ex->getMessage(), 'backtrace' => $ex->getTraceAsString()], 'ContentModifier error');
            }

            $this->logger->log(
                LogLevel::ERROR, $ex->getMessage(), ['file' => $ex->getFile(), 'line' => $ex->getLine(), 'code' => $ex->getCode(), 'trace' => $ex->getTrace()]
            );
        }
    }
}