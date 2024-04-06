<?php
defined('TYPO3') or die();

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$customLanguageFilePrefix = 'LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:';


/*
	Vorhandene Felder von Tabelle 'pages' konfigurieren
*/

$GLOBALS['TCA']['pages']['columns']['target']['config'] = [
    'type' => 'select',
    'renderType' => 'selectSingle',
    'size' => 1,
    'maxitems' => 1,
    'items' => [
        [
            'LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.target.default'
        ]
    ]
];


/*
	Neue Felder zur Tabelle 'pages' hinzufügen
*/

$pagesColumns = [
    'tx_sitepackage_headlinealign' => [
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
    'tx_sitepackage_headerslider_height' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.tx_sitepackage_headerslider_height.label',
        'config' => [
            'type' => 'input',
            'size' => '4',
            'default' => '',
            'eval' => 'int'
        ],
    ],
    'tx_sitepackage_headerslider_mapsmarker' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.tx_sitepackage_headerslider_mapsmarker.label',
        'config' => [
            'type' => 'group',
            'allowed' => 'tx_abavomaps_domain_model_marker',
            'fieldControl' => [
                'editPopup' => [
                    'disabled' => false,
                ],
                'addRecord' => [
                    'disabled' => false,
                ],
                'listModule' => [
                    'disabled' => true,
                ],
            ],
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns('pages', $pagesColumns);

ExtensionManagementUtility::addFieldsToPalette('pages', 'layout', 'tx_sitepackage_headlinealign', 'after:layout');
ExtensionManagementUtility::addFieldsToPalette('pages', 'media', '--linebreak--,tx_sitepackage_headerslider_height,--linebreak--,tx_sitepackage_headerslider_mapsmarker', 'after:media');
