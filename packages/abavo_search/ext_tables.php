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

// FE-Plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'abavo_search', 'Pisearch', 'abavo Suche - Eingabeformular', 'pi-abavo_search'
);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'abavo_search', 'Piresults', 'abavo Suche - Suchergebnisse', 'pi-abavo_search'
);

// Plugin-FlexForms
$pluginSignature                                                                     = str_replace('_', '', 'abavo_search').'_pisearch';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:abavo_search/Configuration/FlexForms/Pisearch.xml');

// Disable not needed BE-fields in plugins
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][str_replace('_', '', 'abavo_search').'_pisearch']  = 'select_key,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][str_replace('_', '', 'abavo_search').'_piresults'] = 'select_key,recursive';

// BE-Module
if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'AbavoSearch', 'web', 'module', '', [
            \Abavo\AbavoSearch\Controller\BackendController::class => 'overview, updateindex, indexingstate, diagnosis, cleanup, stats, datatable', // Allowed controller action combinations
        ],
        [// Additional configuration
        'access' => 'user, group',
        'icon' => 'EXT:abavo_search/Resources/Public/Icons/abavo_search.svg',
        'labels' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_module.xlf',
        ]
    );
}

// Static TS-Setup
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('abavo_search', 'Configuration/TypoScript', 'abavo Search');

// Status reports
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_reports']['status']['providers']['abavo Search'][] = \Abavo\AbavoSearch\Report\StatusReport::class;

// Domain Models
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_abavosearch_domain_model_indexer');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_abavosearch_domain_model_synonym');


/*
 * Configuration-Indexer-Hooks
 */
// from EXTCONF
$indexers = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'];
if (!empty($indexers)) {
    $displayCond = '';
    foreach ($indexers as $indexer) {
        $GLOBALS['TCA']['tx_abavosearch_domain_model_indexer']['columns']['type']['config']['items'][] = array(
            current($indexer)['Label'],
            key($indexer));

        $GLOBALS['TCA']['tx_abavosearch_domain_model_indexer']['columns']['config']['config']['ds'][key($indexer)] = current($indexer)['FlexForm'];

        /* DISPLAY-COND */
        if ((boolean) $indexer['targetPid'] === true) {
            // TODO: Fix for TYPO 8.7.0 array_push($GLOBALS['TCA']['tx_abavosearch_domain_model_indexer']['columns']['target']['displayCond']['OR'],'FIELD:type:=:'.key($indexer));
        }
    }
	
}

//from ext_conf_template
$indexers = (explode("\n", unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['abavo_search'])['customIndexers']));
if (!empty($indexers)) {
    foreach ($indexers as $indexer) {
        $displayCond = '';

        $indexer = explode('|', trim($indexer));

        if (current($indexer) != '') {
            $GLOBALS['TCA']['tx_abavosearch_domain_model_indexer']['columns']['type']['config']['items'][] = [$indexer[1], $indexer[0]];

            $GLOBALS['TCA']['tx_abavosearch_domain_model_indexer']['columns']['config']['config']['ds'][$indexer[0]] = $indexer[2];

            // Show field targetPid
            $GLOBALS['TCA']['tx_abavosearch_domain_model_indexer']['columns']['target']['displayCond'] .= ','.$indexer[0];
        }
    }
}

// Register configuration classes for ApiIndexer
\Abavo\AbavoSearch\Domain\Api\Utility::registerJsonViewConfigurationsFromTypoScriptSetup();