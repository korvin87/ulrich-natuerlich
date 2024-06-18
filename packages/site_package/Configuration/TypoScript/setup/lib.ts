lib {
    parseFunc_RTE {
        externalBlocks.table.stdWrap.outerWrap = <div class="contenttable-responsive">|</div>
    }
    contentElement {
        variables {
            parentRecordNumber = TEXT
            parentRecordNumber.data = cObj:parentRecordNumber
        }
    }
    productCategoryMenu = HMENU
    productCategoryMenu {
        special = userfunction
        special.userFunc = Abavo\UlrichProducts\User\Category->getMenu

        1 = TMENU
        1 {
            NO = 1
            NO {
                allWrap = <li role="menuitem">|</li>
            }
            ACT < .NO
            ACT {
                allWrap = <li class="act" role="menuitem">|</li>
            }
        }
    }
    co_main = COA
    co_main {
        stdWrap {
            dataWrap = <div class="container container--main-white-content container--pos-relative">|</div>
            wrap = <div class="container__inner container__inner--dontremovemarginbottom">|</div>
            required = 1
        }
        10 = FILES
        10 {
            stdWrap {
                outerWrap = <div id="headerteaserslider" class="headerteaserslider fontcolor--white print-hide">|</div>
                wrap = <div class="slider">|</div>
                required = 1
            }
            references {
                table = pages
                data = levelmedia:-1
            }
            renderObj = COA
            renderObj {
                stdWrap {
                    ifEmpty = <div class="headerteaserslider__entry headerteaserslider__entry--nocontent"></div>
                }
                10 = COA
                10 {
                    stdWrap {
                        outerWrap = <div class="headerteaserslider__entry">|</div>
                        innerWrap = <div class="headerteaserslider__entry__wrapper horizontal-content-padding">|</div>
                        required = 1
                    }
                    10 = TEXT
                    10 {
                        data = file:current:alternative
                        stdWrap {
                            outerWrap = <div class="headerteaserslider__title">|</div>
                            if.isTrue.data = file:current:alternative
                        }
                    }
                    20 = COA
                    20 {
                        stdWrap {
                            wrap = <div class="headerteaserslider__infos">|</div>
                            required = 1
                        }
                        10 = TEXT
                        10 {
                            data = file:current:description
                            stdWrap {
                                required = 1
                                br = 1
                            }
                        }
                        20 = TEXT
                        20 {
                            data = file:current:title
                            stdWrap {
                                typolink {
                                    parameter.data = file:current:link
                                }
                                if.isTrue.data = file:current:title
                            }
                        }
                    }
                }
            }
        }
        20 < styles.content.get
        20 {
            stdWrap {
                outerWrap = <div class="frame frame-box frame--bgcolor-white frame-space-before-medium frame-space-after-medium">|</div>
                innerWrap = <div class="frame__innerwrap">|</div>
                required = 1
            }
            select.where = {#colPos}=1
        }
    }
    co_main_fullwidth < styles.content.get
    co_main_fullwidth {
        renderObj {
            stdWrap {
                dataWrap {
                    cObject = COA
                    cObject {
                        stdWrap {
                            noTrimWrap = *<div *>|</div>*
                            noTrimWrap.splitChar = *
                        }
                        10 = COA
                        10 {
                            stdWrap {
                                noTrimWrap = |class="|" |
                                insertData = 1
                            }
                            10 = TEXT
                            10 {
                                value (
                                    container container--padding-y
                                    container--ctype-{field:CType}
                                    container--bgcolor-{field:tx_sitepackage_bgcolor}
                                    fontcolor--{field:tx_sitepackage_fontcolor}
                                    fontsize--{field:tx_sitepackage_fontsize}
                                    textalign--{field:tx_sitepackage_textalign}
                                    horizontal-content-padding
                                )
                            }
                            20 = TEXT
                            20 {
                                value = container--withoutbgimage
                                stdWrap {
                                    noTrimWrap = | ||
                                    if.isFalse.field = tx_sitepackage_bgimage
                                }
                            }
                            30 = TEXT
                            30 {
                                value = container--withbgimage container--bgimagesize-{field:tx_sitepackage_bgimagesize} container--bgcoloroverlay-{field:tx_sitepackage_bgcoloroverimage} lazyload
                                stdWrap {
                                    noTrimWrap = | ||
                                    if.isTrue.field = tx_sitepackage_bgimage
                                }
                            }
                        }
                        20 =< lib.tt_content.bgimage
                        20 {
                            stdWrap.if.isTrue.field = tx_sitepackage_bgimage
                        }
                    }
                }
                innerWrap = <div class="container__inner">|</div>
            }
        }
    }
    co_product < styles.content.get
    co_product {
        select.where = {#colPos}=1
    }
    privacylink = TEXT
    privacylink {
        noTrimWrap = |<script type="text/javascript">var privacylink = |;</script>|
        value = null
        override {
            if.isPositive = {$config.pid_privacy}
            typolink {
                parameter = {$config.pid_privacy}
                returnLast = url
            }
            wrap = '|'
        }
    }
    page {
        langselect = HMENU
        langselect {
            stdWrap {
                outerWrap = <div class="mainnav-tooltip-content">|</div>
                innerWrap = <ul role="menu">|</ul>
                if.isTrue = {$config.active_languages}
            }
            special = language
            special.value = {$config.active_languages}
            special.normalWhenNoLanguage = 0

            1 = TMENU
            1 {
                itemArrayProcFunc = Abavo\SitePackage\User\MenuL10nHelper->hideHiddenExtensionRecords

                NO = 1
                NO {
                    linkWrap = <li role="menuitem">|</li>
                    stdWrap.override = {$config.active_languages_labels_optionsplit}
                    doNotLinkIt = 1
                    stdWrap {
                        typolink {
                            parameter.data = page:uid
                            additionalParams = {$config.active_languages_additionalParams_optionsplit}
                            addQueryString = 1
                            addQueryString.exclude = L,id,cHash,no_cache,tx_ulrichproducts_pi
                            addQueryString.method = GET
                            no_cache = 0
                        }
                    }
                }
                ACT < .NO
                ACT {
                    linkWrap = <li class="act">|</li>
                }
                USERDEF1 < .NO
                USERDEF1 {
                    linkWrap = <li class="na">|</li>
                    stdWrap.typolink.parameter.data = leveluid:0
                    stdWrap.typolink.addQueryString = 0
                }
            }
        }
        mainnav = HMENU
        mainnav {
            stdWrap {
                outerWrap = <nav id="mainnav" class="mainnav">|</nav>
                wrap = <ul role="menu">|</ul>
                preCObject = HMENU
                preCObject {
                    special = list
                    special.value.data = leveluid:0
                    1 = TMENU
                    1 {
                        NO = 1
                        NO {
                            allWrap = <li role="menuitem">|
                            wrapItemAndSub = |</li>
                            stdWrap {
                                override = <span class="fontello-icon-{field:author_email}"></span>
                                override {
                                    insertData = 1
                                    fieldRequired = author_email
                                }
                            }
                            ATagTitle.field = nav_title // title
                        }
                    }
                }
            }
            entryLevel = 0

            1 = TMENU
            1 {
                NO = 1
                NO {
                    allWrap = <li role="menuitem">|
                    wrapItemAndSub = |</li>
                    ATagParams = data-tooltip-content="#tooltip-content-mainnav-{field:uid}"
                    ATagParams {
                        insertData = 1
                    }
                    stdWrap2 {
                        postCObject = COA
                        postCObject {
                            stdWrap {
                                outerWrap = <div class="tooltip_templates">|</div>
                                dataWrap = <div id="tooltip-content-mainnav-{field:uid}" class="mainnav-tooltip-content">|</div>
                                innerWrap = <ul role="menu">|</ul>
                                required = 1
                            }
                            10 = HMENU
                            10 {
                                special = directory
                                special.value.field = uid

                                1 = TMENU
                                1 {
                                    NO = 1
                                    NO {
                                        allWrap = <li role="menuitem">|
                                        wrapItemAndSub = |</li>
                                    }
                                    ACT < .NO
                                    ACT {
                                        allWrap = <li class="act" role="menuitem">|
                                    }
                                }
                            }
                            20 =< lib.productCategoryMenu
                            20 {
                                stdWrap {
                                    if {
                                        value.field = uid
                                        equals = {$plugin.tx_ulrichproducts.settings.productPiPid}
                                    }
                                }
                            }
                        }
                    }
                }
                ACT < .NO
                ACT {
                    allWrap = <li class="act" role="menuitem">|
                }
            }
        }
        metamenu = HMENU
        metamenu {
            special = directory
            special.value = {$config.pid_metamenu}
            excludeUidList = {$plugin.tx_abavosearch.settings.targetPid}

            1 = TMENU
            1 {
                NO = 1
                NO {
                    allWrap = <li class="metamenu-item" role="menuitem">|
                    wrapItemAndSub = |</li>
                    stdWrap {
                        override = <span class="fontello-icon-{field:author_email}"></span>
                        override {
                            insertData = 1
                            fieldRequired = author_email
                        }
                    }
                    ATagTitle.field = nav_title // title
                }
                ACT < .NO
                ACT {
                    allWrap = <li class="metamenu-item act" role="menuitem">|
                }
            }
        }
        respmenu = COA
        respmenu {
            stdWrap {
                outerWrap = <nav id="respmenu" class="print-hide">|</nav>
                innerWrap = <ul role="menu">|</ul>
            }
            10 = HMENU
            10 {
                special = list
                special.value.data = leveluid:0

                1 = TMENU
                1 {
                    NO = 1
                    NO {
                        allWrap = <li class="mainnav-item" role="menuitem">|</li>
                        doNotLinkIt = 0
                        doNotLinkIt {
                            override = 1
                            override.if {
                                value.field = doktype
                                equals = 4
                                isTrue.cObject = TEXT
                                isTrue.cObject {
                                    value = true
                                    if {
                                        value = 1,2
                                        isInList.field = shortcut_mode
                                        isFalse.field = shortcut
                                    }
                                }
                            }
                        }
                        stdWrap {
                            innerWrap = <span class="fontello-icon-{field:author_email}"></span>|
                            innerWrap {
                                insertData = 1
                                fieldRequired = author_email
                            }
                            outerWrap = <span>|</span>
                            outerWrap.if {
                                value.field = doktype
                                equals = 4
                                isTrue.cObject = TEXT
                                isTrue.cObject {
                                    value = true
                                    if {
                                        value = 1,2
                                        isInList.field = shortcut_mode
                                        isFalse.field = shortcut
                                    }
                                }
                            }
                        }
                    }
                    CUR < .NO
                    CUR {
                        allWrap = <li class="mainnav-item Selected" role="menuitem">|</li>
                    }
                }
            }
            20 < .10
            20 {
                special >
                entryLevel = 0

                1 {
                    NO {
                        stdWrap2 {
                            postCObject = COA
                            postCObject {
                                stdWrap {
                                    wrap = <ul role="menu">|</ul>
                                    required = 1
                                }
                                10 < lib.page.respmenu.10
                                10 {
                                    special >
                                    special = directory
                                    special.value.field = uid

                                    1 {
                                        ACT {
                                            allWrap = <li class="mainnav-item Selected" role="menuitem">|</li>
                                        }
                                    }
                                }
                                20 = HMENU
                                20 {
                                    stdWrap {
                                        if {
                                            value.field = uid
                                            equals = {$plugin.tx_ulrichproducts.settings.productPiPid}
                                        }
                                    }
                                    special = userfunction
                                    special.userFunc = Abavo\UlrichProducts\User\Category->getMenu

                                    1 = TMENU
                                    1 {
                                        NO = 1
                                        NO {
                                            allWrap = <li class="mainnav-item" role="menuitem">|</li>
                                        }
                                        ACT < .NO
                                        ACT {
                                            allWrap = <li class="mainnav-item Selected" role="menuitem">|</li>
                                        }
                                    }
                                }
                            }
                        }
                    }
                    CUR >

                    ACT < .NO
                    ACT {
                        allWrap = <li class="mainnav-item Selected" role="menuitem">|</li>
                    }
                }
            }
            30 = TEXT
            30 {
                value = <li class="Divider"></li>
            }
            40 < .20
            40 {
                special >
                special = directory
                special.value = {$config.pid_metamenu}
            }
            50 = TEXT
            50 {
                data = LLL:{$config.l10nfile}:language
                stdWrap {
                    if.isTrue = {$config.active_languages}
                    innerWrap = <span><span class="fontello-icon-earth"></span>|</span>
                    outerWrap = <li role="menuitem" class="langselect">|</li>
                    postCObject = HMENU
                    postCObject {
                        stdWrap {
                            innerWrap = <ul role="menu">|</ul>
                        }
                        special = language
                        special.value = {$config.active_languages}
                        special.normalWhenNoLanguage = 0

                        1 = TMENU
                        1 {
                            itemArrayProcFunc = Abavo\SitePackage\User\MenuL10nHelper->hideHiddenExtensionRecords

                            NO = 1
                            NO {
                                linkWrap = <li role="menuitem">|</li>
                                stdWrap.override = {$config.active_languages_labels_optionsplit}
                                doNotLinkIt = 1
                                stdWrap {
                                    typolink {
                                        parameter.data = page:uid
                                        additionalParams = {$config.active_languages_additionalParams_optionsplit}
                                        addQueryString = 1
                                        addQueryString.exclude = L,id,cHash,no_cache
                                        addQueryString.method = GET
                                        no_cache = 0
                                    }
                                }
                            }
                            ACT < .NO

                            USERDEF1 < .NO
                            USERDEF1 {
                                stdWrap.typolink.parameter.data = leveluid:0
                                stdWrap.typolink.addQueryString = 0
                            }
                        }
                    }
                }
            }
            60 < .30

            70 < .40
            70 {
                special.value = {$config.pid_footer}

                1 {
                    NO {
                        allWrap = <li class="footernav-item" role="menuitem">|
                    }
                    ACT {
                        allWrap = <li class="footernav-item Selected" role="menuitem">|
                    }
                }
                2 < .1
                2.wrap = <ul role="menu">|</ul>

                3 < .2
            }
        }
        headerslider = COA
        headerslider {
            10 = FILES
            10 {
                stdWrap {
                    wrap = <div id="headerslider" class="headerslider print-hide"><div class="slider">|</div></div>
                    required = 1
                    trim = 1
                }
                references {
                    table = pages
                    data = levelmedia:-1
                }
                renderObj = COA
                renderObj {
                    stdWrap {
                        outerWrap.cObject = IMAGE
                        outerWrap.cObject {
                            stdWrap {
                                wrap = <div class="entry" data-bgset="*" data-sizes="auto">|</div>
                                wrap.splitChar = *
                            }
                            file {
                                import.data = file:current:uid
                                treatIdAsReference = 1
                                maxW = 1920
                            }
                            layoutKey = srcset-lazyload-bg
                            layout {
                                srcset-lazyload-bg < temp.respimage-layout.srcset-lazyload-bg
                            }
                            sourceCollection {
                                400 {
                                    maxW = 400
                                    srcappend = ,
                                }
                                600 {
                                    maxW = 600
                                    srcappend = ,
                                }
                                800 {
                                    maxW = 800
                                    srcappend = ,
                                }
                                1200 {
                                    maxW = 1200
                                    srcappend = ,
                                }
                                1600 {
                                    maxW = 1600
                                    srcappend = ,
                                }
                                1920 {
                                    srcappend = ,
                                }
                                2400 {
                                    maxW = 2400
                                    srcappend = ,
                                }
                                3840 {
                                    pixelDensity = 2
                                    quality = 70
                                }
                            }
                        }
                    }
                    10 = IMAGE
                    10 {
                        file = EXT:site_package/Resources/Public/Icons/pixel.png
                        file {
                            width = {$config.container_width}c
                            height = {$config.headerslider_defaultheight}
                            height {
                                override {
                                    data = page:tx_sitepackage_headerslider_height
                                    if.isTrue.data = page:tx_sitepackage_headerslider_height
                                }
                                wrap = |c
                            }
                        }
                        altText.data = file:current:alternative // page:title
                    }
                    20 = TEXT
                    20 {
                        value = <div class="slideloader"><span class="fontello-icon-spin6 animate-spin"></span></div>
                    }
                }
            }
            20 =< lib.tx_abavomaps_map_current_records
            20 {
                stdWrap {
                    if {
                        isTrue.data = page:tx_sitepackage_headerslider_mapsmarker
                    }
                }
                view {
                    templateRootPaths {
                        2 = EXT:site_package/Resources/Private/Vendor/AbavoMapsHeaderslider/Templates/
                    }
                    partialRootPaths {
                        2 = EXT:site_package/Resources/Private/Vendor/AbavoMapsHeaderslider/Partials/
                    }
                    layoutRootPaths {
                        2 = EXT:site_package/Resources/Private/Vendor/AbavoMapsHeaderslider/Layouts/
                    }
                }
                settings {
                    renderType = Standalone
                    routing = 0
                    arguments {
                        repo = MarkerRepository
                        useStdWrap = uids,Map
                        Map.stdWrap.cObject = COA
                        Map.stdWrap.cObject {
                            stdWrap {
                                wrap = {|,"zoom": "10", "zoomcontrol": "0", "pancontrol": "0", "scalecontrol": "0"}
                            }
                            10 = TEXT
                            10 {
                                value = {$config.container_width}
                                wrap = "width": "|",
                            }
                            20 = TEXT
                            20 {
                                value = {$config.headerslider_defaultheight}
                                override {
                                    data = page:tx_sitepackage_headerslider_height
                                    if.isTrue.data = page:tx_sitepackage_headerslider_height
                                }
                                wrap = "height": "|"
                            }
                        }
                        uids.stdWrap.cObject = TEXT
                        uids.stdWrap.cObject {
                            data = page:tx_sitepackage_headerslider_mapsmarker
                        }
                    }
                }
            }
        }
        title = TEXT
        title {
            data = page:title
            stdWrap {
                dataWrap = <h1 class="h-style h-style-{page:layout} textalign--{page:tx_sitepackage_headlinealign}">|</h1>
                preCObject = TEXT
                preCObject {
                    data = page:subtitle
                    stdWrap {
                        if.isTrue.data = page:subtitle
                        wrap = <span class="subtitle">|</span>
                    }
                }
                if {
                    value.data = page:layout
                    equals = 100
                    negate = 1
                }
            }
        }
        breadcrumb = COA
        breadcrumb {
            stdWrap {
                outerWrap = <nav id="breadcrumb" class="breadcrumb print-hide">|</nav><hr>
                preCObject = TEXT
                preCObject {
                    data = LLL:{$config.l10nfile}:breadcrumb.label
                    stdWrap {
                        wrap = <div class="breadcrumb__label">|</div>
                    }
                }
                innerWrap = <ol itemscope itemtype="http://schema.org/BreadcrumbList">|</ol>
                required = 1
            }
            5 = LOAD_REGISTER
            5 {
                breadcrumbcounter {
                    current = 0
                    setCurrent {
                        data = register:counter
                        wrap = |+1
                    }
                    prioriCalc = intval
                }
            }
            10 = HMENU
            10 {
                special = rootline
                special.range = 0|-1
                includeNotInMenu = 1
                excludeUidList = {$config.pid_footer},{$config.pid_metamenu}

                1 = TMENU
                1 {
                    NO = 1
                    NO {
                        allWrap = <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">|<meta itemprop="position" content="{register:breadcrumbcounter}" /></li>
                        allWrap.insertData = 1
                        linkWrap = <span itemprop="name">|</span>
                        stdWrap {
                            parseFunc.constants = 1
                            noTrimWrap = |&raquo; ||
                            postCObject = LOAD_REGISTER
                            postCObject {
                                breadcrumbcounter {
                                    current = 1
                                    setCurrent {
                                        data = register:breadcrumbcounter
                                        wrap = |+1
                                    }
                                    prioriCalc = intval
                                }
                            }
                        }
                    }
                    CUR < .NO
                    CUR {
                        linkWrap >
                    }
                }
            }
            20 = RECORDS
            20 {
                stdWrap {
                    if.isPositive.data = GP:tx_ulrichproducts_pi|category
                    outerWrap = <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">|<meta itemprop="position" content="{register:breadcrumbcounter}" /></li>
                    outerWrap.insertData = 1
                    typolink {
                        parameter.data = page:uid
                        additionalParams {
                            data = GP:tx_ulrichproducts_pi|category
                            intval = 1
                            wrap = &tx_ulrichproducts_pi[category]=|
                        }
                    }
                    postCObject = LOAD_REGISTER
                    postCObject {
                        breadcrumbcounter {
                            current = 1
                            setCurrent {
                                data = register:breadcrumbcounter
                                wrap = |+1
                            }
                            prioriCalc = intval
                        }
                    }
                }
                tables = sys_category
                source {
                    data = GP:tx_ulrichproducts_pi|category
                    intval = 1
                }
                conf.sys_category = TEXT
                conf.sys_category {
                    field = title
                    stripHtml = 1
                    crop = 50 | … | 1
                    htmlSpecialChars = 1
                    noTrimWrap = |&raquo; ||
                }
            }
            25 = LOAD_REGISTER
            25 {
                breadcrumbcounter {
                    current = 1
                    setCurrent {
                        data = register:breadcrumbcounter
                        wrap = |+1
                    }
                    prioriCalc = intval
                }
            }
            30 = RECORDS
            30 {
                stdWrap {
                    if {
                        isPositive.data = GP:tx_ulrichproducts_pi|product
                        value.data = GP:tx_ulrichproducts_pi|action
                        equals = show
                    }
                    outerWrap = <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">|<meta itemprop="position" content="{register:breadcrumbcounter}" /></li>
                    outerWrap.insertData = 1
                    typolink {
                        parameter.data = page:uid
                        addQueryString = 1
                    }
                }
                tables = tx_ulrichproducts_domain_model_product
                source {
                    data = GP:tx_ulrichproducts_pi|product
                    intval = 1
                }
                conf.tx_ulrichproducts_domain_model_product = TEXT
                conf.tx_ulrichproducts_domain_model_product {
                    field = title
                    stripHtml = 1
                    crop = 50 | … | 1
                    htmlSpecialChars = 1
                    noTrimWrap = |&raquo; ||
                }
            }
            9999 = RESTORE_REGISTER
        }
        footer {
            sitemap = HMENU
            sitemap {
                entryLevel = 0

                1 = TMENU
                1 {
                    wrap = <ul role="menu">|</ul>

                    NO = 1
                    NO {
                        allWrap = <li role="menuitem">|
                        wrapItemAndSub = |</li>
                        stdWrap2 {
                            postCObject = COA
                            postCObject {
                                wrap = <ul role="menu">|</ul>

                                10 = HMENU
                                10 {
                                    special = directory
                                    special.value.field = uid

                                    1 = TMENU
                                    1 {
                                        NO = 1
                                        NO {
                                            allWrap = <li role="menuitem">|
                                            wrapItemAndSub = |</li>
                                        }
                                        ACT < .NO
                                        ACT {
                                            allWrap = <li class="act" role="menuitem">|
                                        }
                                    }
                                }
                                20 =< lib.productCategoryMenu
                                20 {
                                    stdWrap {
                                        if {
                                            value.field = uid
                                            equals = {$plugin.tx_ulrichproducts.settings.productPiPid}
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ACT < .NO
                    ACT {
                        allWrap = <li class="act" role="menuitem">|
                    }
                }
            }
            contactbar = COA
            contactbar {
                10 = HMENU
                10 {
                    special = list
                    special.value = {$config.pid_contact}

                    1 = TMENU
                    1 {
                        NO = 1
                        NO {
                            linkWrap = <div class="contactbar__col contactbar__col--contactbutton">|</div>
                            ATagParams = class="button button--primary button--big"
                        }
                    }
                }
                20 < styles.content.get
                20 {
                    stdWrap {
                        outerWrap = <div class="contactbar__col contactbar__col--contactlinks">|</div>
                        required = 1
                    }
                    select {
                        pidInList = {$config.pid_generalcontent}
                        uidInList = {$config.uid_phone},{$config.uid_mail}
                    }
                    renderObj = TEXT
                    renderObj {
                        field = bodytext
                        parseFunc < lib.parseFunc_RTE
                    }
                }
                #                30 = COA
                #                30 {
                #                    stdWrap {
                #                        outerWrap = <div class="contactbar__col contactbar__col--socialmedia">|</div>
                #                    }
                #
                #                    10 = TEXT
                #                    10 {
                #                        value = <span class="fontello-icon-facebook-squared"></span>
                #                        stdWrap {
                #                            typolink.parameter = https://de-de.facebook.com/UlrichnatuerlichEresing/
                #                        }
                #                    }
                #
                #                    20 = TEXT
                #                    20 {
                #                        value = <span class="fontello-icon-xing-squared"></span>
                #                        stdWrap {
                #                            typolink.parameter =
                #                        }
                #                    }
                #                }
            }
            menu = HMENU
            menu {
                special = directory
                special.value = {$config.pid_footer}

                1 = TMENU
                1 {
                    wrap = <ul role="menu">|</ul>

                    NO = 1
                    NO {
                        allWrap = <li role="menuitem">|
                        wrapItemAndSub = |</li>
                    }
                    ACT < .NO
                    ACT {
                        allWrap = <li class="act" role="menuitem">|
                    }
                }
            }
        }
    }
    getlinkhref = TEXT
    getlinkhref {
        typolink {
            parameter.current = 1
            returnLast = url
        }
    }
    getlinktarget = TEXT
    getlinktarget {
        current = 1
        setCurrent.typolink {
            parameter.current = 1
            returnLast = target
        }
        ifEmpty = _top
    }
    uploads {
        image = IMAGE
        image {
            file {
                import.field = image
                maxW = 80
            }
            altText {
                data = field:alternative
                stripHtml = 1
                htmlSpecialChars = 1
            }
            layoutKey = srcset-lazyload
            layout.srcset-lazyload < temp.respimage-layout.srcset-lazyload
            sourceCollection {
                normal {
                    srcappend = ,
                }
                hdpi {
                    pixelDensity = 2
                    quality = 70
                }
            }
        }
    }
    subheader_headerstyle = TEXT
    subheader_headerstyle {
        current = 1
        setCurrent {
            field = headerstyle
            override = 10
            override.if.isFalse.field = headerstyle
            wrap = | + 10
        }
        prioriCalc = intval
    }
    addPageCacheTags = TEXT
    addPageCacheTags.stdWrap.addPageCacheTags.current = 1

    mediarendering = FILES
    mediarendering {
        stdWrap {
            outerWrap = <div class="content-images">|</div>
            required = 1
        }
        maxItems {
            field = maxItems
            ifEmpty = 9999
        }
        begin {
            field = begin
            ifEmpty = 0
        }
        references {
            table.field = table
            uid {
                field = uid
                intval = 1
            }
            fieldName.field = fieldName
        }
        renderObj = COA
        renderObj {
            10 = LOAD_REGISTER
            10 {
                originalImageWidth.cObject < temp.getFalOriginalImageWidth
                originalImageHeight.cObject < temp.getFalOriginalImageHeight
                currentImageWidth {
                    field = width
                    intval = 1
                    ifEmpty.cObject < temp.columnSpace
                }
                currentImageHeight.cObject < temp.getFalCurrentImageHeight
                currentImageHeightPercent.cObject < temp.getFalCurrentImageHeightPercent
            }
            20 < temp.renderFalMedia
            20 {
                stdWrap {
                    outerWrap = <div class="content-image">|</div>
                    dataWrap = <figure class="content-image-wrap" style="width: {register:currentImageWidth}px;">|</figure>
                }
            }
            30 = RESTORE_REGISTER
        }
    }
    robots = COA
    robots {
        10 = TEXT
        10 {
            value (
User-agent: *
Allow: /
Disallow: /typo3/
Disallow: /*?id=*
Disallow: /*lightbox=1
            )
        }
        11 = TEXT
        11.value.char = 10

        20 = TEXT
        20 {
            noTrimWrap = |Sitemap: ||
            typolink {
                parameter = {$config.pid_root}
                additionalParams = &type=655&L=
                forceAbsoluteUrl = 1
                returnLast = url
            }
        }
    }
    tt_content {
        bgimage = FILES
        bgimage {
            stdWrap {
                if {
                    value.field = parentgrid_tx_gridelements_backend_layout
                    equals = accordion
                    negate = 1
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
                stdWrap {
                    wrap = data-bgset="|" data-sizes="auto"
                }
                file {
                    import.data = file:current:uid
                    treatIdAsReference = 1
                    maxW = {$config.container_width}
                }
                layoutKey = srcset-lazyload-bg
                layout {
                    srcset-lazyload-bg < temp.respimage-layout.srcset-lazyload-bg
                }
                sourceCollection {
                    400 {
                        maxW = 400
                        srcappend = ,
                    }
                    600 {
                        maxW = 600
                        srcappend = ,
                    }
                    800 {
                        maxW = 800
                        srcappend = ,
                    }
                    1100 {
                        maxW = 1100
                        srcappend = ,
                    }
                    1440 {
                        srcappend = ,
                    }
                    2200 {
                        maxW = 2200
                        srcappend = ,
                    }
                    2880 {
                        pixelDensity = 2
                        quality = 70
                    }
                }
            }
        }
    }
    imagegallery {
        mainimages = FILES
        mainimages {
            references {
                table.field = table
                uid {
                    field = uid
                    intval = 1
                }
                fieldName.field = fieldName
            }
            renderObj = COA
            renderObj {
                stdWrap {
                    outerWrap = <div class="img-wrap">|</div>
                    typolink {
                        parameter {
                            data = file:current:link
                            override {
                                cObject = IMG_RESOURCE
                                cObject {
                                    file {
                                        import.data = file:current:uid
                                        treatIdAsReference = 1
                                        maxW = {$styles.content.textmedia.linkWrap.width}
                                    }
                                }
                                if {
                                    isTrue.field = image_zoom
                                    isFalse.data = file:current:link
                                }
                            }
                        }
                        ATagParams = class="{$styles.content.textmedia.linkWrap.lightboxCssClass}"
                        ATagParams {
                            if {
                                isTrue.field = image_zoom
                                isFalse.data = file:current:link
                            }
                        }
                        title {
                            data = file:current:title
                            stripHtml = 1
                        }
                    }
                }
                10 = IMAGE
                10 {
                    file {
                        import.data = file:current:uid
                        treatIdAsReference = 1
                        maxW.field = imagewidth
                        maxH.field = imageheight
                    }
                    altText {
                        data = file:current:alternative // field:alternative // file:current:title // field:header
                        stripHtml = 1
                        htmlSpecialChars = 1
                    }
                    layoutKey = srcset-lazyload
                    layout {
                        srcset-lazyload < temp.respimage-layout.srcset-lazyload
                    }
                    sourceCollection {
                        normal {
                            srcappend = ,
                        }
                        hdpi {
                            pixelDensity = 2
                            quality = 70
                        }
                    }
                }
            }
        }
    }
    imageslider {
        image = FILES
        image {
            stdWrap {
                ifEmpty.cObject = IMAGE
                ifEmpty.cObject {
                    file = EXT:site_package/Resources/Public/Icons/pixel.png
                    file {
                        width = 232c
                        height = 152c
                    }
                }
            }
            maxItems = 1
            references {
                table.field = table
                uid {
                    field = uid
                    intval = 1
                }
                fieldName.field = fieldName
            }
            renderObj = IMAGE
            renderObj {
                file {
                    import.data = file:current:uid
                    treatIdAsReference = 1
                    width = 232c
                    height = 152c
                }
                altText {
                    data = file:current:alternative // file:current:title // field:header
                    stripHtml = 1
                    htmlSpecialChars = 1
                }
                layoutKey = srcset-lazyload
                layout {
                    srcset-lazyload < temp.respimage-layout.srcset-lazyload
                }
                sourceCollection {
                    normal {
                        srcappend = ,
                    }
                    hdpi {
                        pixelDensity = 2
                        quality = 70
                    }
                }
            }
        }
    }
    contactperson {
        image = FILES
        image {
            stdWrap {
                ifEmpty.cObject = IMAGE
                ifEmpty.cObject {
                    file = EXT:site_package/Resources/Public/Icons/pixel.png
                    file {
                        width = 150c
                        height = 150c
                    }
                }
            }
            maxItems = 1
            references {
                table = tx_ulrichproducts_domain_model_contact
                uid {
                    field = uid
                    intval = 1
                }
                fieldName = media
            }
            renderObj = IMAGE
            renderObj {
                file {
                    import.data = file:current:uid
                    treatIdAsReference = 1
                    width = 150c
                    height = 150c
                }
                altText {
                    data = file:current:alternative // file:current:title // field:name
                    stripHtml = 1
                    htmlSpecialChars = 1
                }
                layoutKey = srcset-lazyload
                layout {
                    srcset-lazyload < temp.respimage-layout.srcset-lazyload
                }
                sourceCollection {
                    normal {
                        srcappend = ,
                    }
                    hdpi {
                        pixelDensity = 2
                        quality = 70
                    }
                }
            }
        }
    }
}
