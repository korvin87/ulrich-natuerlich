<?php
if (!defined('TYPO3')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'abavo_maps',
	'Pimain',
	'abavo Maps'
);

// Static TypoScript Template
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('abavo_maps', 'Configuration/TypoScript', 'abavo Maps');

// Map
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_abavomaps_domain_model_map', 'EXT:abavo_maps/Resources/Private/Language/locallang_csh_tx_abavomaps_domain_model_map.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_abavomaps_domain_model_map');

// Marker
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_abavomaps_domain_model_marker', 'EXT:abavo_maps/Resources/Private/Language/locallang_csh_tx_abavomaps_domain_model_marker.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_abavomaps_domain_model_marker');

// Shape
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_abavomaps_domain_model_shape', 'EXT:abavo_maps/Resources/Private/Language/locallang_csh_tx_abavomaps_domain_model_shape.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_abavomaps_domain_model_shape'); //TODO: insert config if feature is ready to use

// Adding Flexforms
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['abavomaps_pimain'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('abavomaps_pimain', 'FILE:EXT:'.'abavo_maps'.'/Configuration/FlexForms/ControllerActions.xml');

// Disable not needed BE-fields in plugins
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['abavomaps_pimain']='select_key';

// Add to Element Wizard    
if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['abavo_maps_pimain_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('abavo_maps') . 'Classes/User/Wizicon.php';
}


// Add TT_ADDRESS-FIELDS
if (in_array('tt_address', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getLoadedExtensionListArray())) {
	$GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList'] .= ',longitude,latitude';
	$GLOBALS['TCA']['tt_address']['columns']['longitude'] = ['exclude' => 0, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.longitude', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim']];
	$GLOBALS['TCA']['tt_address']['columns']['latitude'] = ['exclude' => 0, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.latitude', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim']];
	$GLOBALS['TCA']['tt_address']['types']['1']['showitem'] .= ',--palette--;LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.geodatatitle;abavomaps';
	$GLOBALS['TCA']['tt_address']['palettes']['abavomaps'] = ['showitem' => 'longitude, --linebreak--,latitude', 'canNotCollapse' => 1];
}

// Add FEUSERS-FIELDS
$GLOBALS['TCA']['fe_users']['interface']['showRecordFieldList'] .= ',longitude,latitude';
$GLOBALS['TCA']['fe_users']['columns']['longitude'] = ['exclude' => 0, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.longitude', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim']];
$GLOBALS['TCA']['fe_users']['columns']['latitude'] = ['exclude' => 0, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.latitude', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim']];
$GLOBALS['TCA']['fe_users']['types']['0']['showitem'] .= ',--palette--;LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.geodatatitle;abavomaps';
$GLOBALS['TCA']['fe_users']['palettes']['abavomaps'] = ['showitem' => 'longitude, --linebreak--,latitude', 'canNotCollapse' => 1];

// Add NN_ADDRESS-FIELDS
if (in_array('nn_address', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getLoadedExtensionListArray())) {
	$GLOBALS['TCA']['tx_nnaddress_domain_model_address']['interface']['showRecordFieldList'] .= ',longitude,latitude';
	$GLOBALS['TCA']['tx_nnaddress_domain_model_address']['columns']['longitude'] = ['exclude' => 0, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.longitude', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim']];
	$GLOBALS['TCA']['tx_nnaddress_domain_model_address']['columns']['latitude'] = ['exclude' => 0, 'label' => 'LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.latitude', 'config' => ['type' => 'input', 'size' => 30, 'eval' => 'trim']];
	$GLOBALS['TCA']['tx_nnaddress_domain_model_address']['types']['1']['showitem'] .= ',--palette--;LLL:EXT:abavo_maps/Resources/Private/Language/locallang_db.xlf:tx_abavomaps_domain_model_marker.geodatatitle;abavomaps';
	$GLOBALS['TCA']['tx_nnaddress_domain_model_address']['palettes']['abavomaps'] = ['showitem' => 'longitude, --linebreak--,latitude', 'canNotCollapse' => 1];
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
        'abavo_maps',
        'tx_abavomaps_domain_model_marker',
        'categories',
        [
            // This field should not be an exclude-field
            'exclude' => FALSE,
            // Override generic configuration, e.g. sort by title rather than by sorting
            'fieldConfiguration' => ['foreign_table_where' => ' AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.title ASC', 'treeConfig' => ['parentField' => 'parent', 'appearance' => ['expandAll' => false, 'showHeader' => true, 'width' => 420]]],
        ]
);

