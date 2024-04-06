<?php
/**
 * ulrich_products - ActionController.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 24.05.2018 - 07:51:31
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Controller;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Core\Error\Http\PageNotFoundException;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;

/**
 * ActionController
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * EXTKEY
     *
     * $extkey
     */
    protected $extKey = '';

    /**
     * PlugIn parameter
     *
     * Using for for example in UriBuilder or form names
     * This value is also accessible in FLUID via {settings.pluginParam}
     *
     * $pluginParam
     */
    protected $pluginParam = '';

    /*
     * PlugIn Data
     */
    protected $piData = null;

    /**
     * Variables that should be assigned in view
     * @var array
     */
    protected $variables = [];

    /**
     * cacheUtility
     *
     * @var CacheManager
     */
    protected $cacheInstance = null;

    /**
     * initializeAction method
     */
    public function initializeAction()
    {
        parent::initializeAction();

        // Init defaults
        $this->extKey        = GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName());
        $this->pluginParam   = 'tx_'.strtolower($this->request->getControllerExtensionName()).'_'.strtolower($this->request->getPluginName());
        $this->cacheInstance = GeneralUtility::makeInstance(CacheManager::class)->getCache(GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName()));

        // Get piData
        $cObjData = $this->configurationManager->getContentObject()->data;
        if ($cObjData !== null && is_array($cObjData)) {
            if (array_key_exists('list_type', $cObjData)) {
                $this->piData = $cObjData;
            }
        }

        // Define settings
        $this->settings['requestData']        = [
            'controller' => $this->request->getControllerName(),
            'action' => $this->request->getControllerActionName(),
            'plugin' => $this->request->getPluginName()
        ];
        $this->settings['pageData']           = $GLOBALS['TSFE']->page ?? null;
        $this->settings['piData']             = $this->piData;
        $this->settings['pluginParam']        = $this->pluginParam;
        $this->settings['sysLanguageUid']     = $GLOBALS['TSFE']->sys_language_uid ?? null;
        $this->settings['sysLanguageIsocode'] = $GLOBALS['TSFE']->getLanguage()->getTwoLetterIsoCode() ?? null;
        $this->settings['storagePids']        = '';

        /*
         * Set storage pids in following order
         *
         * 1. TS-Setup persistence.storagePid
         * 2. Plugin['pages']
         * 3. GP-Param "storagePids"
         * 4. CurrentPid
         */
        $pids = '';
        $conf = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, $this->request->getControllerExtensionName());
        if (array_key_exists('persistence', $conf) && array_key_exists('storagePid', $conf['persistence']) && (boolean) $conf['persistence']['storagePid']) {
            $pids = $conf['persistence']['storagePid'];
        }

        // Get plug-in´s pages
        $pluginData = $this->piData;
        if (is_array($pluginData) && array_key_exists('pages', $pluginData) && (boolean) $pluginData['pages']) {
            $pids = $pluginData['pages'];
        }

        // "storagePid" GP-Param (overwrite any existing setting above)
        if ($this->request->hasArgument('storagePids') === true) {
            $pids = implode(',', GeneralUtility::intExplode(',', preg_replace("/[^0-9,]/", "", $this->request->getArgument('storagePids'))));
        }

        // CurrentPid if no pids (fallback if nothing is set)
        if (empty($pids)) {
            $pids = (int) $GLOBALS['TSFE']->id;
        }

        // Finaly set storage pids for repositories/settings
        if ((boolean) trim($pids)) {
            $this->settings['storagePids'] = $pids;

            // Set storage pids for extended class repositories
            $classRepositories = array_filter(array_keys(get_class_vars(static::class)), fn($var) => strpos($var, 'Repository') !== false);
            if (is_array($classRepositories) && !empty($classRepositories)) {
                foreach ($classRepositories as $repositoryClassName) {
                    if ($this->$repositoryClassName instanceof Repository) {
                        $this->$repositoryClassName->setDefaultQuerySettings($this->$repositoryClassName->createQuery()->getQuerySettings()->setStoragePageIds(
                                GeneralUtility::intExplode(',', $this->settings['storagePids']))
                        );
                    }
                }
            }
        }

        /*
         *  Is it a JSON-Format request, we set the JsonView object name
         *  Be sure you defined the correct header in your pageType typoscript configuration
         */
        if (strtolower($this->request->getFormat()) === 'json') {
            $this->defaultViewObjectName = JsonView::class;
        }
    }

    /**
     * Calls the specified action method and passes the arguments.
     *
     * If the action returns a string, it is appended to the content in the
     * response object. If the action doesn't return anything and a valid
     * view exists, the view is rendered automatically.
     *
     * @return void
     * @override \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
     */
    protected function callActionMethod(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        try {
            // Setting response header
            if (strtolower($this->request->getFormat()) === 'json') {
                $response = $this->responseFactory->createResponse()->withHeader('Content-Type', 'application/json; charset=utf-8');
            }
            // Call Action method
            return parent::callActionMethod($request);
        } catch (\Exception $ex) {

            if (!$ex instanceof StopActionException) {

                // This enables you to trigger the call of TYPO3s page-not-found handler by throwing \TYPO3\CMS\Core\Error\Http\PageNotFoundException
                if ($ex instanceof PageNotFoundException) {
                    $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction($GLOBALS['TYPO3_REQUEST'], LocalizationUtility::translate('exception.entityNotFoundMessage', $this->request->getControllerExtensionName()));
                    throw new ImmediateResponseException($response);
                }

                // $GLOBALS['TSFE']->pageNotFoundAndExit has not been called, so the exception is of unknown type.
                $message = $ex->getMessage() ?? LocalizationUtility::translate('exception.unknownErrorMessage', $this->request->getControllerExtensionName());
                $response = GeneralUtility::makeInstance(ErrorController::class)->unavailableAction($GLOBALS['TYPO3_REQUEST'], $message);
                throw new ImmediateResponseException($response);
            }
        }
    }

    /**
     * Make a Cache Identifier Method
     *
     * @param string $additionParam
     * @return string md5Hash
     */
    public function makeCacheIdentifier($additionParam = '')
    {
        return md5(
            $GLOBALS['TSFE']->id.'-'.$this->cObj->data['uid'].'-'.$GLOBALS['TSFE']->sys_language_uid.'-'.$this->actionMethodName.$additionParam
        );
    }
}