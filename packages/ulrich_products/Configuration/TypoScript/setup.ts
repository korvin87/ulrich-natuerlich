
plugin.tx_ulrichproducts {
    view {
        templateRootPaths.0 = EXT:ulrich_products/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_ulrichproducts.view.templateRootPath}
        partialRootPaths.0 = EXT:ulrich_products/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_ulrichproducts.view.partialRootPath}
        layoutRootPaths.0 = EXT:ulrich_products/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_ulrichproducts.view.layoutRootPath}
    }
    persistence {
        storagePid = {$plugin.tx_ulrichproducts.persistence.storagePid}
        #recursive = 1
    }
    features {
        #skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
    settings {
        productPiPid = {$plugin.tx_ulrichproducts.settings.productPiPid}
        productInquirePid = {$plugin.tx_ulrichproducts.settings.productInquirePid}
        productPatternInquirePid = {$plugin.tx_ulrichproducts.settings.productPatternInquirePid}
        productDocumentInquiryPid = {$plugin.tx_ulrichproducts.settings.productDocumentInquiryPid}
        pageTypeApi {
            products = {$plugin.tx_ulrichproducts.settings.pageTypeApi.products}
            media = {$plugin.tx_ulrichproducts.settings.pageTypeApi.media}
        }
        accordionMediaWidth = {$plugin.tx_ulrichproducts.settings.accordionMediaWidth}
    }
}

[getTSFE().id == {$plugin.tx_ulrichproducts.settings.productPiPid}]
page.includeJSFooter {
    tx_ulrich_products_jquery_inview = EXT:ulrich_products/Resources/Public/Js/jQuery.inView.js
    tx_ulrich_products_productlist = EXT:ulrich_products/Resources/Public/Js/ProductList.js
}
[global]
[getTSFE().id == {$plugin.tx_ulrichproducts.settings.productPiPid} && traverse(request.getQueryParams(), 'tx_ulrichproducts_pi/product') > 0 || traverse(request.getParsedBody(), 'tx_ulrichproducts_pi/product') > 0]
page.includeJSFooter {
    tx_ulrich_products_jquery_inview >
    tx_ulrich_products_productlist >
}
[global]
## EXT:abavo_search partial
plugin.tx_abavosearch.view.partialRootPaths.abavo_calendar = EXT:ulrich_products/Resources/Vendor/AbavoSearch/Private/Partials/


## EXT:abavo_form lib
lib.tx_ulrichproducts.productUidByGp = TEXT
lib.tx_ulrichproducts.productUidByGp {
    data = GP:tx_ulrichproducts_pi|product
    intval = 1
}

## PAGE objects
TxUlrichProductsApiPage = PAGE
TxUlrichProductsApiPage {
    config {
        disableAllHeaderCode = 1
        metaCharset = utf-8
        admPanel = 0
        debug = 0
        no_cache = 1
        xhtml_cleaning = none
    }
    10 = USER_INT
    10 {
        userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
        extensionName = UlrichProducts
        controller = Api
        pluginName = Api
        vendorName = Abavo
        settings < plugin.tx_ulrichproducts.settings
        features {
            requireCHashArgumentForActionArguments = 0
        }
        mvc {
            callDefaultActionIfActionCantBeResolved = 1
        }
    }
}

TxUlrichProductsApiPageProducts < TxUlrichProductsApiPage
TxUlrichProductsApiPageProducts {
    typeNum = {$plugin.tx_ulrichproducts.settings.pageTypeApi.products}
    config.additionalHeaders.10.header = Content-Type:application/json;charset=utf-8
    10 {
        switchableControllerActions {
            Api {
                1 = products
            }
        }
        format = json
    }
}

TxUlrichProductsApiPageMedia < TxUlrichProductsApiPage
TxUlrichProductsApiPageMedia {
    typeNum = {$plugin.tx_ulrichproducts.settings.pageTypeApi.media}
    10 {
        switchableControllerActions {
            Api {
                1 = media
            }
        }
    }
}
