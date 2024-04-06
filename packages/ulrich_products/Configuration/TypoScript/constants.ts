
plugin.tx_ulrichproducts {
    view {
        # cat=plugin.tx_ulrichproducts_pi/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:ulrich_products/Resources/Private/Templates/
        # cat=plugin.tx_ulrichproducts_pi/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:ulrich_products/Resources/Private/Partials/
        # cat=plugin.tx_ulrichproducts_pi/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:ulrich_products/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_ulrichproducts_pi//a; type=string; label=Default storage PID
        storagePid = 15
    }
    settings {
        # cat=plugin.tx_ulrichproducts_pi//b; type=int; label=Product PlugIn Page
        productPiPid = 14
        # cat=plugin.tx_ulrichproducts_pi//c; type=int; label=Product Inquire Page
        productInquirePid = 21
        # cat=plugin.tx_ulrichproducts_pi//d; type=int; label=Product Pattern Inquire Page
        productPatternInquirePid = 22
        # cat=plugin.tx_ulrichproducts_pi//c; type=int; label=Product Documents Page
        productDocumentInquiryPid = 42
        pageTypeApi {
            # cat=plugin.tx_ulrichproducts_pi//x; type=int; label=API Page type for products
            products = 2505
            # cat=plugin.tx_ulrichproducts_pi//y; type=int; label=API Page type for media
            media = 2805
        }
        accordionMediaWidth = 427
    }
}
