$.extend(true, $.magnificPopup.defaults, {
    tClose: TYPO3.lang['js.magnificpopup.tClose']['0']['target'],
    tLoading: TYPO3.lang['js.magnificpopup.tLoading']['0']['target'],
    gallery: {
        tPrev: TYPO3.lang['js.magnificpopup.gallery.tPrev']['0']['target'],
        tNext: TYPO3.lang['js.magnificpopup.gallery.tNext']['0']['target'],
        tCounter: TYPO3.lang['js.magnificpopup.gallery.tCounter']['0']['target']
    },
    iframe: {
        patterns: {
            youtube: {
                src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
            }
        }
    }
});

$(function () {
    $document.on('click', '.content-images a.lightbox', function (e) {
        var $anchor = $(this),
            $gallery = $anchor.parents('.content-images'),
            items = new Array(),
            index = 1;

        $gallery.find('a.lightbox').filter(function() {
            var $el = $(this);

            if (!$el.parents('.slick-cloned').length) {
                items.push({
                    src: $el.attr('href'),
                    image: $el.find('img')
                });
            }
        });

        for (var i = 0; i < items.length; i++) {
            if (items[i].src === $anchor.attr('href')) {
                index = i++;
                break;
            }
        }

        if (items.length) {
            e.preventDefault();

            $.magnificPopup.close();

            $.magnificPopup.open({
                items: items,
                type: 'image',
                gallery: {
                    enabled: true,
                    preload: [0, 1]
                },
                zoom: {
                    enabled: true,
                    opener: function() {
                        return items[$.magnificPopup.instance.index].image;
                    }
                }
            }, index);
        }
    });

    $.support.cors = true;

    $document.on('click', 'a[target="lightbox"]', function (e) {
        var $el = $(this),
            url = $el.attr('href'),
            hash = this.hash,
            origUrl = url,
            additionalClass = (typeof $el.data('lightboxclass') !== 'undefined') ? (' ' + $el.data('lightboxclass')) : '',
            openInLightbox = true;

        if ($el.hasClass('js-nolightboxopen-ifinlightbox') && $el.parents('.mfp-content').length) {
            openInLightbox = false;
        }

        if (openInLightbox) {
            e.preventDefault();

            if (window.location.protocol == 'https:') {
                url = url.replace(/^http:\/\//i, 'https://');
            }

            if (mmenu) {
                mmenu.close();
            }

            $.magnificPopup.close();

            $.magnificPopup.open({
                items: {
                    src: url
                },
                mainClass: 'lightboxcontent' + additionalClass,
                type: 'ajax',
                closeOnBgClick: false,
                ajax: {
                    settings: {
                        cache: true,
                        dataType: 'html',
                        data: {
                            lightbox: 1
                        }
                    }
                },
                callbacks: {
                    ajaxContentAdded: function () {
                        window.picturefill();
                    },
                    updateStatus: function (data) {
                        if (data.status === 'error') {
                            $.magnificPopup.close();
                            window.open(origUrl, '_blank');
                        }
                    }
                },
                ajaxContentAdded: function () {
                    if (hash) {
                        var $el = $(hash);

                        if ($el.length) {
                            $('.mfp-wrap').animate({
                                scrollTop: $el.offset().top
                            }, 300, 'swing', function () {
                                if ($el.is('input')) {
                                    $el.focus();
                                }
                            });
                        }
                    }
                }
            });
        }
    });

    $document.on('click', 'a[href*="youtube.com/watch?"], a[href*="vimeo.com/"], a[target="iframe"], a[target="youtube"], a[target="vimeo"], a[target="video"]', function (e) {
        var url = $(this).attr('href');

        e.preventDefault();

        $.magnificPopup.close();

        $.magnificPopup.open({
            items: {
                src: url
            },
            type: 'iframe',
            callbacks: {
                updateStatus: function (data) {
                    if (data.status === 'error') {
                        $.magnificPopup.close();
                        window.open(url, '_blank');
                    }
                }
            }
        });
    });

    $document.on('click', 'a[href$=".mp4"]', function (e) {
        var url = $(this).attr('href');

        e.preventDefault();

        $.magnificPopup.close();

        $.magnificPopup.open({
            items: {
                src: '<div class="outerwrap"><video controls="controls" autoplay="autoplay" width="800" height="450"><source src="' + url + '" type="video/mp4"></video></div>'
            },
            mainClass: 'lightboxcontent videocontent',
            type: 'inline',
            callbacks: {
                updateStatus: function (data) {
                    if (data.status === 'error') {
                        $.magnificPopup.close();
                        window.open(url, '_blank');
                    }
                }
            }
        });
    });
});
