var $window = $(window),
    $document = $(document),
    $html = $('html'),
    $body = $('body'),
    $htmlbody = $('html, body'),
    $header = $('#header'),
    $searchformbutton = $header.find('.search-form button'),
    $fixedheader = $('#fixedheader'),
    $fixedactionbar = $('#fixedactionbar'),
    $fixedactionbarclone = $('#fixedactionbar--clone'),
    $headerslider = $('#headerslider .slider'),
    $headerteaserslider = $('#headerteaserslider .slider'),
    $respmenu = $('#respmenu'),
    $ajaxloader = $('#loadingoverlay');


// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.

var debounce = function (func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

var scrollToElementIfNotFullyVisible = function ($el, duration) {
    duration = typeof duration !== 'undefined' ? duration : 300;

    if ($el.offset().top < $window.scrollTop() || $el.offset().top > ( $window.scrollTop() + $window.innerHeight()) ) {
        var scrollPos = $el.offset().top;

        $htmlbody.animate({
            scrollTop: scrollPos
        }, duration);
    }
};

var getUrlParameter = function (name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};
