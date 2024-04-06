<?php
defined('TYPO3') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'UlrichProducts',
            'Pi',
            'Produkte und Kontakte'
        );

        $pluginSignature = str_replace('_', '', 'ulrich_products') . '_pi';
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:ulrich_products/Configuration/FlexForms/flexform_pi.xml');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ulrich_products', 'Configuration/TypoScript', 'Ulrich Produkte Extension');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ulrichproducts_domain_model_product');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ulrichproducts_domain_model_contact', 'EXT:ulrich_products/Resources/Private/Language/locallang_csh_tx_ulrichproducts_domain_model_contact.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ulrichproducts_domain_model_contact');

    }
);
