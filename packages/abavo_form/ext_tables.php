<?php
defined('TYPO3') or die();

/**
 * Typoscript-Templates zur Verfügung stellen
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'abavo_form',
    'Configuration/TypoScript',
    'abavo Form Default'
);


/**
 * FE-Plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'AbavoForm',
	'Pi',
	'abavo Formular'
);
$pluginSignature                                                                     = str_replace('_', '', 'abavo_form').'_pi';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.'abavo_form'.'/Configuration/FlexForms/Pi.xml');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key, pages, recursive';
