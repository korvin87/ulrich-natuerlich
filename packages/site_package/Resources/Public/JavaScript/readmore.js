function toggleReadmoreButton($container) {
    var outerHeight = $container.find('.readmore__container__content').height(),
        innerHeight = $container.find('.readmore__container__content')[0].scrollHeight;

    if (innerHeight <= outerHeight) {
        $container.removeClass('show-buttons');
    } else {
        $container.addClass('show-buttons');
    }
}

var stoggleReadmoreButtonOnce = debounce(function() {
    $('.readmore__container').each(function() {
        toggleReadmoreButton($(this));
    });
}, 250);

$(function() {
    $('.readmore__container__buttons').on('click', 'a', function(e) {
        var $container = $(this).parents('.readmore__container');

        e.preventDefault();

        if ($container.hasClass('open')) {
            $container.removeClass('open');
        } else {
            $container.addClass('open');
        }
    });

    $('.readmore__container').each(function() {
        toggleReadmoreButton($(this));
    });

    $window.on('resize', function() {
        stoggleReadmoreButtonOnce();
    });
});
