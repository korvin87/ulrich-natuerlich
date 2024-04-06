plugin {
    tx_sitepackage {
        view {
            templateRootPaths.0 = {$plugin.tx_sitepackage.view.templateRootPath}
            partialRootPaths.0 = {$plugin.tx_sitepackage.view.partialRootPath}
            layoutRootPaths.0 = {$plugin.tx_sitepackage.view.layoutRootPath}
        }
    }
    tx_csseo {
        sitetitle = {$config.areatitle}
        hreflang {
            enable = 1
            ids = {$config.active_languages}
            keys = {$config.active_languages_isocodes}
        }
        sitemap {
            pages {
                rootPid = {$config.pid_root}
                languageUids = {$config.active_languages}
            }
            extensions {
                products {
                    # return array: uid (uid + category-parameter), lang
                    getRecordsUserFunction = Abavo\UlrichProducts\User\SeoSitemap->getProducts
                    detailPid = {$plugin.tx_ulrichproducts.settings.productPiPid}
                    additionalParams = tx_ulrichproducts_pi[action]=show&tx_ulrichproducts_pi[controller]=Product&tx_ulrichproducts_pi[product]
                    languageUids = {$config.active_languages}
                }
            }
        }
        robots >
        robots =< lib.robots
    }
    tx_abavosearch {
        _LOCAL_LANG {
            default {
                form.autocomplete = Search > Name, INCI, CAS
            }
            de {
                form.autocomplete = Suche > Name, INCI, CAS
            }
        }
    }
    tx_powermail {
        settings {
            setup {
                pid_privacy = {$config.pid_privacy}
                email_privacy = {$config.email_privacy}
                excludeFromPowermailAllMarker {
                    confirmationPage {
                        excludeFromFieldTypes = privacylink
                    }
                    submitPage {
                        excludeFromFieldTypes = privacylink
                    }
                    receiverMail {
                        excludeFromFieldTypes = privacylink
                    }
                    senderMail {
                        excludeFromFieldTypes = privacylink
                    }
                    optinMail {
                        excludeFromFieldTypes = privacylink
                    }
                }
                receiver {
                    overwrite {
                        senderEmail = TEXT
                        senderEmail.value = {$config.mail.sendermail}
                        senderName = TEXT
                        senderName.value = {$config.mail.sendername}
                    }
                }
                sender {
                    overwrite {
                        senderEmail = TEXT
                        senderEmail.value = {$config.mail.sendermail}
                        senderName = TEXT
                        senderName.value = {$config.mail.sendername}
                    }
                }
            }
        }
    }
}
