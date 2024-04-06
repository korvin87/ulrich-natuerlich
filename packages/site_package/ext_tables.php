<?php
defined('TYPO3') or die();

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;


/*
	Typoscript-Templates zur Verfügung stellen
*/

ExtensionManagementUtility::addStaticFile(
    'site_package',
    'Configuration/TypoScript',
    'SitePackage Default'
);


/*
	BE-Module
*/

if (TYPO3_MODE === 'BE') {
    // Get registry info
    $registry                = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Registry::class);
    $registeredDocumentation = $registry->get('Abavo.SitePackage', 'Documentation');

    if ($registeredDocumentation !== '' && file_exists(GeneralUtility::getFileAbsFileName($registeredDocumentation))) {
        ExtensionUtility::registerModule(
            'SitePackage', 'help', 'module', '', [
				\Abavo\SitePackage\Controller\BackendController::class => 'manual', // Allowed controller action combinations
            ],
            [ // Additional configuration
				'access' => 'user, group',
				'icon' => 'EXT:site_package/Resources/Public/Icons/Backend/admin.svg',
				'labels' => 'LLL:EXT:site_package/Resources/Private/Language/locallang_module_help.xlf',
            ]
        );
    }

    ExtensionUtility::registerModule(
        'SitePackage', 'tools', 'module', '', [
			\Abavo\SitePackage\Controller\BackendController::class => 'admin', // Allowed controller action combinations
        ],
        [ // Additional configuration
			'access' => 'admin',
			'icon' => 'EXT:site_package/Resources/Public/Icons/Backend/admin.svg',
			'labels' => 'LLL:EXT:site_package/Resources/Private/Language/locallang_module_admin.xlf',
        ]
    );
}
