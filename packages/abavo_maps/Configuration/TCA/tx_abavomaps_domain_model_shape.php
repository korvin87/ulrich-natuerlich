<?php
if (!defined ('TYPO3')) {
	die ('Access denied.');
}

return ['ctrl' => [
    'title'	=> 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_shape',
    'label' => 'title',
    'tstamp' => 'tstamp',
    'crdate' => 'crdate',
    'cruser_id' => 'cruser_id',
    'sortby' => 'sorting',
    'origUid' => 't3_origuid',
    'languageField' => 'sys_language_uid',
    'transOrigPointerField' => 'l10n_parent',
    'transOrigDiffSourceField' => 'l10n_diffsource',
    'delete' => 'deleted',
    'enablecolumns' => ['disabled' => 'hidden', 'starttime' => 'starttime', 'endtime' => 'endtime'],
    //'requestUpdate' => 'body,color',
    'searchFields' => 'title,color',
    'iconfile' => 'EXT:abavo_maps/Resources/Public/Icons/tx_abavomaps_domain_model_shape.svg',
], 'types' => ['1' => ['showitem' => '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility, title, description, --palette--;Darstellung;base, config']], 'palettes' => ['visibility' => ['showitem' => 'sys_language_uid, starttime, endtime, hidden', 'canNotCollapse' => 1], 'base' => ['showitem' => ' body, color', 'canNotCollapse' => 1]], 'columns' => ['sys_language_uid' => ['exclude' => 1, 'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language', 'config' => ['type' => 'language']], 'l10n_parent' => ['displayCond' => 'FIELD:sys_language_uid:>:0', 'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent', 'config' => ['type' => 'select', 'renderType' => 'selectSingle', 'items' => [['', 0]], 'foreign_table' => 'tx_abavomaps_domain_model_shape', 'foreign_table_where' => 'AND tx_abavomaps_domain_model_shape.pid=###CURRENT_PID### AND tx_abavomaps_domain_model_shape.sys_language_uid IN (-1,0)']], 'l10n_diffsource' => ['config' => ['type' => 'passthrough']], 't3ver_label' => ['label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel', 'config' => ['type' => 'input', 'size' => 30, 'max' => 255]], 'hidden' => ['exclude' => 1, 'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden', 'config' => ['type' => 'check']], 'starttime' => ['exclude' => 1, 'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime', 'config' => ['type' => 'input', 'renderType' => 'inputDateTime', 'size' => 13, 'eval' => 'datetime', 'checkbox' => 0, 'default' => 0, 'range' => ['lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))]]], 'endtime' => ['exclude' => 1, 'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime', 'config' => ['type' => 'input', 'renderType' => 'inputDateTime', 'size' => 13, 'eval' => 'datetime', 'checkbox' => 0, 'default' => 0, 'range' => ['lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))]]], 'title' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_shape.title', 'config' => ['type' => 'input', 'size' => 50, 'eval' => 'trim,required']], 'body' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_shape.body', 'requestUpdate' => 'hidecontent', 'config' => ['type' => 'select', 'renderType' => 'selectSingle', 'items' => [
    ['--bitte wählen--', ''],
    ['Kreis', 'circle'],
    //TOTO: Translation
    ['Rechteck', 'rectangle'],
    ['Polygon', 'polygon'],
], 'minitems' => 1]], 'description' => ['exclude' => 1, 'label' => 'Beschreibung', 'config' => ['type' => 'text', 'cols' => 50, 'rows' => 5]], 'color' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_shape.color', 'config' => ['type' => 'input', 'size' => 10, 'eval' => 'nospace,required', 'renderType' => 'colorpicker']], 'config' => ['exclude' => 1, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_shape.config', 'config' => ['type' => 'user', 'userFunc' => 'TYPO3\AbavoMaps\User\TcaShape->renderMap', 'parameters' => []]]]];

?>
