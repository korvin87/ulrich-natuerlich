# Englische Sprache
[siteLanguage("languageId") == "1"]
config {
    sys_language_uid = 1
    sys_language_isocode = en
    language = en
    locale_all = en_GB.UTF-8
}
[global]
# Französische Sprache
#[globalVar =  GP:L = 2]
#    config {
#        sys_language_uid = 2
#        sys_language_isocode = fr
#        language = fr
#        locale_all = fr_FR.UTF-8
#    }
#[global]

# Lightbox-Seiten
[request.getQueryParams()['lightbox'] == 1]
lightbox = PAGE
lightbox {
    config {
        disableAllHeaderCode = 1
    }
    typeNum = 0
    stdWrap {
        outerWrap = <div class="outerwrap">|</div>
        innerWrap = <div class="innerwrap">|</div>
        parseFunc.constants = 1
    }
    10 = TEXT
    10 {
        field = title
        stdWrap {
            outerWrap = <p class="h-style h-style-20">|</p>
            if {
                value.data = page:layout
                equals = 100
                negate = 1
            }
        }
    }
    20 < styles.content.get
    21 < styles.content.get
    21.select.where = {#colPos}=1

    30 = TEXT
    30 {
        value = <script src="{path:EXT:powermail/Resources/Public/JavaScripts/Powermail/Form.min.js}"></script>
        insertData = 1
    }
}
[global]
# Produktkategorie-Seiten
[traverse(request.getQueryParams(), 'tx_ulrichproducts_pi/category') > 0 || traverse(request.getParsedBody(), 'tx_ulrichproducts_pi/category') > 0 && traverse(request.getQueryParams(), 'tx_ulrichproducts_pi/product') < 1 || traverse(request.getParsedBody(), 'tx_ulrichproducts_pi/product') < 1]
lib {
    page {
        content {
            title {
                data >
                cObject = RECORDS
                cObject {
                    dontCheckPid = 1
                    tables = sys_category
                    source {
                        data = GP:tx_ulrichproducts_pi|category
                        intval = 1
                    }
                    conf.sys_category = TEXT
                    conf.sys_category {
                        field = title
                        htmlSpecialChars = 1
                    }
                }
            }
        }
    }
}
[global]
# Produktdetail-Seiten
[traverse(request.getQueryParams(), 'tx_ulrichproducts_pi/product') > 0 || traverse(request.getParsedBody(), 'tx_ulrichproducts_pi/product') > 0 && traverse(request.getQueryParams(), 'tx_ulrichproducts_pi/action') == 'show' || traverse(request.getParsedBody(), 'tx_ulrichproducts_pi/action') == 'show']
page {
    10 {
        pagets__main {
            file = ProductDetail.html
        }
    }
}
lib {
    page {
        content {
            title >
        }
    }
}
[global]
