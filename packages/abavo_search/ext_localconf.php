<?php
/*
 * abavo_search
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

/**
 * @author mbruckmoser
 */
if (!defined('TYPO3')) {
    die('Access denied.');
}


// FE-PLUGINS
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AbavoSearch', 'Pisearch',
    // cacheable actions
    [\Abavo\AbavoSearch\Controller\SearchController::class => 'form'],
    // non-cacheable actions
    [\Abavo\AbavoSearch\Controller\SearchController::class => 'form']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AbavoSearch', 'Piresults',
    // cacheable actions
    [\Abavo\AbavoSearch\Controller\SearchController::class => 'search'],
    // non-cacheable actions
    [\Abavo\AbavoSearch\Controller\SearchController::class => 'search']
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AbavoSearch', 'PiData',
    // cacheable actions
    [\Abavo\AbavoSearch\Controller\AjaxController::class => 'autocomplete, hitindex', \Abavo\AbavoSearch\Controller\ApiController::class => 'indexer'],
    // non-cacheable actions
    [\Abavo\AbavoSearch\Controller\AjaxController::class => 'autocomplete, hitindex', \Abavo\AbavoSearch\Controller\ApiController::class => 'indexer']
);

// Configure PageTSConfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:abavo_search/Configuration/TypoScript/pageTSConfig.ts">');

// SCHEDULER COMMAND CONTROLLER
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Abavo\AbavoSearch\Controller\IndexCommandController::class;

// REGISTER SEARCH-SERVICES-CLASSES
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['searchServiceClasses'][\Abavo\AbavoSearch\Domain\Service\IndexSearchService::class] = [
    'label' => ['de' => 'Base SearchService']
];

/*
 * REGISTER TERM-SERVICE-CLASS
 * To register your own class simple extend \Abavo\AbavoSearch\Domain\Service\TermService class and overwrite "findForAutocomplete" method
 * and return array with \Abavo\AbavoSearch\Domain\Model\AutocompleteItem objects
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['termServiceClasses']['default'] = \Abavo\AbavoSearch\Domain\Service\TermService::class;

// REGISTER INDEXER

if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getVersion()) >= 9_005_000) {
    // ContentIndexer
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'][] = [
    'Content' => [
        'Label' => 'Content Indexer',
        'FlexForm' => 'FILE:EXT:abavo_search/Configuration/FlexForms/ContentIndexerConfig.xml'],
    'targetPid' => false
];
} else {
    // PageIndexer
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'][] = [
        'Page' => [
            'Label' => 'Page Indexer',
            'FlexForm' => 'FILE:EXT:abavo_search/Configuration/FlexForms/PageIndexerConfig.xml'],
        'targetPid' => false
    ];
}

// FalIndexer
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'][] = [
    'Fal' => [
        'Label' => 'FAL Indexer',
        'FlexForm' => 'FILE:EXT:abavo_search/Configuration/FlexForms/FalIndexerConfig.xml'],
    'targetPid' => false
];

// UrlIndexer
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'][] = [
    'Url' => [
        'Label' => 'URL Indexer',
        'FlexForm' => 'FILE:EXT:abavo_search/Configuration/FlexForms/UrlIndexerConfig.xml'],
    'targetPid' => false
];

// ApiIndexer
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'][] = [
    'Api' => [
        'Label' => 'API Indexer',
        'FlexForm' => 'FILE:EXT:abavo_search/Configuration/FlexForms/ApiIndexerConfig.xml'],
    'targetPid' => false
];

// NEWSIndexer
if (in_array('news', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getLoadedExtensionListArray())) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'][] = [
        'News' => [
            'Label' => 'News Indexer',
            'FlexForm' => 'FILE:EXT:abavo_search/Configuration/FlexForms/NewsIndexerConfig.xml'],
        'targetPid' => true
    ];

// NEWSIndex MODIFY HOOK
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['modifyIndex']['News'][] = \Abavo\AbavoSearch\Hooks\NewsIndexModifier::class;
}

// autocomplete term von cHash-Kalkulation ausschließen
#$GLOBALS['TYPO3_CONF_VARS']['FE']['cHashExcludedParameters'] .= ',tx_abavosearch_pidata[term]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavosearch_pidata[term]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavosearch_piresults[search]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavosearch_piresults[controller]';
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'][] = 'tx_abavosearch_piresults[action]';

// Add configuration for the logging API
$logFilePath = 'typo3temp/logs/';
if (version_compare(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getBranch(), '9.5', '>=') && \TYPO3\CMS\Core\Core\Environment::isComposerMode()) {
	$logFilePath = \TYPO3\CMS\Core\Core\Environment::getProjectPath(). '/var/log/';
}
$GLOBALS['TYPO3_CONF_VARS']['LOG']['Abavo']['AbavoSearch']['Controller']['IndexCommandController']['writerConfiguration'] = [
    // configuration for NOTICE severity, including all
    // levels with higher severity (WARNING, ERROR, CRITICAL, EMERGENCY)
    \TYPO3\CMS\Core\Log\LogLevel::NOTICE => [
        // add a FileWriter
        \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            // configuration for the writer
            'logFile' => $logFilePath.'abavo_search'.'-IndexCommandController.log'
        ]
    ]
];
$GLOBALS['TYPO3_CONF_VARS']['LOG']['Abavo']['AbavoSearch']['Hooks']['ContentModifier']['writerConfiguration']             = [
    // configuration for NOTICE severity, including all
    // levels with higher severity (WARNING, ERROR, CRITICAL, EMERGENCY)
    \TYPO3\CMS\Core\Log\LogLevel::NOTICE => [
        // add a FileWriter
        \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            // configuration for the writer
            'logFile' => $logFilePath.'abavo_search'.'-ContentModifier.log'
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
            'apps-pagetree-folder-contains-abavo_search' => 'storage.svg',
            'pi-abavo_search' => 'abavo_search.svg',
            'indexer-abavo_search' => 'settings.svg',
            'update-abavo_search' => 'update.svg',
            'wizard-abavo_search' => 'abavo_search.svg'
        ]
    ];

    foreach ($icons as $provider => $group) {
        foreach ($group as $identifier => $file) {
            $iconRegistry->registerIcon(
                $identifier, $provider, ['source' => 'EXT:abavo_search/Resources/Public/Icons/'.$file]
            );
        }
    }
}

/*
 * Content-Modifier-Hook (Highlighting)
 */
if (TYPO3_MODE === 'FE') {
    // hook is called after Caching / pages with TypoScript COA_/USER_INT objects.
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['abavo_search']       = 'Abavo\AbavoSearch\Hooks\ContentModifier->noCache';
    // hook is called before Caching / pages on their way in the cache.
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['abavo_search']          = 'Abavo\AbavoSearch\Hooks\ContentModifier->cache';
    // Call a hook in the t3lib_pagerenderer.php
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['abavo_search'] = 'Abavo\AbavoSearch\Hooks\ContentModifier->main';
}
