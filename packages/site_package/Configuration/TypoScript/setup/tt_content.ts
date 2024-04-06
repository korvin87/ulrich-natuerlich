tt_content {
    textmedia {
        variables {
            contentMedia < temp.ttcontentimage
        }
    }
    text {
        variables {
            contentMedia < temp.ttcontentimage
        }
    }
    textpic {
        variables {
            contentMedia < temp.ttcontentimage
        }
    }
    image {
        variables {
            contentMedia < temp.ttcontentimage
        }
    }
    menu_product_categories =< lib.contentElement
    menu_product_categories {
        templateName = MenuProductCategories
        variables {
            menu = HMENU
            menu {
                special = userfunction
                special.userFunc = Abavo\UlrichProducts\User\Category->getMenu

                1 = TMENU
                1 {
                    wrap = <ul role="menu">|</ul>

                    NO = 1
                    NO {
                        allWrap = <li role="menuitem">|</li>
                        stdWrap {
                            postCObject = TEXT
                            postCObject {
                                value = </ul><ul role="menu">
                                stdWrap {
                                    if {
                                        value.data = register:count_HMENU_MENUOBJ
                                        equals {
                                            current = 1
                                            setCurrent {
                                                data = register:count_menuItems
                                                wrap = | / 2
                                            }
                                            prioriCalc = 1
                                            round = 1
                                            round {
                                                roundType = ceil
                                                decimals = 0
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ACT < .NO
                    ACT {
                        allWrap = <li class="act" role="menuitem">|</li>
                    }
                }
            }
        }
    }
    tx_sitepackage_tsobject =< lib.contentElement
    tx_sitepackage_tsobject {
        templateName = Tsobject
    }
    gridelements_pi1 >
    gridelements_pi1 =< lib.contentElement
    gridelements_pi1 {
        templateName = Gridelements

        variables {
            gridelementsSetup < temp.gridelementsSetup
            gridelementsSetup {
                15 = LOAD_REGISTER
                15 {
                    # ID der Seiten-Inhaltsspalte (colPos), in welcher das oberste Gridelement liegt, speichern
                    pageColPos {
                        field = colPos
                        override.cObject = COA
                        override.cObject {
                            10 = TEXT
                            10 {
                                data = register:pageColPos
                                if.isTrue.data = register:pageColPos
                            }
                            20 = TEXT
                            20 {
                                value = default
                                if.value.data = register:pageColPos
                                if.equals = 0
                            }
                        }
                    }
                }
                20.10.setup {
                    columns-1-1 < lib.gridelements.defaultGridSetup
                    columns-1-1 {
                        stdWrap {
                            innerWrap = <div class="columns width-1-1 {field:flexform_gridpadding} verticalalign--{field:flexform_verticalalign}">|</div>
                            innerWrap.insertData = 1
                        }
                        columns {
                            1 < .default
                            1 {
                                stdWrap {
                                    outerWrap.cObject = TEXT
                                    outerWrap.cObject {
                                        value = first
                                        noTrimWrap = *<div class="col *">|</div>*
                                        noTrimWrap.splitChar = *
                                    }
                                }
                                renderObj {
                                    # Faktor der aktuellen Spalte setzen
                                    10 < temp.gridColumnWidthCalculation
                                    10.gridColumnFactor.cObject.setCurrent = 0.5

                                    # Debugging-Ausgabe
                                    15 < temp.gridColumnsDebugging

                                    30 = RESTORE_REGISTER
                                }
                            }
                            2 < .1
                            2.stdWrap.outerWrap.cObject.value = second
                        }
                    }
                    columns-1-1-1 < .columns-1-1
                    columns-1-1-1 {
                        stdWrap {
                            innerWrap = <div class="columns width-1-1-1 {field:flexform_gridpadding} verticalalign--{field:flexform_verticalalign}">|</div>
                        }
                        columns {
                            1.renderObj.10.gridColumnFactor.cObject.setCurrent = 0.333
                            2 < .1
                            2.stdWrap.outerWrap.cObject.value = second
                            3 < .1
                            3.stdWrap.outerWrap.cObject.value = third
                        }
                    }
                    columns-1-1-1-1 < .columns-1-1
                    columns-1-1-1-1 {
                        stdWrap {
                            innerWrap = <div class="columns width-1-1-1-1 {field:flexform_gridpadding} verticalalign--{field:flexform_verticalalign}">|</div>
                        }
                        columns {
                            1.renderObj.10.gridColumnFactor.cObject.setCurrent = 0.25
                            2 < .1
                            2.stdWrap.outerWrap.cObject.value = second
                            3 < .1
                            3.stdWrap.outerWrap.cObject.value = third
                            4 < .1
                            4.stdWrap.outerWrap.cObject.value = fourth
                        }
                    }
                    columns-1-2 < .columns-1-1
                    columns-1-2 {
                        stdWrap {
                            innerWrap = <div class="columns width-1-2 {field:flexform_gridpadding} verticalalign--{field:flexform_verticalalign}">|</div>
                        }
                        columns {
                            1.renderObj.10.gridColumnFactor.cObject.setCurrent = 0.3333
                            2 < .1
                            2.stdWrap.outerWrap.cObject.value = second
                            2.renderObj.10.gridColumnFactor.cObject.setCurrent = 0.6666
                        }
                    }
                    columns-2-1 < .columns-1-1
                    columns-2-1 {
                        stdWrap {
                            innerWrap = <div class="columns width-2-1 {field:flexform_gridpadding} verticalalign--{field:flexform_verticalalign}">|</div>
                        }
                        columns {
                            1.renderObj.10.gridColumnFactor.cObject.setCurrent = 0.6666
                            2 < .1
                            2.stdWrap.outerWrap.cObject.value = second
                            2.renderObj.10.gridColumnFactor.cObject.setCurrent = 0.3333
                        }
                    }
                    defaultaccordion < lib.gridelements.defaultGridSetup
                    defaultaccordion {
                        stdWrap {
                            innerWrap.cObject = COA
                            innerWrap.cObject {
                                10 = COA
                                10 {
                                    stdWrap.noTrimWrap = |<div class="abavo_accordion" role="tablist" aria-multiselectable="true" |>|
                                    10 = TEXT
                                    10 {
                                        value = false
                                        override = true
                                        override.if.isTrue.data = field:flexform_showfirstentry
                                        wrap = data-showfirstentry="|"
                                    }
                                    20 = TEXT
                                    20 {
                                        data = field:flexform_slideduration
                                        override = 300
                                        override.if.isFalse.data = field:flexform_slideduration
                                        stdWrap {
                                            intval = 1
                                            noTrimWrap = | data-slideduration="|" |
                                            required = 1
                                        }
                                    }
                                }
                                20 = TEXT
                                20 {
                                    data = LLL:{$config.l10nfile}:accordion.show_close_all
                                    stdWrap {
                                        if.isTrue.data = field:flexform_showtogglealldiv
                                        outerWrap = <div class="show_close_all_wrap"><a class="show_close_all" href="#">|</a></div>
                                    }
                                }
                                30 = TEXT
                                30.value = |</div>
                            }
                        }
                        columns {
                            0 < .default
                            0 {
                                renderObj {
                                    stdWrap.outerWrap = <div class="accordion-element">|</div>
                                    10 = TEXT
                                    10 {
                                        field = header
                                        stdWrap {
                                            dataWrap = <div class="accordion-header" id="acc-header-c{field:uid}" tabindex="0" role="tab" aria-controls="acc-content-{field:uid}" aria-expanded="false">|</div>
                                        }
                                    }
                                    20.stdWrap.outerWrap = <div class="accordion-content" id="acc-content-{field:uid}" role="tabpanel" aria-labelledby="acc-header-c{field:uid}" aria-expanded="false"><div class="accordion-wrapper">|</div></div>
                                    20.stdWrap.outerWrap.insertData = 1
                                }
                            }
                        }
                    }
                    tabs < lib.gridelements.defaultGridSetup
                    tabs {
                        stdWrap {
                            required = 1
                            outerWrap = <div class="tabs">|</div>
                            preCObject = CONTENT
                            preCObject {
                                table = tt_content
                                select {
                                    selectFields = header, uid, tx_gridelements_container, sorting
                                    where = tx_gridelements_container={field:uid}
                                    where.insertData = 1
                                    orderBy = sorting
                                }
                                wrap = <ul class="tab-navigation" role="tablist">|</ul>
                                renderObj = TEXT
                                renderObj {
                                    field = header
                                    stdWrap {
                                        parseFunc.constants = 1
                                        insertData = 1
                                        wrap.cObject = CASE
                                        wrap.cObject {
                                            key.data = cObj:parentRecordNumber
                                            default = TEXT
                                            default.value = <a class="tab" href="#tab-{field:uid}" id="tab-{field:uid}-tab" aria-controls="tab-{field:uid}" role="tab">|</a>
                                            1 = TEXT
                                            1.value = <a class="tab" href="#tab-{field:uid}" id="tab-{field:uid}-tab" aria-controls="tab-{field:uid}" role="tab" aria-expanded="true">|</a>
                                        }
                                        outerWrap.cObject = CASE
                                        outerWrap.cObject {
                                            key.data = cObj:parentRecordNumber
                                            default = TEXT
                                            default.value = <li role="presentation">|</li>
                                            1 = TEXT
                                            1.value = <li role="presentation" class="active">|</li>
                                        }
                                    }
                                }
                            }
                        }
                        columns {
                            0 < .default
                            0 {
                                wrap = <div class="tab-content">|</div>
                                renderObj {
                                    stdWrap {
                                        preCObject = TEXT
                                        preCObject {
                                            field = header
                                            parseFunc.constants = 1
                                            dataWrap.cObject = CASE
                                            dataWrap.cObject {
                                                key.data = cObj:parentRecordNumber
                                                default = TEXT
                                                default.value = <a class="acc-tab" href="#tab-{field:uid}" aria-controls="tab-{field:uid}">|</a>
                                                1 = TEXT
                                                1.value = <a class="acc-tab active" href="#tab-{field:uid}" aria-controls="tab-{field:uid}">|</a>
                                            }
                                        }
                                        outerWrap.cObject = CASE
                                        outerWrap.cObject {
                                            key.data = cObj:parentRecordNumber
                                            default = TEXT
                                            default {
                                                value = <div role="tabpanel" class="tab-panel box box--bg-grey-light" id="tab-{field:uid}" aria-labelledby="tab-{field:uid}-tab">|</div>
                                                insertData = 1
                                            }
                                            1 < .default
                                            1 {
                                                value = <div role="tabpanel" class="tab-panel box box--bg-grey-light active" id="tab-{field:uid}" aria-labelledby="tab-{field:uid}-tab">|</div>
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    accordion < lib.gridelements.defaultGridSetup
                    accordion {
                        stdWrap {
                            outerWrap = <div class="teaseraccordion">|</div>
                            preCObject = USER
                            preCObject {
                                stdWrap {
                                    outerWrap = <div class="teaseraccordion__teasermenu">|</div>
                                    innerWrap = <div class="teaseraccordion__teasermenu__wrapper">|</div>
                                }
                                userFunc = GridElementsTeam\Gridelements\Plugin\Gridelements->main
                                setup {
                                    default {
                                        columns {
                                            default {
                                                renderObj = TEXT
                                                renderObj {
                                                    field = header
                                                    stdWrap {
                                                        innerWrap = <div class="entry__wrapper">|</div>
                                                        dataWrap {
                                                            cObject = FILES
                                                            cObject {
                                                                stdWrap {
                                                                    wrap = <div class="teaseraccordion__teasermenu__entry lazyload" data-slide="{cObj:parentRecordNumber}" data-bgset="*" data-sizes="auto">|</div>
                                                                    wrap {
                                                                        splitChar = *
                                                                        override = <div class="teaseraccordion__teasermenu__entry current lazyload" data-slide="{cObj:parentRecordNumber}" data-bgset="*" data-sizes="auto">|</div>
                                                                        override {
                                                                            if {
                                                                                value.data = cObj:parentRecordNumber
                                                                                equals = 1
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                maxItems = 1
                                                                references {
                                                                    table = tt_content
                                                                    fieldName = tx_sitepackage_bgimage
                                                                    uid.field = uid
                                                                }
                                                                renderObj = IMAGE
                                                                renderObj {
                                                                    file {
                                                                        import.data = file:current:uid
                                                                        treatIdAsReference = 1
                                                                        maxW = 500
                                                                    }
                                                                    layoutKey = srcset-lazyload-bg
                                                                    layout {
                                                                        srcset-lazyload-bg < temp.respimage-layout.srcset-lazyload-bg
                                                                    }
                                                                    sourceCollection {
                                                                        250 {
                                                                            maxW = 250
                                                                            srcappend = ,
                                                                        }
                                                                        500 {
                                                                            maxW = 500
                                                                            srcappend = ,
                                                                        }
                                                                        100 {
                                                                            pixelDensity = 2
                                                                            quality = 70
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            innerWrap = <div class="teaseraccordion__accordion">|</div>
                        }
                        columns {
                            0 < .default
                            0 {
                                renderObj {
                                    stdWrap.outerWrap = <div class="teaseraccordion__accordion__entry">|</div>
                                    10 = TEXT
                                    10 {
                                        field = header
                                        stdWrap {
                                            dataWrap {
                                                cObject = FILES
                                                cObject {
                                                    stdWrap {
                                                        wrap = <div class="teaseraccordion__accordion__entry__header lazyload" data-bgset="*" data-sizes="auto"><div class="teaseraccordion__accordion__entry__header__innerwrap">|</div></div>
                                                        wrap.splitChar = *
                                                    }
                                                    maxItems = 1
                                                    references {
                                                        table = tt_content
                                                        fieldName = tx_sitepackage_bgimage
                                                        uid.field = uid
                                                    }
                                                    renderObj = IMAGE
                                                    renderObj {
                                                        file {
                                                            import.data = file:current:uid
                                                            treatIdAsReference = 1
                                                            maxW = 500
                                                        }
                                                        layoutKey = srcset-lazyload-bg
                                                        layout {
                                                            srcset-lazyload-bg < temp.respimage-layout.srcset-lazyload-bg
                                                        }
                                                        sourceCollection {
                                                            250 {
                                                                maxW = 250
                                                                srcappend = ,
                                                            }
                                                            500 {
                                                                maxW = 500
                                                                srcappend = ,
                                                            }
                                                            100 {
                                                                pixelDensity = 2
                                                                quality = 70
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    20.stdWrap.outerWrap = <div class="teaseraccordion__accordion__entry__content horizontal-content-padding">|</div>
                                    20.stdWrap.outerWrap.insertData = 1
                                }
                            }
                        }
                    }
                    wrapper < lib.gridelements.defaultGridSetup
                    wrapper {
                        stdWrap {
                            innerWrap = <div class="wrapper-element">|</div>
                        }
                    }
                }
                100 = RESTORE_REGISTER
            }
        }
    }
}
