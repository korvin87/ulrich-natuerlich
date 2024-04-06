RTE {
    default {
        preset = sitepackage
    }
}

TCEMAIN {
    default {
        disablePrependAtCopy = 1
    }

    permissions {
        groupid = 1
        user = show,edit,delete,new,editcontent
        group = show,edit,delete,new,editcontent
        everybody =
    }

    linkHandler {
        tx_ulrichproducts {
            handler = TYPO3\CMS\Recordlist\LinkHandler\RecordLinkHandler
            label = Produkte
            configuration {
                table = tx_ulrichproducts_domain_model_product
                storagePid = 15
                hidePageTree = 1
            }
            scanBefore = page
        }
        productcategories {
            handler = TYPO3\CMS\Recordlist\LinkHandler\RecordLinkHandler
            label = Produktkategorien
            configuration {
                table = sys_category
                storagePid = 15
                hidePageTree = 1
            }
            scanBefore = page
        }
    }
}

TCEFORM {
    pages {
        layout {
            label = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.layout.label
            altLabels {
                0 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.layout.0
            }
            addItems {
                10 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:h-style.10
                20 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:h-style.20
                30 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:h-style.30
                40 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:h-style.40
                50 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:h-style.50
                100 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.layout.100
            }
            removeItems = 1,2,3
        }
        media {
            label = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.media.label
        }
        backend_layout {
            label = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.backend_layout.label
            removeItems = -1, 0
        }
        backend_layout_next_level {
            label = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.backend_layout_next_level.label
            removeItems = -1, 0
        }
        target {
            addItems {
                _blank = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.target._blank
                lightbox = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:pages.target.lightbox
            }
        }
    }
    pages_language_overlay < .pages
    tt_content {
        header_layout {
            altLabels {
                1 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.1
                2 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.2
                3 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.3
                4 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.4
                5 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.5
            }
            addItems {
                10 = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.header_layout.10
            }
        }
        frame_class {
            label.de = Darstellung
            removeItems = ruler-before,ruler-after,indent,indent-left,indent-right,none
            altLabels {
                default = Standard
            }
            addItems {
                box = Als Box
            }
        }
        layout{
            addItems {
                10 = Fachhändler
            }
        }
        tx_sitepackage_headerstyle {
            addItems < TCEFORM.pages.layout.addItems
            removeItems = 100
        }
    }

    tx_powermail_domain_model_form {
        css {
            removeItems = layout1, layout2, layout3, nolabel
            addItems {
                default-form--2-columns = Zweispaltig
            }
        }
    }
    tx_powermail_domain_model_page {
        css {
            removeItems = layout1, layout2, layout3
        }
    }
    tx_powermail_domain_model_field {
        css {
            removeItems = layout1, layout2, layout3
            altLabels {
                nolabel = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_field.css.nolabel
            }
            addItems {
                short = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tx_powermail_domain_model_field.css.short
            }
        }
    }
}

TCAdefaults {
    pages {
        hidden = 1
        tx_sitepackage_headlinealign = center
    }
    tt_content {
        tx_sitepackage_headerstyle = 20
        header_layout = 2
        imagecols = 1
        imageorient = 2
        filelink_size = 1
        uploads_description = 1
        uploads_type = 1
    }
    tx_abavomaps_domain_model_map {
        width = 600
        height = 350
        zoom = 15
        zoomcontrol = 1
        pancontrol = 1
        scalecontrol = 1
    }
}

mod {
    SHARED {
        defaultLanguageFlag = de
        defaultLanguageLabel = Deutsch
    }
    wizards.newContentElement.wizardItems {
        common {
            elements {
                textmedia.tt_content_defValues.imageorient < TCAdefaults.tt_content.imageorient
            }
        }
        menu {
            elements {
                menu_product_categories {
                    iconIdentifier = content-menu-categorized
                    title = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.menu_product_categories.label
                    description = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.menu_product_categories.description
                    tt_content_defValues {
                        CType = menu_product_categories
                    }
                }
            }
            show = *
        }
        dev {
            header = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.wizardItems.dev
            elements {
                tx_sitepackage_tsobject {
                    iconIdentifier = content-header
                    title = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.tx_sitepackage_tsobject.label
                    description = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tt_content.tx_sitepackage_tsobject.description
                    tt_content_defValues {
                        CType = tx_sitepackage_tsobject
                    }
                }
            }
            show = *
        }
    }
}

tx_powermail {
    flexForm {
        type {
            addFieldOptions {
                privacylink = LLL:EXT:site_package/Resources/Private/Language/locallang_db.xlf:tx_powermail.tx_powermail_domain_model_field.privacylink.label
            }
        }
    }
}

tx_csseo {
    products = tx_ulrichproducts_domain_model_product
    products {
        enable = GP:tx_ulrichproducts_pi|product
        fallback {
            title = title
            description = description
            og_image = media
        }
        evaluation {
            getParams = &tx_ulrichproducts_pi[controller]=Product&tx_ulrichproducts_pi[action]=show&tx_ulrichproducts_pi[product]=
            detailPid = 14
        }
    }
    categories = sys_category
    categories {
        enable = GP:tx_ulrichproducts_pi|category
        fallback {
            title = title
        }
        evaluation {
            getParams = &tx_ulrichproducts_pi[controller]=Product&tx_ulrichproducts_pi[action]=show&tx_ulrichproducts_pi[category]=
            detailPid = 14
        }
    }
}
