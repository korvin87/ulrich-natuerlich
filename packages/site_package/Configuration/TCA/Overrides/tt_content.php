<?php
defined('TYPO3') or die();

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


$frontendLanguageFilePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';
$customLanguageFilePrefix = 'LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:';


/*
	Eigene Inhaltselemente definieren
*/

$prependFields = '--palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,';
$headerFields = '--palette--;' . $frontendLanguageFilePrefix . 'palette.headers;headers,';
$appendFields = ',
	--div--;' . $frontendLanguageFilePrefix . 'tabs.appearance,
        --palette--;' . $frontendLanguageFilePrefix . 'palette.frames;frames,
        --palette--;' . $frontendLanguageFilePrefix . 'palette.appearanceLinks;appearanceLinks,
     --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
        --palette--;;language,
     --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
        --palette--;;hidden,
        --palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,
     --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        categories,
     --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
        rowDescription,
     --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
';

if (ExtensionManagementUtility::isLoaded('gridelements')) {
    $appendFields = $appendFields . ', tx_gridelements_container, tx_gridelements_columns';
}

// TS-Objekt

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        $customLanguageFilePrefix . 'tt_content.tx_sitepackage_tsobject.label',
        'tx_sitepackage_tsobject',
        'content-header'
    ],
    'html',
    'after'
);

$GLOBALS['TCA']['tt_content']['types']['tx_sitepackage_tsobject']['showitem'] = $prependFields . 'header' . $appendFields;

// TS-Objekt

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        $customLanguageFilePrefix . 'tt_content.menu_product_categories.label',
        'menu_product_categories',
        'content-menu-categorized'
    ],
    'html',
    'after'
);

$GLOBALS['TCA']['tt_content']['types']['menu_product_categories']['showitem'] = $prependFields . $headerFields . $appendFields;


/*
	Neue Felder zur Tabelle 'tt_content' hinzufügen
*/

$tt_contentColumns = [
    'tx_sitepackage_headerstyle' => [
        'exclude' => 1,
        'label' => $customLanguageFilePrefix . 'tt_content.headerstyle.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle'
        ],
    ],
    'tx_sitepackage_bgcolor' => [
        'exclude' => 1,
        'label' => $customLanguageFilePrefix . 'bgcolor.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [$customLanguageFilePrefix . 'inherit', 'inherit'],
                [$customLanguageFilePrefix . 'color.grey-light', 'grey-light'],
                [$customLanguageFilePrefix . 'color.turkis', 'turkis'],
                [$customLanguageFilePrefix . 'color.blue-dark', 'blue-dark'],
            ],
            'default' => 'inherit'
        ],
    ],
    'tx_sitepackage_textalign' => [
        'exclude' => 1,
        'label' => $customLanguageFilePrefix . 'textalign.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [$customLanguageFilePrefix . 'inherit', 'inherit'],
                [$customLanguageFilePrefix . 'textalign.left', 'left'],
                [$customLanguageFilePrefix . 'textalign.center', 'center'],
                [$customLanguageFilePrefix . 'textalign.right', 'right'],
            ],
            'default' => 'inherit'
        ],
    ],
    'tx_sitepackage_fontsize' => [
        'exclude' => 1,
        'label' => $customLanguageFilePrefix . 'fontsize.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [$customLanguageFilePrefix . 'inherit', 'inherit'],
                [$customLanguageFilePrefix . 'fontsize.normal', 'normal'],
            ],
            'default' => 'inherit'
        ],
    ],
    'tx_sitepackage_fontcolor' => [
        'exclude' => 1,
        'label' => $customLanguageFilePrefix . 'tt_content.tx_sitepackage_fontcolor.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [$customLanguageFilePrefix . 'inherit', 'inherit'],
                [$customLanguageFilePrefix . 'fontcolor.black', 'black'],
                [$customLanguageFilePrefix . 'fontcolor.white', 'white'],
            ],
            'default' => 'inherit'
        ],
    ],
    'tx_sitepackage_bgimage' => [
        'label' => $customLanguageFilePrefix . 'tt_content.tx_sitepackage_bgimage.label',
        'exclude' => 1,
        'config' => ExtensionManagementUtility::getFileFieldTCAConfig('tx_sitepackage_bgimage', ['appearance' => ['createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'], 'minitems' => 0, 'maxitems' => 1], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
    ],
    'tx_sitepackage_bgimagesize' => [
        'exclude' => 1,
        'label' => $customLanguageFilePrefix . 'tt_content.tx_sitepackage_bgimagesize.label',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [$customLanguageFilePrefix . 'tt_content.tx_sitepackage_bgimagesize.cover', 'cover'],
                [$customLanguageFilePrefix . 'tt_content.tx_sitepackage_bgimagesize.contain', 'contain'],
            ],
            'default' => 'cover'
        ],
    ],
    'tx_sitepackage_bgcoloroverimage' => [
        'exclude' => 1,
        'label' => $customLanguageFilePrefix . 'tt_content.tx_sitepackage_bgcoloroverimage.label',
        'config' => [
            'type' => 'check',
            'default' => 0
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns('tt_content', $tt_contentColumns);

ExtensionManagementUtility::addFieldsToPalette('tt_content', 'header', 'tx_sitepackage_headerstyle', 'after:header_layout');
ExtensionManagementUtility::addFieldsToPalette('tt_content', 'headers', 'tx_sitepackage_headerstyle', 'after:header_layout');

ExtensionManagementUtility::addFieldsToPalette('tt_content', 'paletteBackground', '
	tx_sitepackage_bgcolor,tx_sitepackage_bgcoloroverimage,--linebreak--,tx_sitepackage_bgimage,tx_sitepackage_bgimagesize
');
ExtensionManagementUtility::addFieldsToPalette('tt_content', 'paletteText', '
	tx_sitepackage_textalign,tx_sitepackage_fontsize,tx_sitepackage_fontcolor
');

ExtensionManagementUtility::addToAllTCAtypes('tt_content', '
	--palette--;LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.palette.background;paletteBackground,
	--palette--;LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.palette.text;paletteText
', '', 'after:space_after_class');
