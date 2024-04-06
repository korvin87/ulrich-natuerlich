temp {
    gridelementsSetup < tt_content.gridelements_pi1

    # Für den Inhalt zur Verfügung stehende Breite anhand Seitentemplate und der jeweiligen Inhaltsspalte setzen
    pageColumnWidth = CASE
    pageColumnWidth {
        key.data = pagelayout
        default = CASE
        default {
            key {
                field = colPos
                override.data = register:pageColPos
                override.if.isTrue.data = register:pageColPos
            }
            default = TEXT
            default.value = {$config.container_width}
            1 = TEXT
            1.value = 1068
        }
    }
    # Berechnung der Spaltenbreite von Gridelements
    gridColumnWidthCalculation = LOAD_REGISTER
    gridColumnWidthCalculation {
        # Faktor der aktuellen Spalte (wird direkt in tt_content der Gridelement-Definition gesetzt)
        gridColumnFactor.cObject = TEXT
        gridColumnFactor.cObject {
            current = 1
        }
        # Breite der Zwischenräume des Gridelements
        gridPaddingWidth.cObject = TEXT
        gridPaddingWidth.cObject {
            value = {$config.gridpadding}
            override {
                if.isTrue.field = parentgrid_flexform_gridpadding
                field = parentgrid_flexform_gridpadding
                split {
                    token = gridpadding-
                    cObjNum = 1
                    1.current = 1
                }
            }
        }
        # Breite des kompletten Gridelements
        gridWidth {
            cObject < temp.pageColumnWidth

            override {
                data = register:gridColumnWidth
                if.isTrue.data = register:gridColumnWidth
            }
        }
        # Breite der aktuellen Spalte des Gridelements
        gridColumnWidth.cObject = TEXT
        gridColumnWidth.cObject {
            current = 1
            setCurrent = {register:gridColumnFactor} * ( {register:gridWidth} + {register:gridPaddingWidth} ) - {register:gridPaddingWidth}
            setCurrent.insertData = 1
            prioriCalc = 1
            round.roundType = floor
        }
    }
    # Debugging-Ausgabe für Grid-Spalten
    gridColumnsDebugging = COA
    gridColumnsDebugging {
        stdWrap {
            wrap = <div style="border: 2px solid green; padding: 10px; overflow: hidden;">|</div>
            if.value = {$config.debug}
            if.equals = 1
            if.isTrue.data = TSFE:beUserLogin
        }
        10 = TEXT
        10 {
            noTrimWrap = |<div>Faktor: |</div>|
            data = register:gridColumnFactor
        }
        20 = TEXT
        20 {
            noTrimWrap = |<div>Spalten-Abstand: |</div>|
            data = register:gridPaddingWidth
        }
        30 = TEXT
        30 {
            noTrimWrap = |<div>Gesamt-Breite: |</div>|
            data = register:gridWidth
        }
        40 = TEXT
        40 {
            noTrimWrap = |<div>Spalten-Breite: |</div>|
            data = register:gridColumnWidth
        }
    }
    # Responsive Image Rendering Layouts
    respimage-layout {
        srcset-default {
            element = 				<img width="###WIDTH###" height="###HEIGHT###" src="###SRC###" srcset="###SOURCECOLLECTION###" sizes="(max-width: ###WIDTH###px) 100vw, ###WIDTH###px" ###PARAMS### ###ALTPARAMS### ###SELFCLOSINGTAGSLASH###>
            source = ###SRC### ###WIDTH###w###SRCAPPEND###
        }
        srcset-lazyload {
            element = <img class="lazyload" width="###WIDTH###" height="###HEIGHT###" data-sizes="(max-width: ###WIDTH###px) 100vw, ###WIDTH###px" data-srcset="###SOURCECOLLECTION###" ###PARAMS### ###ALTPARAMS### ###SELFCLOSINGTAGSLASH### />
            source = ###SRC### ###WIDTH###w###SRCAPPEND###
        }
        srcset-lazyload-bg {
            element = ###SOURCECOLLECTION###
            source = ###SRC### ###WIDTH###w ###HEIGHT###h###SRCAPPEND###
        }
    }
    getFalOriginalImageWidth = TEXT
    getFalOriginalImageWidth {
        data = file:current:width
        override {
            if.isTrue.data = file:current:crop
            current = 1
            setCurrent {
                data = file:current:crop
                listNum = 1
                listNum.splitChar = "width":
            }
            listNum = 0
            dataWrap = | * {file:current:width}
        }
        prioriCalc = intval
    }
    getFalOriginalImageHeight = TEXT
    getFalOriginalImageHeight {
        data = file:current:height
        override {
            if.isTrue.data = file:current:crop
            current = 1
            setCurrent {
                data = file:current:crop
                listNum = 1
                listNum.splitChar = "height":
            }
            listNum = 0
            dataWrap = | * {file:current:height}
        }
        prioriCalc = intval
    }
    getFalCurrentImageHeight = TEXT
    getFalCurrentImageHeight {
        current = 1
        setCurrent {
            data = register:originalImageWidth
            dataWrap = {register:currentImageWidth} / | * {register:originalImageHeight}
        }
        prioriCalc = intval
    }
    getFalCurrentImageHeightPercent = TEXT
    getFalCurrentImageHeightPercent {
        current = 1
        setCurrent {
            data = register:currentImageHeight
            dataWrap = | / {register:currentImageWidth} * 100
        }
        prioriCalc = 1
    }
    renderFalMedia = COA
    renderFalMedia {
        10 = CASE
        10 {
            stdWrap {
                outerWrap = <div class="img-wrap">|</div>
            }
            key.data = file:current:type

            # Bilder
            default = CASE
            default {
                key.data = file:current:extension
                stdWrap {
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
                default = IMAGE
                default {
                    file {
                        import.data = file:current:uid
                        treatIdAsReference = 1
                        maxW {
                            data = register:currentImageWidth
                            if.isFalse.data = register:fixedImageDimensions
                        }
                        width {
                            data = register:currentImageWidth
                            wrap = |c
                            if.isTrue.data = register:fixedImageDimensions
                        }
                        height {
                            data = register:currentImageHeight
                            wrap = |c
                            if.isTrue.data = register:fixedImageDimensions
                        }
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
                        small {
                            maxW {
                                current = 1
                                setCurrent.data = register:currentImageWidth
                                setCurrent.wrap = | / 2
                                prioriCalc = intval
                                if.isFalse.data = register:fixedImageDimensions
                            }
                            width {
                                current = 1
                                setCurrent.data = register:currentImageWidth
                                setCurrent.wrap = | / 2
                                prioriCalc = intval
                                wrap = |c
                                if.isTrue.data = register:fixedImageDimensions
                            }
                            height {
                                current = 1
                                setCurrent.data = register:currentImageHeight
                                setCurrent.wrap = | / 2
                                prioriCalc = intval
                                wrap = |c
                                if.isTrue.data = register:fixedImageDimensions
                            }
                            srcappend = ,
                        }
                        normal {
                            srcappend = ,
                        }
                        hdpi {
                            pixelDensity = 2
                            quality = 70
                        }
                    }
                }
                # SVG
                svg = IMAGE
                svg {
                    file {
                        import.data = file:current:uid
                        treatIdAsReference = 1
                        maxW {
                            data = register:currentImageWidth
                            if.isFalse.data = register:fixedImageWidth
                        }
                        width {
                            data = register:currentImageWidth
                            wrap = |c
                            if.isTrue.data = register:fixedImageDimensions
                        }
                        height {
                            data = register:currentImageHeight
                            wrap = |c
                            if.isTrue.data = register:fixedImageDimensions
                        }
                    }
                    altText {
                        data = file:current:alternative // field:alternative // file:current:title // field:header
                        stripHtml = 1
                        htmlSpecialChars = 1
                    }
                }
            }
            # Videos
            4 = CASE
            4 {
                key {
                    data = file:current:extension
                    case = lower
                }
                youtube = TEXT
                youtube {
                    value (
                        <div class="embedvideo embedvideo--youtube" data-height="{register:currentImageHeight}" data-width="{register:currentImageWidth}" data-placeholderid="youtubevideo-{file:current:contents}" data-videoid="{file:current:contents}" data-autoplay="{file:current:autoplay}" style="width: {register:currentImageWidth}px; padding-bottom: {register:currentImageHeightPercent}%;">
                            <div id="youtubevideo-{file:current:contents}"></div>
                        </div>
                    )
                    insertData = 1
                }
                vimeo = TEXT
                vimeo {
                    value (
                        <div class="embedvideo embedvideo--vimeo" style="width: {register:currentImageWidth}px; padding-bottom: {register:currentImageHeightPercent}%;">
                            <iframe src="//player.vimeo.com/video/{file:current:contents}?autoplay={file:current:autoplay}" width="{register:currentImageWidth}" height="{register:currentImageHeight}" allowfullscreen></iframe>
                        </div>
                    )
                    insertData = 1
                }
                mp4 = COA
                mp4 {
                    10 = LOAD_REGISTER
                    10 {
                        videoheight {
                            current = 1
                            setCurrent = {register:currentImageWidth} / 16 * 9
                            setCurrent.insertData = 1
                            prioriCalc = 1
                            intval = 1
                            override {
                                data = register:currentImageHeight
                                if.isTrue.data = register:fixedImageDimensions
                            }
                        }
                        videoheightPercent.cObject = TEXT
                        videoheightPercent.cObject {
                            current = 1
                            setCurrent {
                                data = register:videoheight
                                dataWrap = | / {register:currentImageWidth} * 100
                            }
                            prioriCalc = 1
                        }
                        autoplay {
                            current = 1
                            setCurrent = autoplay
                            setCurrent.if.isTrue.data = file:current:autoplay
                        }
                        poster.cObject = FILES
                        poster.cObject {
                            stdWrap {
                                if.isTrue.data = file:current:tx_sitepackage_posterimage
                                outerWrap = poster="|"
                            }
                            references {
                                table = sys_file_reference
                                fieldName = tx_sitepackage_posterimage
                                uid.data = file:current:uid
                            }
                            renderObj = IMG_RESOURCE
                            renderObj {
                                file {
                                    import.data = file:current:uid
                                    treatIdAsReference = 1
                                    width {
                                        data = register:currentImageWidth
                                        wrap = |c
                                    }
                                    height {
                                        data = register:videoheight
                                        wrap = |c
                                    }
                                }
                            }
                        }
                    }
                    20 = COA
                    20 {
                        stdWrap {
                            outerWrap = <div class="embedvideo" style="width: {register:currentImageWidth}px; padding-bottom: {register:videoheightPercent}%;">|</div>
                            outerWrap.insertData = 1
                            dataWrap = <video {register:autoplay} controls="controls" width="{register:currentImageWidth}" height="{register:videoheight}" {register:poster}>|</video>
                        }
                        10 = TEXT
                        10 {
                            value = <source src="{file:current:publicUrl}" type="video/{file:current:extension}" />
                            insertData = 1
                        }
                        20 = FILES
                        20 {
                            stdWrap {
                                if.isTrue.data = file:current:tx_sitepackage_posterimage
                            }
                            references {
                                table = sys_file_reference
                                fieldName = tx_sitepackage_posterimage
                                uid.data = file:current:uid
                            }
                            renderObj = IMAGE
                            renderObj {
                                file {
                                    import.data = file:current:uid
                                    treatIdAsReference = 1
                                    width {
                                        data = register:currentImageWidth
                                        wrap = |c
                                    }
                                    height {
                                        data = register:videoheight
                                        wrap = |c
                                    }
                                }
                                altText {
                                    data = file:current:alternative // field:alternative // file:current:title // field:header
                                    stripHtml = 1
                                    htmlSpecialChars = 1
                                }
                            }
                        }
                    }
                    30 = RESTORE_REGISTER
                }
            }
        }
        # Bildunterschrift ausgeben
        20 = TEXT
        20 {
            data = file:current:description
            stdWrap {
                htmlSpecialChars = 1
                br = 1
                outerWrap = <figcaption>|</figcaption>
                required = 1
                if.isFalse.data = register:disableCaption
            }
        }
    }
    # Verfügbarer Platz für das Inhaltselement
    columnSpace = TEXT
    columnSpace {
        data = register:gridColumnWidth
        ifEmpty.cObject < temp.pageColumnWidth
    }
    # Eigenes Rendering für Fluid Styled Content Medien definieren
    ttcontentimage = COA
    ttcontentimage {
        10 = LOAD_REGISTER
        10 {
            # Verfügbarer Platz für das Inhaltselement
            columnSpace.cObject < temp.columnSpace

            # Verfügbarer Platz für Bilder
            imagesSpace.cObject = TEXT
            imagesSpace.cObject {
                current = 1
                setCurrent {
                    data = register:columnSpace
                    wrap = ( | / 100 * {$styles.content.textmedia.floatimagemaxwidthpercent} ) - {$styles.content.textmedia.columnSpacing}
                    wrap.if {
                        value = 17,18,25,26
                        isInList.field = imageorient
                    }
                }
                prioriCalc = intval
            }
            # Anzahl der Bildspalten. Wenn Bildanzahl kleiner, wird Bildanzahl genommen
            imageCols.cObject = TEXT
            imageCols.cObject {
                current = 1
                setCurrent {
                    cObject = FILES
                    cObject {
                        references {
                            table = tt_content
                            fieldName.cObject = CASE
                            fieldName.cObject {
                                key.field = CType
                                default = TEXT
                                default.value = image
                                textmedia = TEXT
                                textmedia.value = assets
                            }
                        }
                        renderObj = TEXT
                        renderObj {
                            data = register:FILES_COUNT
                            wrap = |,
                        }
                    }
                    override {
                        field = imagecols
                        wrap = |,
                        if {
                            value.field = imagecols
                            isGreaterThan.data = register:FILES_COUNT
                        }
                    }
                }
                split {
                    token = ,
                    cObjNum = 1 |*| 2 |*| 2
                    1.current = 1
                }
            }
            # Bildspalten in Prozent
            imageColPercent.cObject = TEXT
            imageColPercent.cObject {
                current = 1
                setCurrent = 100 / {register:imageCols}
                setCurrent.insertData = 1
                prioriCalc = 1
            }
            # Zusätzliche CSS-Klassen definieren
            imagewrapperAdditionalClass.cObject = COA
            imagewrapperAdditionalClass.cObject {
                10 = TEXT
                10 {
                    value = cols
                    if {
                        value = 1
                        isGreaterThan.data = register:imageCols
                    }
                }
            }
            # Bildrahmenbreite berechnen
            singleImageBorderWidth.cObject = TEXT
            singleImageBorderWidth.cObject {
                fieldRequired = imageborder
                value = 2 * {$styles.content.textmedia.borderWidth}
                prioriCalc = 1
            }
            # Maximale Bildgröße eines einzelnen Bildes berechnen
            singleImageMaxWidth.cObject = TEXT
            singleImageMaxWidth.cObject {
                current = 1
                setCurrent = ( {register:imagesSpace} - {$styles.content.textmedia.columnSpacing} * ( {register:imageCols} - 1 ) ) / {register:imageCols} - {register:singleImageBorderWidth}
                setCurrent {
                    insertData = 1
                    override {
                        field = imagewidth
                        if {
                            value.data = register:imagesSpace
                            isLessThan.field = imagewidth
                        }
                    }
                }
                prioriCalc = intval
            }
            # Größe für Wrapper-Tabelle berechnen, welche eingefügt wird, wenn Bilder nebeneinander platziert werden (imagecols > 1)
            imageWrappertableWidth.cObject = TEXT
            imageWrappertableWidth.cObject {
                current = 1
                setCurrent = ( {register:singleImageMaxWidth} * {register:imageCols} ) + ( {$styles.content.textmedia.columnSpacing} * {register:imageCols} )
                setCurrent.insertData = 1
                prioriCalc = intval
            }
        }
        # Debugging-Ausgabe, wenn in Konstanten ($config.debug) aktiviert
        15 = COA
        15 {
            stdWrap {
                wrap = <div style="border: 2px solid red; padding: 10px; overflow: hidden;">|</div>
                if.value = {$config.debug}
                if.equals = 1
                if.isTrue.data = TSFE:beUserLogin
            }
            10 = TEXT
            10 {
                noTrimWrap = |<div>Platz für Inhalt: |px</div>|
                data = register:columnSpace
            }
            20 = TEXT
            20 {
                noTrimWrap = |<div>Platz für Bilder: |px</div>|
                data = register:imagesSpace
            }
            30 = TEXT
            30 {
                noTrimWrap = |<div>Bildgröße: |px</div>|
                noTrimWrap.override = |<div>Bildgröße: |px (manuell gesetzt: {field:imagewidth}px)</div>|
                noTrimWrap.override.if.isTrue.field = imagewidth
                noTrimWrap.override.insertData = 1
                data = register:singleImageMaxWidth
            }
            40 = TEXT
            40 {
                noTrimWrap = |<div>Bildspalten: | (eingestellt: {field:imagecols})</div>|
                noTrimWrap.insertData = 1
                data = register:imageCols
            }
            50 = TEXT
            50 {
                noTrimWrap = |<div>Bildrahmen: |{$styles.content.textmedia.borderWidth}px</div>|
                fieldRequired = imageborder
            }
        }
        # Ausgabe der Bilder
        20 = FILES
        20 {
            stdWrap {
                required = 1
                dataWrap = <div class="content-images border-{field:imageborder} align-{field:imageorient} caption-align-{field:imagecaption_position} {register:imagewrapperAdditionalClass}">|</div>
                innerWrap = <div class="imagetablewrap" style="width: {register:imageWrappertableWidth}px;"><div class="imagetable">|</div></div>
                innerWrap {
                    insertData = 1
                    if {
                        value = 1
                        isGreaterThan.data = register:imageCols
                    }
                }
            }
            references {
                table = tt_content
                uid.field = uid
                fieldName.cObject = CASE
                fieldName.cObject {
                    key.field = CType
                    default = TEXT
                    default.value = image
                    textmedia = TEXT
                    textmedia.value = assets
                }
            }
            renderObj = COA
            renderObj {
                10 = LOAD_REGISTER
                10 {
                    # Originalbreite des Bildes
                    originalImageWidth.cObject < temp.getFalOriginalImageWidth

                    # Originalhöhe des Bildes
                    originalImageHeight.cObject < temp.getFalOriginalImageHeight

                    # Bildbreite des aktuellen Bildes berechnen
                    currentImageWidth.cObject = TEXT
                    currentImageWidth.cObject {
                        data = register:singleImageMaxWidth
                        override {
                            data = register:originalImageWidth
                            if {
                                value.data = register:originalImageWidth
                                isGreaterThan.data = register:singleImageMaxWidth
                                isTrue.cObject = TEXT
                                isTrue.cObject {
                                    value = true
                                    if {
                                        value.data = file:current:type
                                        equals = 4
                                        negate = 1
                                    }
                                }
                            }
                        }
                        round.roundType = floor
                    }
                    # Breite des Imagewrappers berechnen
                    currentImageWrapperWidth.cObject = TEXT
                    currentImageWrapperWidth.cObject {
                        current = 1
                        setCurrent {
                            data = register:currentImageWidth
                            dataWrap = | + {register:singleImageBorderWidth}
                        }
                        prioriCalc = intval
                    }
                    # Bildhöhe des aktuellen Bildes errechnen
                    currentImageHeight.cObject < temp.getFalCurrentImageHeight

                    # Bildhöhe in % des aktuellen Bildes errechnen
                    currentImageHeightPercent.cObject < temp.getFalCurrentImageHeightPercent
                }
                20 = COA
                20 {
                    stdWrap {
                        outerWrap = <div class="content-image">|</div>
                        outerWrap {
                            override = <div class="content-image" style="width: {register:imageColPercent}%;">|</div>
                            override {
                                insertData = 1
                                if {
                                    value = 1
                                    isGreaterThan.data = register:imageCols
                                }
                            }
                        }
                        dataWrap = <figure class="content-image-wrap" style="width: {register:currentImageWrapperWidth}px;">|</figure>
                    }
                    # Debugging-Ausgabe
                    5 = COA
                    5 {
                        stdWrap {
                            wrap = <div style="border: 2px solid red; padding: 10px; overflow: hidden;">|</div>
                            if.value = {$config.debug}
                            if.equals = 1
                            if.isTrue.data = TSFE:beUserLogin
                        }
                        5 = TEXT
                        5 {
                            noTrimWrap = |<div>Original Bildbreite: |px</div>|
                            data = register:originalImageWidth
                        }
                        7 = TEXT
                        7 {
                            noTrimWrap = |<div>Original Bildhöhe: |px</div>|
                            data = register:originalImageHeight
                        }
                        10 = TEXT
                        10 {
                            noTrimWrap = |<div>Bildbreite: |px</div>|
                            data = register:currentImageWidth
                        }
                        20 = TEXT
                        20 {
                            noTrimWrap = |<div>Bildhöhe: |px</div>|
                            data = register:currentImageHeight
                        }
                        25 = TEXT
                        25 {
                            noTrimWrap = |<div>Bildhöhe in %: |%</div>|
                            data = register:currentImageHeightPercent
                        }
                        30 = TEXT
                        30 {
                            noTrimWrap = |<div>Gesamtanzahl Bilder: |</div>|
                            data = register:FILES_COUNT
                        }
                    }
                    # Medien ausgeben
                    10 < temp.renderFalMedia
                }
                30 = RESTORE_REGISTER
            }
        }
        30 = RESTORE_REGISTER
    }
    stripShy {
        replacement {
            10 {
                search = &shy;
                replace = 
            }
        }
    }
}
