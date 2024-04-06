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

return ['ctrl' => ['title' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_synonym', 'label' => 'title', 'tstamp' => 'tstamp', 'crdate' => 'crdate', 'cruser_id' => 'cruser_id', 'languageField' => 'sys_language_uid', 'transOrigPointerField' => 'l10n_parent', 'transOrigDiffSourceField' => 'l10n_diffsource', 'delete' => 'deleted', 'enablecolumns' => ['disabled' => 'hidden'], 'searchFields' => 'title,synonym', 'iconfile' => ((\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getBranch() === '6.2') ? \TYPO3\CMS\Core\Utility\PathUtility::getAbsoluteWebPath(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('abavo_search')) : 'EXT:abavo_search/') . 'Resources/Public/Icons/synonym.svg'], 'types' => ['1' => ['showitem' => '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility, title, alt, synonym']], 'palettes' => ['visibility' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource;;1, hidden', 'canNotCollapse' => 1]], 'columns' => ['sys_language_uid' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:system.language', 'config' => ['type' => 'language']], 'l10n_parent' => ['displayCond' => 'FIELD:sys_language_uid:>:0', 'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent', 'config' => ['type' => 'select', 'renderType' => 'selectSingle', 'items' => [['', 0]], 'foreign_table' => 'tx_abavosearch_domain_model_synonym', 'foreign_table_where' => 'AND tx_abavosearch_domain_model_synonym.pid=###CURRENT_PID### AND tx_abavosearch_domain_model_synonym.sys_language_uid IN (-1,0)']], 'l10n_diffsource' => ['config' => ['type' => 'passthrough']], 'hidden' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:system.hidden', 'config' => ['type' => 'check']], 'title' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_synonym.title', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim,required,unique']], 'alt' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_synonym.alt', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim,unique']], 'synonym' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:tx_abavosearch_domain_model_synonym.synonym', 'config' => ['type' => 'text', 'cols' => 30, 'rows' => 15, 'wrap' => 'off', 'eval' => 'trim,required']]]];
