$(function() {
    $('.teaseraccordion').each(function() {
        var $menu = $(this).find('.teaseraccordion__teasermenu'),
            $accordion = $(this).find('.teaseraccordion__accordion');

        $accordion
            .on('afterChange', function(e, slick, currentSlide) {
                var slideNumber = currentSlide + 1;

                $menu.find('.teaseraccordion__teasermenu__entry.current').removeClass('current');
                $menu.find('.teaseraccordion__teasermenu__entry[data-slide="' + slideNumber + '"]').addClass('current');
            })
            .slick({
                dots: false,
                pauseOnHover: false,
                autoplay: false,
                fade: true,
                cssEase: 'ease-in-out',
                speed: 500,
                arrows: false
            });

        $menu.on('click', '.teaseraccordion__teasermenu__entry', function(e) {
            e.preventDefault();
            $accordion.slick('slickGoTo', $(this).data('slide') - 1, true);
        });

        $accordion.on('click', '.teaseraccordion__accordion__entry__header', function(e) {
            var $entry = $(this).parents('.teaseraccordion__accordion__entry');

            e.preventDefault();

            if ($entry.hasClass('current')) {
                $entry.removeClass('current');
            } else {
                $accordion.find('.teaseraccordion__accordion__entry.current').removeClass('current');
                $entry.addClass('current');
            }
        });
    });
});
