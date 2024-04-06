config {
    compressJs = 0
    compressCss = 1
    concatenateJs = 0
    concatenateCss = 1

    absRefPrefix = auto
    tx_realurl_enable = 1

    linkVars = L(1-10)
    sys_language_uid = 0
    sys_language_isocode_default = de
    language = de
    locale_all = de_DE.UTF-8

    doctype = html5

    sendCacheHeaders = 1
    sendCacheHeaders_onlyWhenLoginDeniedInBranch = 1

    sys_language_mode = strict
    #    sys_language_overlay = hideNonTranslated

    disablePrefixComment = 1

    fileTarget = _blank
    extTarget = _blank
    spamProtectEmailAddresses = 2
    spamProtectEmailAddresses_atSubst = &#064;

    recordLinks {
        tx_ulrichproducts {
            forceLink = 0
            typolink {
                parameter = {$plugin.tx_ulrichproducts.settings.productPiPid}
                additionalParams = &tx_ulrichproducts_pi[product]={field:uid}&tx_ulrichproducts_pi[action]=show&tx_ulrichproducts_pi[controller]=Product
                additionalParams {
                    insertData = 1
                    preCObject = COA
                    preCObject {
                        stdWrap {
                            wrap = &tx_ulrichproducts_pi[category]=|
                            required = 1
                        }
                        10 = LOAD_REGISTER
                        10.productUid.field = uid
                        20 = USER
                        20.userFunc = Abavo\UlrichProducts\User\Category->getCategoryUidByProductUid
                        20.10 = TEXT
                        20.10.data = register:productUid
                        30 = RESTORE_REGISTER
                    }
                }
                ATagParams.data = parameters:allParams
            }
        }
        productcategories {
            forceLink = 0
            typolink {
                parameter = {$plugin.tx_ulrichproducts.settings.productPiPid}
                additionalParams = &tx_ulrichproducts_pi[category]={field:uid}&tx_ulrichproducts_pi[action]=show&tx_ulrichproducts_pi[controller]=Product
                additionalParams {
                    insertData = 1
                }
                ATagParams.data = parameters:allParams
            }
        }
    }
}
