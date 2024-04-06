<?php
defined('TYPO3') || die('Access denied.');

call_user_func(
    function() {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'UlrichProducts', 'Pi', 
        [
        \Abavo\UlrichProducts\Controller\CategoryController::class => 'list',
        \Abavo\UlrichProducts\Controller\ProductController::class => 'list, show',
        \Abavo\UlrichProducts\Controller\ContactController::class => 'list'
        ],
        // non-cacheable actions
        [
        \Abavo\UlrichProducts\Controller\ProductController::class => 'list'
        ]
    );
    
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'UlrichProducts', 'Api', [
        \Abavo\UlrichProducts\Controller\ApiController::class => 'products, media'
        ],
        // non-cacheable actions
        [
        \Abavo\UlrichProducts\Controller\ApiController::class => 'products, media'
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    pi {
                        iconIdentifier = ulrich_products-plugin-pi
                        title = LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrich_products_pi.name
                        description = LLL:EXT:ulrich_products/Resources/Private/Language/locallang_db.xlf:tx_ulrich_products_pi.description
                        tt_content_defValues {
                            CType = list
                            list_type = ulrichproducts_pi
                        }
                    }
                }
                show = *
            }
       }'
    );
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

    $iconRegistry->registerIcon(
        'ulrich_products-plugin-pi', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, ['source' => 'EXT:ulrich_products/Resources/Public/Icons/user_plugin_pi.svg']
    );
    
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('abavo_form')) {
        /*
         * Register NEW-Templates
         */
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['new'][] = [
            'label' => 'Produktanfrage',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Form/Product/InquiryNew.html'
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['new'][] = [
            'label' => 'Produktanfrage (Muster)',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Form/Product/PatternInquiryNew.html'
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['new'][] = [
            'label' => 'Dokumentanfrage',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Form/Product/DocumentInquiryNew.html'
        ];

        /*
         * Register CREATE-Templates
         */
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['create'][] = [
            'label' => 'Produktanfrage',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Form/Product/InquiryCreate.html'
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['create'][] = [
            'label' => 'Produktanfrage (Muster)',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Form/Product/PatternInquiryCreate.html'
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['create'][] = [
            'label' => 'Dokumentanfrage',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Form/Product/DocumentInquiryCreate.html'
        ];

        /*
         * Register EMAIL-Templates
         */
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['email'][] = [
            'label' => 'Produktanfrage',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Email/Product/Inquiry'
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['email'][] = [
            'label' => 'Produktanfrage (Muster)',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Email/Product/PatternInquiry'
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['email'][] = [
            'label' => 'Dokumentanfrage',
            'value' => 'EXT:ulrich_products/Resources/Vendor/AbavoForm/Private/Templates/Email/Product/DocumentInquiry'
        ];

        /*
         * Register Additional Form Models
         */
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['formmodels'][] = [
            'label' => 'Produktanfrage',
            'value' => \Abavo\UlrichProducts\Domain\Model\Product\Inquiry::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['formmodels'][] = [
            'label' => 'Produktanfrage (Muster)',
            'value' => \Abavo\UlrichProducts\Domain\Model\Product\PatternInquiry::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['formmodels'][] = [
            'label' => 'Dokumentanfrage',
            'value' => \Abavo\UlrichProducts\Domain\Model\Product\DocumentInquiry::class
        ];
    }
        
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('abavo_search')) {
        // REGISTER SEARCH INDEXER
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['indexers'][] = [
        'Product' => [
            'Label' => 'Product Indexer',
            'FlexForm' => \Abavo\UlrichProducts\Domain\Model\Product\SearchIndexer::CONFIG_FLEXFORM],
            'targetPid' => false
        ];

        // REGISTER TERM-SERVICE-CLASS
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_search']['termServiceClasses'][] = \Abavo\UlrichProducts\Domain\Model\Product\SearchTermService::class;
    }
}
);

/*
 *  Caching framework
 */
if (!is_array($GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products'])) {
    $GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products'] = [];
}
// Cache variables
if (!isset($GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products']['frontend'])) {
    $GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products']['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class;
}
// Cache defaultLifetime
if (!isset($GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products']['options'])) {
    $GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products']['options'] = ['defaultLifetime' => 86400];
}
// Cache groups
if (!isset($GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products']['groups'])) {
    $GLOBALS['TYPO3_CONF_VARS'] ['SYS']['caching']['cacheConfigurations']['ulrich_products']['groups'] = ['pages', 'all'];
}
