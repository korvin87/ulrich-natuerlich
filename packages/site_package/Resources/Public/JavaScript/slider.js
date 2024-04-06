$(function() {
    // Headerslider
    var $headersliderEntries = $headerslider.find('.entry'),
        $headerteasersliderEntries = $headerteaserslider.find('.headerteaserslider__entry');

    $headersliderEntries.first().addClass('lazyload');

    if ($headersliderEntries.length > 1) {
        $headersliderEntries.first().next().addClass('lazyload');

        $headerslider
            .on('init', function() {
                $headersliderEntries.show();
            })
            .on('beforeChange', function(e, slick, currentSlide, nextSlide) {
                $headerslider.find('.slick-slide[data-slick-index=' + nextSlide + '] .entry, .slick-slide[data-slick-index=' + (nextSlide + 1) + '] .entry').addClass('lazyload');
            })
            .slick({
                dots: false,
                pauseOnHover: false,
                autoplay: false,
                fade: false,
                cssEase: 'ease-in-out',
                speed: 500,
                arrows: false,
                asNavFor: ($headerteaserslider.length) ? $headerteaserslider : null
            });

        $headerteaserslider
            .on('init', function() {
                $headerteasersliderEntries.show();
            })
            .slick({
                dots: false,
                pauseOnHover: true,
                autoplay: true,
                autoplaySpeed: 5000,
                fade: true,
                cssEase: 'ease-in-out',
                speed: 500,
                arrows: true,
                asNavFor: ($headerslider.length) ? $headerslider : null
            });
    }


    // Bildergalerie
    $('.imagegallery').each(function() {
        var $gallery = $(this),
            $slider = $gallery.children('.imagegallery__mainimage'),
            $sliderThumbMenu = $gallery.children('.imagegallery__thumbnails');

        $slider.on('init', function() {
            $gallery.removeClass('loading');

        }).on('beforeChange', function(e, slick, currentSlide, nextSlide) {
            var $currentSlide = $slider.find('.slick-slide[data-slick-index=' + currentSlide + ']'),
                $ytVideo = $currentSlide.find('.embedvideo--youtube iframe'),
                $vimeoVideo = $currentSlide.find('.embedvideo--vimeo iframe'),
                $html5Video = $currentSlide.find('.embedvideo video');

            if ($ytVideo.length && typeof YT !== 'undefined') {
                YT.get($ytVideo.attr('id')).pauseVideo();
            } else if ($vimeoVideo.length) {
                var player = new Vimeo.Player($vimeoVideo);

                player.getPaused().then(function(paused) {
                    player.pause();
                });
            } else if ($html5Video.length) {
                $html5Video[0].pause();
            }

        }).on('afterChange', function(event, slick, currentSlide) {
            var $currentSlide = $slider.find('.slick-slide[data-slick-index=' + currentSlide + ']'),
                $ytVideo = $currentSlide.find('.embedvideo--youtube iframe'),
                $vimeoVideo = $currentSlide.find('.embedvideo--vimeo iframe'),
                $html5Video = $currentSlide.find('.embedvideo video');

            if ($ytVideo.length && typeof YT !== 'undefined') {
                YT.get($ytVideo.attr('id')).playVideo();
            } else if ($vimeoVideo.length) {
                var player = new Vimeo.Player($vimeoVideo);

                player.getPaused().then(function(paused) {
                    player.play();
                });
            } else if ($html5Video.length) {
                $html5Video[0].play();
            }

        }).slick({
            dots: false,
            asNavFor: '#' + $slider.data('sliderthumbid'),
            cssEase: 'ease',
            speed: 400,
            fade: true,
            adaptiveHeight: true,
            arrows: false
        });

        $sliderThumbMenu.slick({
            asNavFor: '#' + $sliderThumbMenu.data('sliderid'),
            dots: false,
            arrows: true,
            focusOnSelect: true,
            infinite: true,
            respondTo: 'slider',
            slidesToShow: 9,
            responsive: [
                { breakpoint: 1250, settings: { slidesToShow: 8 } },
                { breakpoint: 1100, settings: { slidesToShow: 7 } },
                { breakpoint: 950, settings: { slidesToShow: 6 } },
                { breakpoint: 800, settings: { slidesToShow: 5 } },
                { breakpoint: 650, settings: { slidesToShow: 4 } },
                { breakpoint: 500, settings: { slidesToShow: 3 } },
                { breakpoint: 350, settings: { slidesToShow: 2 } }
            ]
        });
    });


    // Imageslider
    $('.imageslider').slick({
        dots: false,
        arrows: true,
        infinite: true,
        speed: 500,
        respondTo: 'slider',
        autoplay: true,
        autoplaySpeed: 3000,
        slidesToShow: 4,
        responsive: [
            {breakpoint: 980, settings: {slidesToShow: 3}},
            {breakpoint: 760, settings: {slidesToShow: 2}},
            {breakpoint: 500, settings: {slidesToShow: 1}}
        ]
    });
});
