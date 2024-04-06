<?php
defined('TYPO3') or die();

// FE-PlugIn
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AbavoForm', 'Pi', [
    \Abavo\AbavoForm\Controller\FormController::class => 'new, create'
    ],
    // non-cacheable actions
    [
    \Abavo\AbavoForm\Controller\FormController::class => 'new, create'
    ]
);

// FE-PlugIn
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AbavoForm', 'PiAjaxValidate', [
    \Abavo\AbavoForm\Controller\FormController::class => 'ajaxValidate'
    ],
    // non-cacheable actions
    [
    \Abavo\AbavoForm\Controller\FormController::class => 'ajaxValidate'
    ]
);

/*
 *  Add configuration for the logging API
 */
$GLOBALS['TYPO3_CONF_VARS']['LOG']['Abavo']['AbavoForm']['Controller']['BaseFrontendController']['writerConfiguration'] = [
    // configuration for NOTICE severity, including all
    // levels with higher severity (WARNING, ERROR, CRITICAL, EMERGENCY)
    \TYPO3\CMS\Core\Log\LogLevel::INFO => [
        // add a FileWriter
        \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            // configuration for the writer
            'logFile' => 'uploads/tx_abavoform/Log/BaseFrontendController.log'
        ]
    ]
];

/*
 * Register icons
 */
if (TYPO3_MODE === 'BE') {
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

    $icons = [
         \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class => [
            'wizard-abavo_form' => 'wizard.svg'
        ],
        #\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class => [
        #
        #]
    ];

    foreach ($icons as $provider => $group) {
        foreach ($group as $identifier => $file) {
            $iconRegistry->registerIcon(
                $identifier,
                $provider,
                ['source' => 'EXT:abavo_form/Resources/Public/Icons/'.$file]
            );
        }
    }
}

// Configure PageTSConfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:abavo_form/Configuration/TypoScript/pageTSConfig.ts">');

/*
 * Register Templates
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['new'][] = [
    'label' => 'Default (Form/New.html)',
    'value' => ''
];
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['create'][] = [
    'label' => 'Default (Form/Create.html)',
    'value' => ''
];
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['email'][] = [
    'label' => 'Default (Email/Form.html)',
    'value' => ''
];


/*
 * Register Additional Services
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['services'][] = [
    'label' => 'Database Logging',
    'value' => \Abavo\AbavoForm\Domain\Service\FormRepositoryService::class
];


/*
 * Register Additional Form Models
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['formmodels'][] = [
    'label' => 'Form (Default)',
    'value' => \Abavo\AbavoForm\Domain\Model\Form::class
];

/*
 * Example Session Hook
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['sessionhooks'][] = [
    'label' => 'EXT:abavo_form - Hook Example',
    'value' => \Abavo\AbavoForm\Hooks\SessionHookExample::class
];

/*
 * Exclude params from cHash
 * Found on https://github.com/dmitryd/typo3-realurl/issues/265#issuecomment-248381610
 */

$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[formModelClass]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[action]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[controller]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[format]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[field]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[value]';

$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[formSettingsUid]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_piajaxvalidate[noWrapLayout]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavoform_pi[data]';