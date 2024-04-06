var setStickyWrapperHeightOnce = debounce(function() {
    var $stickywrapper = $('.sticky-wrapper');

    if ($stickywrapper.length) {
        $stickywrapper.height($fixedheader.height());
    }
}, 250);

function showSearchboxOverlay() {
    var $form = $searchformbutton.parents('form'),
        $input = $searchformbutton.siblings('input.search-word'),
        posTop = 0,
        posRight = 0;

    window.scrollTo(0, 0);

    posTop = $form.offset().top;
    posRight = $window.width() - ($form.offset().left + $form.outerWidth());

    $form.css('top', posTop).css('right', posRight).css('left', posRight);
    $body.addClass('searchform-overlay-active');
    $input.focus();
}

function hideSearchboxOverlay() {
    $searchformbutton.parents('form').css('top', '').css('right', '').css('left', '');
    $body.removeClass('searchform-overlay-active');
}

$(function() {
	var stickyHeader = new Waypoint.Sticky({
		element: $fixedheader[0]
	});

	var animatedHeader = new Waypoint({
		element: $header[0],
		handler: function(dir) {
			if (dir === 'down') {
				$header.addClass('narrow');
			} else {
				$header.removeClass('narrow');
				setStickyWrapperHeightOnce();
			}
		},
		offset: function() {
			return -this.element.clientHeight
		}
	});

	if ($fixedactionbarclone.length) {
        $fixedactionbarclone.appendTo($fixedheader);

        var fixedactionbarcloneWaypoint = new Waypoint({
            element: $fixedactionbar[0],
            handler: function(dir) {
                if (dir === 'down') {
                    $body.addClass('show-fixed-actionbar');
                } else {
                    $body.removeClass('show-fixed-actionbar');
                }
            }
        });

        $('.fixedactionbar').on('click', '.js-open-fixedactionbar__right--acccontent', function (e) {
            e.preventDefault();

            $('.fixedactionbar').toggleClass('accontent-open');
        });
    }

	$window.resize(function() {
		setStickyWrapperHeightOnce();
	});

    $header.find('[data-tooltip-content]').click(function(e) {
        var $el = $(this),
            $target = $($el.data('tooltip-content'));

        if (!$el.hasClass('tooltipstered') && $target.length) {
            e.preventDefault();

            $el.tooltipster({
                delay: 0,
                interactive: true,
                side: ['bottom', 'right', 'left', 'top'],
                maxWidth: 400,
                animationDuration: 150,
                trigger: 'click',
                contentCloning: true,
                functionAfter: function (origin) {
                    origin.destroy();
                }
            }).tooltipster('open');
        }
    });

    $searchformbutton.click(function(e) {
        if (!$searchformbutton.siblings('input.search-word').is(':visible')) {
            e.preventDefault();
            showSearchboxOverlay();
        } else {
            hideSearchboxOverlay();
        }
    });

    $('#searchoverlay').click(function() {
        hideSearchboxOverlay();
    });
});







$(document).ready(function(e) {
	$("#header .search-form button").hover(function(event) {
		$(".search-new form.search-form").addClass("fade-open");
		event.stopPropagation();
	});


      if ($("#ulrich-products-dh").hasClass("p-handspülen")) {
        $("body").addClass('p-handspülen');
        console.log("addClass ")
      }
      if ($("#ulrich-products-dh").hasClass("p-reinigenpflegen")) {
        $("body").addClass('p-reinigenpflegen');
        console.log("addClass ")
      }

      if ($("#ulrich-products-dh").hasClass("p-maschinenspülen")) {
        $("body").addClass('p-maschinenspülen');
        console.log("addClass ")
      }


      if ($("#ulrich-products-dh").hasClass("p-waschen")) {
        $("body").addClass('p-waschen');
        console.log("addClass ")
      }
      if ($("#ulrich-products-dh").hasClass("p-entkalken")) {
        $("body").addClass('p-entkalken');
        console.log("addClass ")
      }
      if ($("#ulrich-products-dh").hasClass("p-unverpackt-themenwelten")) {
        $("body").addClass('p-unverpackt-themenwelten');
        console.log("addClass ")
      }

	$('.frame-type-menu_pages.frame-layout-10').addClass("arrow-list arrow-list--250");



});
