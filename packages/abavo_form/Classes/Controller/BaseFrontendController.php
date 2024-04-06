<?php

namespace Abavo\AbavoForm\Controller;

/*
 * abavo_form
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Abavo\AbavoForm\Utility\ConfigHelper;
use Abavo\AbavoForm\Utility\BasicUtility;
use Abavo\AbavoForm\Domain\Repository\FormRepository;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use Abavo\AbavoForm\Domain\Exception\FlashMesssageException;

/**
 * BaseFrontendController
 *
 * @author mbruckmoser
 */
class BaseFrontendController extends ActionController
{
    /**
     * EXTKEY
     *
     * @string $extkey
     */
    protected $extKey = '';

    /**
     * PlugIn parameter
     *
     * Using for for example in UriBuilder or form names
     * This value is also accessible in FLUID via {settings.pluginParam}
     *
     * @string $pluginParam
     */
    protected $pluginParam = '';

    /**
     * configHelper
     *
     * @var ConfigHelper
     */
    protected $config = null;

    /**
     * BasicUtility
     *
     * @var BasicUtility
     */
    protected $basicUtility = null;

    /**
     * FormRepository
     *
     * @var FormRepository
     */
    protected $formRepository = null;


    /*
     * PlugIn Data
     */
    protected $piData = null;

    /**
     * @var $this->logger \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger = null;

    /**
     * initializeAction method
     */
    public function initializeAction()
    {
        parent::initializeAction();

        // Init defaults
        $this->logger      = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
        $this->extKey      = GeneralUtility::camelCaseToLowerCaseUnderscored($this->request->getControllerExtensionName());
        $this->pluginParam = 'tx_'.strtolower($this->request->getControllerExtensionName()).'_'.strtolower($this->request->getPluginName());

        // Get piData
        $cObjData = $this->configurationManager->getContentObject()->data;
        if ($cObjData !== null && is_array($cObjData)) {
            if (array_key_exists('list_type', $cObjData)) {
                $this->piData = $cObjData;
            }
        }

        // Define settings
        $this->settings['pageData']           = (isset($GLOBALS['TSFE'])) ? $GLOBALS['TSFE']->page : null;
        $this->settings['piData']             = $this->piData;
        $this->settings['interfaceData']      = ($this->request->hasArgument('data')) ? $this->basicUtility->cleanInputArrayForHtmlRecursive($this->request->getArgument('data')) : null;
        $this->settings['pluginParam']        = $this->pluginParam;
        $this->settings['sysLanguageUid']     = (isset($GLOBALS['TSFE'])) ? $GLOBALS['TSFE']->sys_language_uid : null;
        $this->settings['sysLanguageIsoCode'] = (isset($GLOBALS['TSFE'])) ? $GLOBALS['TSFE']->getLanguage()->getTwoLetterIsoCode() : null;
        $this->settings['storagePids']        = '';
        $this->settings['requestData']        = [
            'action' => $this->request->getControllerActionName(),
            'controller' => $this->request->getControllerName(),
            'extension' => $this->request->getControllerExtensionName()
        ];

        /*
         * Set storage pids in following order
         * 
         * 1. TS-Setup persistence.storagePid
         * 2. Plugin['pages']
         * 3. GP-Param "storagePid"
         * 4. CurrentPid
         */
        $pids = '';
        $conf = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK, $this->request->getControllerExtensionName());
        if (array_key_exists('persistence', $conf) && array_key_exists('storagePid', $conf['persistence']) && !(boolean) $conf['persistence']['storagePid']) {
            $pids = $conf['persistence']['storagePid'];
        }

        // Get plug-in´s pages
        $pluginData = $this->piData;
        if (is_array($pluginData) && array_key_exists('pages', $pluginData) && (boolean) $pluginData['pages']) {
            $pids = $pluginData['pages'];
        }

        // "storagePids" GP-Param (overwrite any existing setting above)
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
            $this->formRepository->setDefaultQuerySettings($this->formRepository->createQuery()->getQuerySettings()->setStoragePageIds(GeneralUtility::intExplode(',', $pids)));
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
     * Throw a action controller exception
     *
     * @param \Exception $ex
     * @param string $title (optional) title
     * @return FlashMesssageException
     */
    final public function throwActionControllerException(\Exception $ex, $title = '', $setFlashMessage = true)
    {
        if ($ex instanceof \Exception && !$ex instanceof StopActionException) {

            // Generate FlashMessageException
            $title                  = ((boolean) trim($title)) ? $title : 'Error in '.__FUNCTION__;
            $flashMesssageException = new FlashMesssageException($ex->getMessage(), $ex->getCode(), $ex, (($setFlashMessage) ? $this : null), $title);

            // Log Exception
            if ($flashMesssageException->getCode() == AbstractMessage::ERROR) {
                $this->logger->log(LogLevel::ERROR,
                    json_encode([
                    'exceptionClass' => get_class($ex),
                    'file' => $ex->getFile().' line '.$ex->getLine(),
                    'message' => $ex->getMessage(),
                    'trace' => $ex->getTrace()
                ], JSON_THROW_ON_ERROR));
            }

            return $flashMesssageException;
        }
    }

    /**
     * A special action which is called if the originally intended action could
     * not be called, for example if the arguments were not valid.
     *
     * The default implementation sets a flash message, request errors and forwards back
     * to the originating action. This is suitable for most actions dealing with form input.
     *
     * We clear the page cache by default on an error as well, as we need to make sure the
     * data is re-evaluated when the user changes something.
     *
     * @return string
     */
    public function errorAction(): ResponseInterface
    {
        $message = LocalizationUtility::translate('Form.error.message', $this->extKey);
        $title   = LocalizationUtility::translate('Form.error', $this->extKey);
        $this->addFlashMessage($message, $title, AbstractMessage::WARNING);

        $this->clearCacheOnError();
        $this->forwardToReferringRequest();
        return $this->htmlResponse();
    }

    public function injectConfig(ConfigHelper $config): void
    {
        $this->config = $config;
    }

    public function injectBasicUtility(BasicUtility $basicUtility): void
    {
        $this->basicUtility = $basicUtility;
    }

    public function injectFormRepository(FormRepository $formRepository): void
    {
        $this->formRepository = $formRepository;
    }
}