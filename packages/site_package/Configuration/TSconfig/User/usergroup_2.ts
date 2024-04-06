options {
    clearCache {
        pages = 1
    }
    pageTree {
        showNavTitle = 1
        showDomainNameWithTitle = 1
    }
}

setup {
    default {
        thumbnailsByDefault = 1
    }
}

mod {
    web_info {
        menu {
            function {
                TYPO3\CMS\InfoPagetsconfig\Controller\InfoPageTyposcriptConfigController = 0
            }
        }
    }
    web_list {
        table {
            pages {
                hideTable = 1
            }
            pages_language_overlay < .pages
        }
    }
}

page {
    TCEFORM {
        pages_language_overlay < .pages
        tt_content {
            menu_type {
                removeItems = 4,7,6,categorized_pages,categorized_content
            }
        }
        tx_powermail_domain_model_field {
            type {
                removeItems = captcha,content,typoscript,reset
            }
        }
    }
}
