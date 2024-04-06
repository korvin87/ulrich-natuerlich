<?php
defined('TYPO3') or die();

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$ttcontentSysFileReferenceColumns = [
    'tx_sitepackage_posterimage' => [
        'label' => 'LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:sys_file_reference.tx_sitepackage_posterimage.label',
        'exclude' => 1,
        'config' => ExtensionManagementUtility::getFileFieldTCAConfig('tx_sitepackage_posterimage', ['appearance' => ['createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'], 'minitems' => 0, 'maxitems' => 1], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
    ],
];

ExtensionManagementUtility::addTCAcolumns('sys_file_reference', $ttcontentSysFileReferenceColumns);

ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'videoOverlayPalette', '--linebreak--,tx_sitepackage_posterimage', 'after:autoplay');
