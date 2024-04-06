(function ($) {
    $.fn.abavo_accordion = function (options) {
        var set = $.extend({
                slideDuration: 300,
                actClass: 'current',
                openedClass: 'opened',
                showFirstEntry: false, // first entry will be opened on page load
                heightMode: false, // alternative mode of hiding the accordion elements ("height: 0px; overflow: hidden;" instead of "display: none;"), so elements inside of accordion elements have their final dimensions and are correctly initialized
                heightModeTransition: 'swing',
                accElementDiv: '.accordion-element',
                accHeaderDiv: '.accordion-header',
                accContentDiv: '.accordion-content',
                accContentWrapperDiv: '.accordion-wrapper',
                toggleAllAnchor: '.show_close_all'
            }, options),

            $acc = this,
            $accEntries = $acc.children(set.accElementDiv),
            $accHeaderDivs = $accEntries.children(set.accHeaderDiv),
            hash = window.location.hash.substring(1)
            ;

        function hideAccEntry($entry) {
            var $content = $entry.children(set.accContentDiv),
                $header = $entry.children(set.accHeaderDiv);

            if (set.heightMode) {
                $content.stop(1).animate({height: 0}, set.slideDuration, set.heightModeTransition);
            } else {
                $content.slideUp(set.slideDuration);
            }

            $entry.removeClass(set.actClass);
            $content.attr('aria-expanded', 'false');
            $header.attr('aria-expanded', 'false');

            $entry.find('.tab-navigation').trigger('sticky_kit:detach');
        }

        function showAccEntry($entry, scrolling) {
            var $content = $entry.children(set.accContentDiv),
                $header = $entry.children(set.accHeaderDiv),
                scrolling = typeof scrolling !== 'undefined' ? scrolling : true;

            if (set.heightMode) {
                $content.each(function () {
                    $(this).stop(1).animate({height: $(this).find(set.accContentWrapperDiv).outerHeight(true)}, set.slideDuration, set.heightModeTransition, function () {
                        if (scrolling) {
                            scrollToElementIfNotFullyVisible($entry);
                        }

                        setTabsSticky($entry.find('.tab-navigation'));
                    });
                });
            } else {
                $content.slideDown(set.slideDuration, function () {
                    if (scrolling) {
                        scrollToElementIfNotFullyVisible($entry);
                    }

                    setTabsSticky($entry.find('.tab-navigation'));
                });
            }

            $entry.addClass(set.actClass);
            $content.attr('aria-expanded', 'true');
            $header.attr('aria-expanded', 'true');
        }

        function toggleAccEntry($currEntry, scrolling) {
            var scrolling = typeof scrolling !== 'undefined' ? scrolling : true;

            $currEntry.children(set.accContentDiv).each(function() {
                if ($currEntry.hasClass(set.actClass)) {
                    hideAccEntry($currEntry);
                    $acc.removeClass(set.openedClass);
                } else {
                    if (set.hideOtherEntries) {
                        hideAccEntry($accEntries.not($currEntry));
                    }
                    showAccEntry($currEntry, scrolling);
                }
            });
        }

        if (set.heightMode) {
            $acc.addClass('heightmode');
        }

        $accHeaderDivs.click(function (e) {
            var $currEntry = $(this).parent(set.accElementDiv);

            e.preventDefault();

            toggleAccEntry($currEntry);
        });

        $accHeaderDivs.keypress(function (e) {
            if (e.which === 13) {
                var $currEntry = $(this).parent(set.accElementDiv);

                toggleAccEntry($currEntry);

                return false;
            }
        });

        $acc.on('click', set.toggleAllAnchor, function (e) {
            e.preventDefault();

            if ($acc.hasClass(set.openedClass)) {
                hideAccEntry($accEntries);
                $acc.removeClass(set.openedClass);
            } else {
                showAccEntry($accEntries);
                $acc.addClass(set.openedClass);
            }
        });

        if (set.showFirstEntry) {
            var $currEntry = $accHeaderDivs.first().parent(set.accElementDiv);

            showAccEntry($currEntry, false);
        } else {
            hideAccEntry($accEntries);
        }

        if (hash) {
            var $accheader = $('#acc-header-' + hash);

            if ($accheader.length) {
                $window.on('load', function () {
                    var $currEntry = $accheader.parent(set.accElementDiv);

                    toggleAccEntry($currEntry);
                });
            }
        }

        return this;
    };
}(jQuery));
