config {
    areatitle = W. Ulrich GmbH
    areaname = ulrich
    l10nfile = EXT:site_package/Resources/Private/Language/locallang.xlf
    pid_root = 1
    pid_footer = 8
    pid_metamenu = 38
    pid_privacy = 13
    pid_contact = 9
    pid_generalcontent = 39
    uid_phone = 65
    uid_mail = 66
    active_languages = 0,1
    active_languages_isocodes = de,en,fr
    active_languages_labels_optionsplit = Deutsch || English || Français
    active_languages_additionalParams_optionsplit = &L= || &L=1 || &L=2
    email_privacy = info@ulrichgmbh.de

    tx_abavosearch.contentModifier.wrap = <span class="ce-sword">###SWORD###</span>

    # Grid- und Bildbreitenberechung
    container_width = 1140
    gridpadding = 30
    headerslider_defaultheight = 500

    debug = 0

    mail {
        sendername = W. ULRICH GmbH
        sendermail = info@ulrichgmbh.de
    }
}

styles {
    templates {
        templateRootPath = EXT:site_package/Resources/Private/Vendor/Content/Templates/
        partialRootPath = EXT:site_package/Resources/Private/Vendor/Content/Partials/
        layoutRootPath = EXT:site_package/Resources/Private/Vendor/Content/Layouts/
    }
    content {
        defaultHeaderType = 2
        textmedia {
            columnSpacing = 15
            rowSpacing = 15
            textMargin = 20
            linkWrap {
                lightboxEnabled = 1
                width = 1200
            }
            borderWidth = 1
            floatimagemaxwidthpercent = 40
        }
    }
}

plugin {
    tx_sitepackage {
        view {
            # cat=plugin.site_package/file; type=string; label=Path to template root (FE)
            templateRootPath = EXT:site_package/Resources/Private/Templates/
            # cat=plugin.site_package/file; type=string; label=Path to template partials (FE)
            partialRootPath = EXT:site_package/Resources/Private/Partials/
            # cat=plugin.site_package/file; type=string; label=Path to template layouts (FE)
            layoutRootPath = EXT:site_package/Resources/Private/Layouts/
        }
    }
    tx_powermail {
        view {
            templateRootPath = EXT:site_package/Resources/Private/Vendor/Powermail/Templates/
            partialRootPath = EXT:site_package/Resources/Private/Vendor/Powermail/Partials/
            layoutRootPath = EXT:site_package/Resources/Private/Vendor/Powermail/Layouts/
        }
        settings {
            misc {
                ajaxSubmit = 1
            }
            javascript {
                addJQueryFromGoogle = 0
            }
            styles {
                framework {
                    formClasses = default-form textalign-left
                    fieldAndLabelWrappingClasses = row
                    fieldWrappingClasses = entry powermail_field
                    labelClasses = label powermail_label
                    offsetClasses = 
                    radioClasses = radiobutton
                    checkClasses = checkbox
                }
            }
            sender {
                addDisclaimerLink = 0
            }
        }
    }
    tx_abavosearch {
        view {
            templateRootPath = EXT:site_package/Resources/Private/Vendor/AbavoSearch/Templates/
            partialRootPath = EXT:site_package/Resources/Private/Vendor/AbavoSearch/Partials/
            layoutRootPath = EXT:site_package/Resources/Private/Vendor/AbavoSearch/Layouts/
        }
        persistence {
            storagePid = 6
        }
        settings {
            targetPid = 7
            cssFile = 
            jsGlobalInclude = 1
            autocompleteLibrary = tooltipster
            jsTooltipster = 
            cssTooltipster = 
        }
    }
    tx_abavomaps {
        settings {
            markerDefaultIcon = typo3conf/ext/site_package/Resources/Public/Icons/location.png
            gmApiKey = AIzaSyCeoau597XZqUv1BkqxOihaNJTXTm5kmF0
        }
    }
}
