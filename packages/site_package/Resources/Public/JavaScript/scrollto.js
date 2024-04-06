function scrollToElement($el, event) {
    if ($el.length && !$el.hasClass('accordion-header')) {
        var scrollableParent = getScrollParent($el[0], false),
            $scrollableParent = $(scrollableParent),
            scrollableParentName = $scrollableParent[0].localName;

        if ($scrollableParent.length) {
            var elementScrollPos = $el.offset().top,
                currentScrollPos = $scrollableParent.scrollTop(),
                duration;

            if (typeof event !== 'undefined') {
                event.preventDefault();
            }

//            if (scrollableParentName === 'html' || $scrollableParent[0].scrollHeight > $scrollableParent.height()+2) {
                $scrollableParent = $htmlbody;

                if ($fixedheader.length) {
                    elementScrollPos = elementScrollPos - $fixedheader.height();
                }

//            } else {
//                elementScrollPos = elementScrollPos + currentScrollPos;
//            }

            elementScrollPos = Math.ceil(elementScrollPos) - 20;

            duration = Math.abs(elementScrollPos - currentScrollPos) * 0.3;

            if (duration < 300) {
                duration = 300;
            } else if (duration > 600) {
                duration = 600;
            }

            $scrollableParent.animate({scrollTop: elementScrollPos}, duration, 'swing');
        }
    }
}

/*
gets first scrollable parent
https://stackoverflow.com/questions/35939886/find-first-scrollable-parent
 */
function getScrollParent(element, includeHidden) {
    var style = getComputedStyle(element);
    var excludeStaticParent = style.position === "absolute";
    var overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/;

    if (style.position === "fixed") return document.body;
    for (var parent = element; (parent = parent.parentElement);) {
        style = getComputedStyle(parent);
        if (excludeStaticParent && style.position === "static") {
            continue;
        }
        if (overflowRegex.test(style.overflow + style.overflowY + style.overflowX)) return parent;
    }

    return document.body;
}

$(function() {
    $document.on('click', '#page a[href*="#"]', function(e) {
        scrollToElement($(this.hash), e);
    });
});
