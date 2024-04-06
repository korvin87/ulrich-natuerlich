<?php
defined('TYPO3') or die();


$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['sitepackage'] = 'EXT:site_package/Configuration/RTE/Sitepackage.yaml';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:site_package/Configuration/TSconfig/Page/tsconfig.txt">'
);

if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['termServiceClasses']['default']))
    unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['termServiceClasses']['default']);