<?php
/*
 * abavo_maps
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace TYPO3\AbavoMaps\User;

use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * EidBootstrap
 *
 * This class could called via eID
 * Usage <domain.tld>/?eID=<lowerCaseExtKey>_<pluginName>
 *
 * @author mbruckmoser
 */
class EidBootstrap
{
    /**
     * configuration
     *
     * @var \array
     */
    protected $configuration;

    /**
     * bootstrap
     *
     * @var \array
     */
    protected $bootstrap;

    /**
     * conf
     *
     * @var \stdClass
     */
    protected $conf;

    /**
     * Generates the output
     *
     * @return \string from action
     */
    public function run()
    {
        return $this->bootstrap->run('', $this->configuration);
    }

    /**
     * Initialize Extbase
     *
     * @param \array $TYPO3_CONF_VARS
     */
    public function __construct($TYPO3_CONF_VARS)
    {
        /**
         * Set Base Configuration
         */
        $this->conf                         = (object) array_combine(['vendor', 'extensionName', 'classPath'], explode('\\', __NAMESPACE__));
        $this->conf->extensionNameLowerCase = strtolower($this->conf->extensionName);
        $this->conf->pluginName             = 'PiEid'; /* Because backward compatibility to other Extensions, we dont change the request and work with a Dummy "PiEid" */

        // get merged $_POST and $_GET
        #$_GPVars       = GeneralUtility::_GPmerged('tx_'.$this->conf->extensionNameLowerCase.'_'.strtolower($this->conf->pluginName));
        $_GPVars = GeneralUtility::_GP('request');
        $_GPVars['id'] = GeneralUtility::_GET('id') ? : 0;

        // pre-check syntax of controller and action
        if (!preg_match('/^[a-z0-9]*$/i', $_GPVars['action'])) {
            throw new \Exception("Don't give me such an action-name", 1_430_985_302);
        }

        // if not set fallback to default action in plugin-configuration -> ext_localconf.php
        if (!array_key_exists('controller', $_GPVars) || !preg_match('/^[a-z0-9]*$/i', $_GPVars['controller'])) {
            throw new \Exception("Don't give me such a controller-name", 1_430_985_310);
        }

        // extbase-bootstraping
        $this->bootstrap = new Bootstrap();

        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(TypoScriptFrontendController::class, $TYPO3_CONF_VARS, $_GPVars['id'], 0, TRUE);

        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->fe_user = EidUtility::initFeUser();
        $GLOBALS['TSFE']->id      = $_GPVars['id'];
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->getConfigArray();
        #TODO: needed for TYPO3 6.2? $GLOBALS['TSFE']->includeTCA();


        // get plugin typoscript configuration
        $typoScriptService   = GeneralUtility::makeInstance(TypoScriptService::class);
        $pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_'.$this->conf->extensionNameLowerCase.'.']);

        // set storagePids
        $storagePids = implode(',', [$_GPVars['id'], (int) $pluginConfiguration['persistence']['storagePid']]);

        // set bootstrap-configuration
        $this->configuration = [
            'pluginName' => $this->conf->pluginName,
            'vendorName' => $this->conf->vendor,
            'extensionName' => $this->conf->extensionName,
            'controller' => $_GPVars['controller'],
            'action' => $_GPVars['action'],
            'mvc' => [
                'requestHandlers' => [FrontendRequestHandler::class => FrontendRequestHandler::class]
            ],
            'settings' => $pluginConfiguration['settings'],
            'persistence' => ['storagePid' => $storagePids]
        ];

    }
}
global $TYPO3_CONF_VARS;
$eid = GeneralUtility::makeInstance(__NAMESPACE__.'\EidBootstrap', $TYPO3_CONF_VARS);
echo $eid->run(); // print content
