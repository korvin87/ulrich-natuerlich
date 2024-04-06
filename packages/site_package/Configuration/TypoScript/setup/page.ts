page = PAGE
page {
    typeNum = 0
    shortcutIcon = EXT:site_package/Resources/Public/Icons/Favicon/favicon_{$config.areaname}.ico

    meta {
        viewport = width=device-width, initial-scale=1
    }
    headerData {
        136 =< lib.privacylink
        230 = TEXT
        230 {
            value = {$config.areatitle}
            stdWrap {
                encodeForJavaScriptValue = 1
                outerWrap = <script type="text/javascript">var areatitle = |;</script>
            }
        }
        # tx_csseo
        654 {
            5 {
                htmlSpecialChars = 0
                stripHtml = 1
                stdWrap.parseFunc.constants = 1
            }
            30 {
                10 {
                    htmlSpecialChars = 0
                    stripHtml = 1
                    parseFunc.constants = 1
                }
                40 {
                    data >
                    value = {$config.areatitle}
                }
            }
        }
    }
    includeCSSLibs {
        normalize = EXT:site_package/Resources/Public/Css/vendor/normalize/normalize.css
        normalize.media = all
        normalize.forceOnTop = 1
        mmenu = EXT:site_package/Resources/Public/JavaScript/vendor/mmenu/dist/jquery.mmenu.css
        mmenu.media = all
        mmenu-positioning = EXT:site_package/Resources/Public/JavaScript/vendor/mmenu/dist/extensions/positioning/jquery.mmenu.positioning.css
        mmenu-positioning.media = all
        mmenu-pagedim = EXT:site_package/Resources/Public/JavaScript/vendor/mmenu/dist/extensions/pagedim/jquery.mmenu.pagedim.css
        mmenu-pagedim.media = all
        magnific-popup = EXT:site_package/Resources/Public/JavaScript/vendor/magnific-popup/dist/magnific-popup.css
        magnific-popup.media = all
        slick = EXT:site_package/Resources/Public/JavaScript/vendor/slick/slick/slick.css
        slick.media = all
        slick-theme = EXT:site_package/Resources/Public/JavaScript/vendor/slick/slick/slick-theme.css
        slick-theme.media = all
        fontello = EXT:site_package/Resources/Public/Fonts/fontello/css/fontello.css
        fontello.media = all
        fontello-animation = EXT:site_package/Resources/Public/Fonts/fontello/css/animation.css
        fontello-animation.media = all
        tooltipster = EXT:site_package/Resources/Public/JavaScript/vendor/tooltipster/dist/css/tooltipster.bundle.min.css
        tooltipster.media = all
    }
    includeCSS {
        all = EXT:site_package/Resources/Public/Css/all.css
        all.media = all
    }
    includeJSLibs {
        modernizr = EXT:site_package/Resources/Public/JavaScript/vendor/modernizr/modernizr.custom.min.js
        lazysizes-config = EXT:site_package/Resources/Public/JavaScript/lazysizes-config.js
        lazysizes-respimg = EXT:site_package/Resources/Public/JavaScript/vendor/lazysizes/plugins/respimg/ls.respimg.min.js
        lazysizes-parent-fit = EXT:site_package/Resources/Public/JavaScript/vendor/lazysizes/plugins/parent-fit/ls.parent-fit.min.js
        lazysizes-bgset = EXT:site_package/Resources/Public/JavaScript/vendor/lazysizes/plugins/bgset/ls.bgset.js
        lazysizes-print = EXT:site_package/Resources/Public/JavaScript/vendor/lazysizes/plugins/print/ls.print.min.js
        lazysizes = EXT:site_package/Resources/Public/JavaScript/vendor/lazysizes/lazysizes.min.js
        picturefill = EXT:site_package/Resources/Public/JavaScript/vendor/picturefill/dist/picturefill.min.js
    }
    includeJSFooterlibs {
        jquery = EXT:site_package/Resources/Public/JavaScript/vendor/jquery/jquery-3.3.1.js
        mmenu = EXT:site_package/Resources/Public/JavaScript/vendor/mmenu/dist/jquery.mmenu.js
        magnific-popup = EXT:site_package/Resources/Public/JavaScript/vendor/magnific-popup/dist/jquery.magnific-popup.min.js
        slick = EXT:site_package/Resources/Public/JavaScript/vendor/slick/slick/slick.min.js
        abavo_accordion = EXT:site_package/Resources/Public/JavaScript/jquery.abavo_accordion.js
        abavo_lbmessage = EXT:site_package/Resources/Public/JavaScript/jquery.abavo_lbmessage.js
        waypoints = EXT:site_package/Resources/Public/JavaScript/vendor/waypoints/lib/jquery.waypoints.min.js
        waypoints-sticky = EXT:site_package/Resources/Public/JavaScript/vendor/waypoints/lib/shortcuts/sticky.min.js
        tooltipster = EXT:site_package/Resources/Public/JavaScript/vendor/tooltipster/dist/js/tooltipster.bundle.min.js
        stickykit = EXT:site_package/Resources/Public/JavaScript/vendor/stickykit/jquery.sticky-kit.min.js
        #cookieconsent = EXT:site_package/Resources/Public/JavaScript/vendor/cookieconsent/build/cookieconsent.min.js
        #vimeoapi = https://player.vimeo.com/api/player.js
        #vimeoapi.external = 1
    }
    includeJSFooter {
        global = EXT:site_package/Resources/Public/JavaScript/global.js
        global.forceOnTop = 1
        tooltips = EXT:site_package/Resources/Public/JavaScript/tooltips.js
        abavo-search = EXT:abavo_search/Resources/Public/Js/Form.js
        abavo-search-autocomplete = EXT:abavo_search/Resources/Public/Js/Autocomplete_{$plugin.tx_abavosearch.settings.autocompleteLibrary}.js
        tabs = EXT:site_package/Resources/Public/JavaScript/tabs.js
        cookieconsent = EXT:site_package/Resources/Public/JavaScript/cookieconsent.js
        lightbox = EXT:site_package/Resources/Public/JavaScript/lightbox.js
        mmenu = EXT:site_package/Resources/Public/JavaScript/mmenu.js
        header = EXT:site_package/Resources/Public/JavaScript/header.js
        accordion = EXT:site_package/Resources/Public/JavaScript/accordion.js
        sword = EXT:site_package/Resources/Public/JavaScript/sword.js
        youtube = EXT:site_package/Resources/Public/JavaScript/youtube.js
        slider = EXT:site_package/Resources/Public/JavaScript/slider.js
        teaseraccordion = EXT:site_package/Resources/Public/JavaScript/teaseraccordion.js
        readmore = EXT:site_package/Resources/Public/JavaScript/readmore.js
        scrollto = EXT:site_package/Resources/Public/JavaScript/scrollto.js
    }
    inlineLanguageLabelFiles {
        fe = EXT:site_package/Resources/Private/Language/locallang.xlf
        fe {
            selectionPrefix = js
            stripFromSelectionName = js.
        }
        js = EXT:site_package/Resources/Private/Language/locallang_js.xlf
    }
    10 = CASE
    10 {
        key.data = pagelayout

        pagets__main = FLUIDTEMPLATE
        pagets__main {
            file = Main.html
            file.wrap = EXT:site_package/Resources/Private/Templates/Page/|

            templateRootPaths.0 = {$plugin.tx_sitepackage.view.templateRootPath}
            partialRootPaths.0 = {$plugin.tx_sitepackage.view.partialRootPath}
            layoutRootPaths.0 = {$plugin.tx_sitepackage.view.layoutRootPath}

            settings {
                pid_root = {$config.pid_root}
                page_class = template-main
                areaname = {$config.areaname}
                areatitle = {$config.areatitle}
                searchresultpage = {$plugin.tx_abavosearch.settings.targetPid}
                searchstoragepage = {$plugin.tx_abavosearch.persistence.storagePid}
            }
        }
    }
}
