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

return ['ctrl' => ['title' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_indexer', 'label' => 'title', 'tstamp' => 'tstamp', 'crdate' => 'crdate', 'cruser_id' => 'cruser_id', 'delete' => 'deleted', 'enablecolumns' => ['disabled' => 'hidden', 'starttime' => 'starttime', 'endtime' => 'endtime'], 'searchFields' => 'title,storagepid,target,type,config,', 'iconfile' => ((\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getBranch() === '6.2') ? \TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('abavo_search')) : 'EXT:abavo_search/').'Resources/Public/Icons/settings.svg'], 'types' => ['1' => ['showitem' => 'hidden,--palette--;;1,title,storagepid,target,type,config,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime,endtime'], '1' => ['showitem' => '
				--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility,
				--palette--;Grundeinstellungen;base,categories,
			--div--;Erweitert, locale, config']], 'palettes' => ['visibility' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource;;1, starttime, endtime, hidden', 'canNotCollapse' => 1], 'base' => ['showitem' => 'title, type, priority,--linebreak--, storagepid, --linebreak--, target', 'canNotCollapse' => 1]], 'columns' => ['hidden' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:system.hidden', 'config' => ['type' => 'check']], 'starttime' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:system.starttime', 'config' => ['type' => 'input', 'renderType' => 'inputDateTime', 'size' => 13, 'eval' => 'datetime', 'checkbox' => 0, 'default' => 0, 'range' => ['lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))], 'behaviour' => ['allowLanguageSynchronization' => true]]], 'endtime' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:system.endtime', 'config' => ['type' => 'input', 'renderType' => 'inputDateTime', 'size' => 13, 'eval' => 'datetime', 'checkbox' => 0, 'default' => 0, 'range' => ['lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))], 'behaviour' => ['allowLanguageSynchronization' => true]]], 'title' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_indexer.title', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim,required']], 'storagepid' => ['exclude' => 1, 'label' => 'Search Storage PID', 'config' => ['type' => 'group', 'allowed' => 'pages', 'size' => '1', 'maxitems' => '1', 'minitems' => '0']], 'target' => [
    'exclude' => 1,
    'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_indexer.target',
    // TODO: Fix for TYPO 8.7.0 'displayCond' => array('OR' => array()),
    'config' => ['type' => 'group', 'allowed' => 'pages', 'size' => '1', 'maxitems' => '1', 'minitems' => '0'],
], 'type' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_indexer.type', 'onchange' => 'reload', 'config' => ['type' => 'select', 'renderType' => 'selectSingle', 'items' => [
    -1 => [0 => 'Choose', 1 => '--div--']
], 'size' => 1, 'minitems' => 1, 'maxitems' => 1]], 'categories' => ['exclude' => 1, 'label' => 'Index zusätzliche Kategorien zuweisen', 'config' => ['type' => 'select', 'renderType' => 'selectTree', 'foreign_table' => 'sys_category', 'foreign_table_where' => ' AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.title ASC', 'renderMode' => ((\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getBranch() === '6.2') ? 'tree' : ''), 'maxitems' => 9999, 'treeConfig' => ['parentField' => 'parent', 'appearance' => ['expandAll' => true, 'showHeader' => true]]]], 'locale' => ['exclude' => 1, 'label' => 'Titel-Local-Mapping (zeilengetrennt; z.B: en=MyTitle)', 'config' => ['type' => 'text', 'cols' => 30, 'rows' => 3, 'wrap' => 'off', 'eval' => 'trim']], 'config' => ['exclude' => 1, 'label' => 'Indexer FlexForm', 'displayCond' => 'FIELD:type:REQ:true', 'config' => ['type' => 'flex', 'ds_pointerField' => 'type', 'ds' => [
    'default' => 'FILE:EXT:abavo_search/Configuration/FlexForms/DefaultIndexerConfig.xml',
]]], 'priority' => ['exclude' => 1, 'label' => 'Priorität (0=niedrig, 5=hoch)', 'config' => [
    'type' => 'input',
    'size' => 5,
    'eval' => 'trim,int',
    'range' => [
        'lower' => 0,
        'upper' => 5,
    ],
    'default' => 0,
    'slider' => [
        'step' => 1,
        'width' => 200,
    ],
]]]];
