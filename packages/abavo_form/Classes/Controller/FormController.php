<?php

namespace Abavo\AbavoForm\Controller;

/*
 * abavo_form
 * 
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use Psr\Http\Message\ResponseInterface;
use Abavo\AbavoForm\Domain\Service\SessionService;
use Abavo\AbavoForm\Utility\FormSanitation;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository;
use Abavo\AbavoForm\Domain\Model\Form;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Controller\Argument;
use TYPO3\CMS\Extbase\Property\PropertyMapper;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use Abavo\AbavoForm\Domain\Model\Stepper;
use Abavo\AbavoForm\Domain\Service\SessionHookService;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use SJBR\StaticInfoTables\Domain\Model\Country;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Reflection\PropertyReflection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * ConfiguratorController
 *
 * @author mbruckmoser
 */
class FormController extends BaseFrontendController
{
    /**
     * SessionService
     *
     * @var SessionService
     */
    protected $sessionService = null;

    /**
     * FormSanitation
     *
     * @var FormSanitation
     */
    protected $formSanitation = null;

    /**
     * countryRepository
     *
     * @var CountryRepository
     */
    protected $staticCountryRepository = null;

    /**
     * countryZoneRepository
     *
     * @var CountryZoneRepository
     */
    protected $staticCountryZoneRepository = null;

    /**
     * Initialize all action methods
     */
    public function initializeAction()
    {
        // defaults
        parent::initializeAction();
        $makeNewInstance = false;

        // Set model class in $this->settings['formModelClass'] (AJAX validation usecase)
        $this->setFormModellClassSettingByRequest();

        // Set $this->settings by request (AJAX submit usecase)
        $this->setSettingsByRequest();

        // Set currentFormSessionKey to settings for usage in services or where ever :-)
        $this->settings['currentFormSessionKey'] = $this->getCurrentFormSessionKey();

        // Restore form model from session (if not exists return false)
        $newForm = $this->sessionService->restoreFromSession($this->getCurrentFormSessionKey());

        // Make new form instance if no valid form model exists
        if (!$newForm instanceof Form && class_exists($this->settings['formModelClass'])) {
            $makeNewInstance = true;
        }

        // Handle form reset logic
        if ($makeNewInstance === false && $newForm->isProcessed() && $newForm->isResetAfterCreate()) {
            $makeNewInstance = true;
        }

        // Make NEW session instance if needed
        if ($makeNewInstance === true) {
            $newForm = GeneralUtility::makeInstance($this->settings['formModelClass']);
        }

        // Write session instance in session
        $this->sessionService->writeToSession($newForm, $this->getCurrentFormSessionKey());

        // Debugging
        if ((boolean) $this->settings['debug'] && $this->request->getFormat() === 'html') {
            DebuggerUtility::var_dump(['settings' => $this->settings, 'newForm' => $newForm], __METHOD__);
        }
    }

    /**
     * Define Form Model Class for settings by request
     */
    private function setFormModellClassSettingByRequest()
    {
        // In case of AJAX requests and this setting is not defined
        if ($this->request->hasArgument('formModelClass')) {

            $classNameRequest = htmlentities($this->request->getArgument('formModelClass'));

            // Validation request value
            if (in_array($classNameRequest, array_column($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['formmodels'], 'value')) === true) {

                if (GeneralUtility::makeInstance($classNameRequest) instanceof Form) {
                    // setting model class
                    $this->settings['formModelClass'] = $classNameRequest;
                }
            }
        }
    }

    /**
     * Define settings by request
     */
    private function setSettingsByRequest()
    {

        $formSettingsLib = null;
        // In case of AJAX requests and this setting is not defined
        $formSettingsUid = null;
        if ($this->request->hasArgument('formSettingsUid')) {
            $formSettingsUid = (int) $this->request->getArgument('formSettingsUid');
        }

        // In case of error forwarding we need the original request
        if ($this->request->getOriginalRequest()) {
            if ($this->request->getOriginalRequest()->hasArgument('formSettingsUid')) {
                $formSettingsUid = (int) $this->request->getOriginalRequest()->getArgument('formSettingsUid');
            }
        }

        // Overrule settings from original plugin flexform
        if (is_int($formSettingsUid)) {

            $connectionPool = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class);
            $queryBuilder = $connectionPool->getQueryBuilderForTable('tt_content');
            $records = $queryBuilder
                        ->select('*')
                        ->from('tt_content')
                        ->where(
                            $queryBuilder->expr()->eq('uid', (int) $formSettingsUid)
                        )
                        ->executeQuery();
            
            $piData = null;
            while ($row = $records->fetchAssociative()) {
                $piData = $row;
            }

            if (isset($piData['pi_flexform'])) {
                $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
                $flexFormData             = $flexFormService->convertFlexFormContentToArray($piData['pi_flexform']);
                ArrayUtility::mergeRecursiveWithOverrule($this->settings, $flexFormData['settings'], true);
                $this->settings['piData'] = $piData;
            }
        }

        /*
         *  Overrule settings from lib - TypoScript-Path
         */
        if ($this->request->hasArgument('formSettingsLib')) {
            $formSettingsLib = $this->request->getArgument('formSettingsLib');
        }

        // In case of error forwarding we need the original request
        if ($this->request->getOriginalRequest()) {
            if ($this->request->getOriginalRequest()->hasArgument('formSettingsLib')) {
                $formSettingsLib = $this->request->getOriginalRequest()->getArgument('formSettingsLib');
            }
        }

        if ($formSettingsLib) {
            $tsArray          = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
            $typoScriptParser = GeneralUtility::makeInstance(TypoScriptParser::class);
            $tsValArray       = $typoScriptParser->getVal($formSettingsLib.'.settings', $tsArray);

            if (is_array($tsValArray) && !empty($tsValArray)) {
                $tsService   = GeneralUtility::makeInstance(TypoScriptService::class);
                $libSettings = $tsService->convertTypoScriptArrayToPlainArray(current($tsValArray));

                ArrayUtility::mergeRecursiveWithOverrule($this->settings, $libSettings, true);
            }
        }

        // define submitMode
        if ($this->request->hasArgument('submitMode')) {
            $this->settings['submitMode'] = htmlentities($this->request->getArgument('submitMode'));
        }

        if ($this->settings['submitMode'] === 'background') {
            $this->defaultViewObjectName = JsonView::class;
        }

        // define noWrapLayout
        if ($this->request->hasArgument('noWrapLayout')) {
            $this->settings['noWrapLayout'] = true;
        }
    }

    /**
     * Get current (for this plugin) form session key
     *
     * @return type
     */
    private function getCurrentFormSessionKey()
    {
        return trim($this->settings['sessionKeyPrefix'].$GLOBALS['TSFE']->id.'_'.$GLOBALS['TSFE']->sys_language_uid.'_'.$this->settings['formModelClass']::SESSION_KEY);
    }

    /**
     * Overwrite model specific validators based on settings['formModelClass'] class
     *
     * @return void
     */
    private function overwriteModelValidation()
    {
        // Overwrite argument 'newForm' and it´s validation if model is a registered 'Abavo\AbavoForm\Domain\Model\Form' extended class
        if ($this->arguments->hasArgument('newForm') && $this->settings['formModelClass'] !== Form::class) {

            /*
             * Create an new argument instance with dataType from setting 'formModelClass'
             */
            $argument       = $this->objectManager->get(Argument::class, 'newForm', $this->settings['formModelClass']);

            // Extract and allow differnt properties from extended model or skip unknown properties
            $propertyMappingConfiguration = $this->arguments->getArgument('newForm')->getPropertyMappingConfiguration();
            if ($this->settings['formModelClass']::ALLOW_EXT_PROPERTIES_EXPLICIT === true) {

                $extModelProperties  = array_keys(GeneralUtility::makeInstance($this->settings['formModelClass'])->_getProperties());
                $origModelProperties = array_keys(GeneralUtility::makeInstance(Form::class)->_getProperties());
                $diffModelProperties = array_diff($extModelProperties, $origModelProperties);

                if (!empty($diffModelProperties)) {
                    foreach ($diffModelProperties as $additionalProperty) {
                        $propertyMappingConfiguration->allowProperties($additionalProperty);
                    }
                }
            } else {
                $propertyMappingConfiguration->skipUnknownProperties();
            }            

            // Inject (modified/original) property mapping configuration from base arguments
            $argument->injectPropertyMappingConfiguration($propertyMappingConfiguration);


            // Set BaseValidatorConjunction
            $validatorResolver        = $this->objectManager->get(ValidatorResolver::class);
            $conjunctionValidator     = $this->objectManager->get(ConjunctionValidator::class);
            $baseValidatorConjunction = $validatorResolver->getBaseValidatorConjunction($this->settings['formModelClass']);


            /*
             * Remove all property validators of not submitted properties
             */
            if ($this->request->hasArgument('newForm')) {

                $requestProperties = array_keys($this->request->getArgument('newForm'));
                $validators        = $baseValidatorConjunction->getValidators();

                foreach ($validators as $validator) {

                    /* @var \TYPO3\CMS\Extbase\Validation\Validator\GenericObjectValidator */
                    $genericOriginalObjectValidator = $validator->getPropertyValidators();

                    foreach (array_keys($genericOriginalObjectValidator) as $property) {
                        if (!in_array($property, $requestProperties)) {
                            unset($genericOriginalObjectValidator[$property]); // we remove properties there are not in request here
                        }
                    }

                    // We use super cow powers to set value through reflection(!), because we see no other way to modify this dynamically.
                    ObjectAccess::setProperty($validator, 'propertyValidators', $genericOriginalObjectValidator);
                }
            }

            // Overwrite argument 'newForm' with new class based properties
            $conjunctionValidator->addValidator($baseValidatorConjunction);
            $argument->setValidator($conjunctionValidator);
            $this->arguments['newForm'] = $argument;
        }
    }

    /**
     * Spam validation
     *
     * @throws \Exception
     */
    private function spamValidation($sessionForm, $newForm)
    {
        if ((boolean) $this->settings['enableSpamDetection'] && $this->request->hasArgument('newForm')) {

            // Honeypot check
            if ($this->request->hasArgument('controll') === false || $this->request->getArgument('controll') !== '') {
                throw new \Exception(LocalizationUtility::translate('Validation.message.honeypot', 'AbavoForm'), 1_497_006_870);
            }

            // Fast submit check
            if ($this->request->hasArgument('datetime')) {
                try {
                    $currentDatetime = new \DateTime;
                    $checkDateTime   = \DateTime::createFromFormat('Y.m.d H:i:s', $this->request->getArgument('datetime'));
                    if ((int) date_diff($currentDatetime, $checkDateTime)->format('%s') < (int) $this->settings['formSpamDetectionSeconds']) {
                        throw new \Exception(LocalizationUtility::translate('Validation.message.spam', 'AbavoForm', ['#D3']), 1_497_006_982);
                    }
                } catch (\Exception $ex) {
                    throw new \Exception(LocalizationUtility::translate('Validation.message.spam', 'AbavoForm', ['#D2']), 1_497_006_982);
                }
            } else {
                throw new \Exception(LocalizationUtility::translate('Validation.message.spam', 'AbavoForm', ['#D1']), 1_497_006_982);
            }

            // UniqueId check
            if ($sessionForm instanceof Form && $newForm->getUniqueId() !== $sessionForm->getUniqueId()) {
                $this->sessionService->writeToSession(null, $this->getCurrentFormSessionKey());
                throw new \Exception(LocalizationUtility::translate('Validation.message.expired', 'AbavoForm'), 1_497_013_893);
            }
        }
    }

    /**
     *  Initialize newAction method
     */
    public function initializeNewAction()
    {
        $this->overwriteModelValidation();
    }

    /**
     * action new
     *
     * @param mixed $newForm FIXME: The form model for post data could be "mixed" because of validation?
     * @param int $step the next step
     */
    public function newAction($newForm = null, $step = null): ResponseInterface
    {
        // defaults
        $variables = ['formAction' => 'create'];
        $errors    = false;

        try {
            // Use another template (usecase)?
            if ($this->defaultViewObjectName !== JsonView::class && (boolean) $this->settings['template']['new']) {
                $this->view->setTemplatePathAndFilename($this->settings['template']['new']);
            }

            // Get session data
            $sessionForm = $this->sessionService->restoreFromSession($this->getCurrentFormSessionKey());

            // Spam validation
            $this->spamValidation($sessionForm, $newForm);

            // Stepper
            if (is_int($step) && $sessionForm->getStepper() instanceof Stepper) {
                $sessionForm->getStepper()->to($step);
            }

            // Handle arguments (In case of error we get the properties of the original request)
            $arguments = ($this->request->getOriginalRequest() === null) ? $this->request->getArguments() : $this->request->getOriginalRequest()->getArguments();

            // Set properties only by given arguments
            if (array_key_exists('newForm', $arguments) && $newForm instanceof Form) {

                $requestProperties = array_keys($arguments['newForm']);
                foreach ($requestProperties as $property) {
                    $getter = 'get'.ucfirst($property);
                    if (method_exists($newForm, $getter)) {
                        ObjectAccess::setProperty($sessionForm, $property, $newForm->$getter());
                    } else {
                        throw new \Exception('Fatal error: Method '.$getter.' in model '.get_class($newForm).' does not exists.');
                    }
                }
            }

            // Work the registered sessionHookServices (from settings)
            $variables['sessionHookService'] = SessionHookService::getInstance($sessionForm, $this->settings);

            // Write back session form
            $this->sessionService->writeToSession($sessionForm, $this->getCurrentFormSessionKey());

            // Debugging
            if ((boolean) $this->settings['debug']) {
                DebuggerUtility::var_dump(['newForm' => $newForm, 'sessionForm' => $sessionForm], __METHOD__);
            }

            // Set form object
            $variables['newForm'] = $sessionForm;

            // Set form action
            $variables['formAction'] = ($sessionForm->getStepper()->isLast()) ? 'create' : 'new';

            // Get salutaions
            $variables['salutaions'] = $sessionForm->getSalutations();

            // Get Countries
            $this->staticCountryRepository->setDefaultOrderings(['shortNameLocal' => QueryInterface::ORDER_ASCENDING]);
            $variables['staticCountries'] = $this->staticCountryRepository->findAll();

            // Get CountryZones (Limited)
            $enabledCountryZonesCountries = GeneralUtility::trimExplode(',', $this->settings['countriesEnableCountryzones']);
            if ($sessionForm->getCountry() instanceof Country && in_array($sessionForm->getCountry()->getIsoCodeA3(), $enabledCountryZonesCountries)) {
                $this->staticCountryZoneRepository->setDefaultOrderings(['localName' => QueryInterface::ORDER_ASCENDING]);
                $variables['staticCountryZones'] = $this->staticCountryZoneRepository->findByCountry($sessionForm->getCountry());
            }
            //
        } catch (\Exception $ex) {
            $this->throwActionControllerException($ex);
            $errors = true;
        }

        /*
         * Assign data to view
         */
        if ($this->settings['submitMode'] === 'background') {
            $this->view->assign('value', ['errors' => $errors]);
        } else {
            $this->view->assignMultiple(array_merge($variables, ['actionMethodName' => $this->actionMethodName]));
        }
        return $this->htmlResponse();
    }

    /**
     * Initialize create action
     */
    public function initializeCreateAction()
    {
        $this->overwriteModelValidation();
    }

    /**
     * action create
     *
     * @param Form $newForm FIXME: The form model for post data could be "mixed" because of validation?
     * @return void
     */
    public function createAction($newForm = null)
    {
        $variables = [];
        // defaults
        $variables['success'] = false;

        try {
            // Use another create template?
            if ($this->defaultViewObjectName !== JsonView::class && (boolean) $this->settings['template']['create']) {
                $this->view->setTemplatePathAndFilename($this->settings['template']['create']);
            }

            // Get session data
            $sessionForm = $this->sessionService->restoreFromSession($this->getCurrentFormSessionKey());

            // Spam validation
            $this->spamValidation($sessionForm, $newForm);

            // TODO: Is here a upload - execute it on initialize for newAction, too?
            $upload = $newForm->getMedia();
            if (is_array($upload) && $upload['error'] === 0) {
                $this->uploadAndSetMediaForForm($upload, $newForm);
            }


            // Set properties only by given arguments
            $arguments = $this->request->getArguments();
            if (array_key_exists('newForm', $arguments) && $newForm instanceof Form) {

                $requestProperties = array_keys($arguments['newForm']);
                foreach ($requestProperties as $property) {
                    $getter = 'get'.ucfirst($property);
                    if (method_exists($newForm, $getter)) {
                        ObjectAccess::setProperty($sessionForm, $property, $newForm->$getter());
                    }
                }
            }


            // Work the registered postServices (from settings)
            if (isset($this->settings['postServices'])) {

                // Get all service names from settings
                $postServices = GeneralUtility::trimExplode(',', $this->settings['postServices']);
                if (!empty($postServices)) {
                    foreach ($postServices as $serviceClassName) {

                        // Clone object for different service handling
                        $tempForm = clone $sessionForm;

                        // Init and run each services
                        $serviceClass = GeneralUtility::makeInstance($serviceClassName, $this->settings);
                        $serviceClass->run($tempForm);

                        // Is run of service successed?
                        if ($serviceClass->isSuccessed() !== true) {
                            throw new \Exception('Postservice '.$serviceClassName.' not successed', AbstractMessage::WARNING);
                        }
                    }
                }
            }

            // Update session
            $sessionForm->setProcessed(true);

            // Work the registered sessionHookServices (from settings)
            $variables['sessionHookService'] = SessionHookService::getInstance($sessionForm, $this->settings);
            if ($this->settings['formModelClass']::RESET_AFTER_CREATE) {
                $this->sessionService->cleanUpSession($this->getCurrentFormSessionKey());
            } else {
                $this->sessionService->writeToSession($sessionForm, $this->getCurrentFormSessionKey());
            }
            $variables['success'] = $sessionForm->isProcessed();

            // Debugging
            if ((boolean) $this->settings['debug']) {
                DebuggerUtility::var_dump(['newForm' => $newForm, 'sessionForm' => $sessionForm], __METHOD__);
            }

            // Redirect to success page if set
            if ((boolean) $this->settings['successPid'] && $this->settings['submitMode'] !== 'background' && (boolean) $this->settings['enableAjaxSubmit'] === false) {
                $this->redirect(null, null, null, null, $this->settings['successPid']);
            }

            // Send redirect url in header for FE-JavaScript
            if ((boolean) $this->settings['enableAjaxSubmit'] === true && (boolean) $this->settings['successPid']) {
                header($this->request->getControllerExtensionName().'_RedirectUrl: '. $this->uriBuilder->setTargetPageUid( $this->settings['successPid'])->setCreateAbsoluteUri(true)->build());
            }
            //
        } catch (\Exception $ex) {
            $this->throwActionControllerException($ex, LocalizationUtility::translate('Form.error', $this->extKey));
        }

        /*
         * Assign data to view
         */
        if ($this->settings['submitMode'] === 'background') {
            $this->view->assign('value', ['formProcessed' => $sessionForm->isProcessed()]);
        } else {
            $this->view->assignMultiple([...$variables, 'actionMethodName' => $this->actionMethodName]);
        }
    }

    /**
     * AJAX Validation Action (check model-validation)
     *
     * @param string $field
     * @param string $value
     */
    public function ajaxValidateAction($field = null, $value = null): ResponseInterface
    {

        $variables = [
            'isValid' => false,
            'messages' => []
        ];

        $propertyValidators = [];

        try {
            if (is_string($field) && strpos($field, '][') !== false && $value !== null) {

                // Extract property (field)
                $property = array_pop(GeneralUtility::trimExplode(']', str_replace('[', '', $field), true));

                // Get Form´s properties
                $tempForm   = GeneralUtility::makeInstance($this->settings['formModelClass']);
                $properties = array_keys($tempForm->_getProperties());

                // check property
                if (!in_array($property, $properties)) {
                    throw new \Exception('Property '.htmlentities($property).' not valid.');
                }

                // Get property validators
                $validatorResolver    = $this->objectManager->get(ValidatorResolver::class);
                $conjunctionValidator = $validatorResolver->getBaseValidatorConjunction($this->settings['formModelClass']);

                if ($conjunctionValidator instanceof ConjunctionValidator) {
                    $genericObjectValidators = $conjunctionValidator->getValidators();
                    if ($genericObjectValidators->count() > 0) {
                        foreach ($genericObjectValidators as $genericObjectValidator) {
                            $propertyValidators = array_merge($propertyValidators, $genericObjectValidator->getPropertyValidators());
                        }
                    }
                }

                /*
                 *  If property is mapped to a model (special case in single field validation)
                 *
                 *  1. We extract from property´s reflection the mapped class name (@var \Vendor\MyExt\Classes\Domain\Model\MappedClass).
                 *  2. Get the persistent object by uid ($value)
                 *  3. Overwrite $value for validation with persistent object or null if not exisist
                 */
                /*
                $propertyReflection = new PropertyReflection(get_class($tempForm), $property);
                $propertyTags       = $propertyReflection->getTagsValues();
                if (isset($propertyTags['var'][0])) {

                    $rawVar = $propertyTags['var'][0];
                    if (substr($rawVar, 0, 1) === '\\') {
                        $propertyClassName = implode('\\', GeneralUtility::trimExplode('\\', $rawVar, true));
                        if (class_exists($propertyClassName)) {
                            $value = $this->basicUtility->getPersistentObject($value, $propertyClassName);
                        }
                    }
                }
                */

                // Validate property
                if (!empty($propertyValidators) && array_key_exists($property, $propertyValidators)) {

                    foreach ($propertyValidators[$property] as $validator) {

                        $result = $validator->validate($value);
                        if ($result->hasErrors()) {
                            foreach ($result->getErrors() as $error) {
                                $variables['messages'][] = $error->getMessage();
                            }
                        }
                    }
                }

                // Has property some error messages?
                if (count($variables['messages']) === 0) {
                    $variables['isValid'] = true;
                }
            }
        } catch (\Exception $ex) {
            $variables['messages'][] = $ex->getMessage().' '.$ex->getFile().' line '.$ex->getLine();
            header('HTTP/1.1 500 Internal Server Error');
        }

        $this->view->assign('value', $variables);
        return $this->htmlResponse();
    }

    /**
     * Upload media if exists and set form´s property
     *
     * @param array $media
     * @param Form $newForm
     * @param boolean $force forces overwrite
     * @throws \Exception
     */
    private function uploadAndSetMediaForForm(&$media = [], &$newForm = null, $force = false)
    {
        if (is_array($media) && $media['tmp_name'] && file_exists($media['tmp_name'])) {

            // Validate file size
            if (($media['size'] >= $this->basicUtility->returnBytes(ini_get('upload_max_filesize'))) || $media['size'] == 0) {
                throw new \Exception('File too large. File must be less than '.ini_get('upload_max_filesize'));
            }

            // Get fileidentifier and go on
            if ($fileIdentifier = $this->getFileIdentifierForForm($media['name'], $newForm)) {
                $source      = $media['tmp_name'];
                $destination = GeneralUtility::getFileAbsFileName('uploads/tx_abavoform/'.$fileIdentifier.'.dat');

                // If the impossible case target file exist occurs (should only in TYPO3-BE), we stop here with critical error.
                if ($force === false && file_exists($destination)) {
                    throw new \Exception('File duplicate exception');
                }

                // Move uploaded file to destination
                if (move_uploaded_file($source, $destination) === false) {
                    throw new \Exception('Upload failed.');
                }

                // Set metadata for media
                $media['identifier'] = $fileIdentifier;
                $media['extension']  = 'dat';
                unset($media['tmp_name']);
                $newForm->setMedia($media);
            }
        }
    }

    /**
     * Get the fileidentifier for a order
     *
     * @param string $filename
     * @param Form $form object
     * @return mixed
     */
    private function getFileIdentifierForForm($filename = '', $form = null)
    {
        if ($filename !== '' && $form instanceof Form) {
            return $form->getDatetime()->format('Y-m-d').'_'.md5($form->getUniqueId().'-'.$filename);
        }
        return false;
    }

    public function injectSessionService(SessionService $sessionService): void
    {
        $this->sessionService = $sessionService;
    }

    public function injectFormSanitation(FormSanitation $formSanitation): void
    {
        $this->formSanitation = $formSanitation;
    }

    public function injectStaticCountryRepository(CountryRepository $staticCountryRepository): void
    {
        $this->staticCountryRepository = $staticCountryRepository;
    }

    public function injectStaticCountryZoneRepository(CountryZoneRepository $staticCountryZoneRepository): void
    {
        $this->staticCountryZoneRepository = $staticCountryZoneRepository;
    }
}