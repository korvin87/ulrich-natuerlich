$(function () {
    $('.abavo_accordion').each(function () {
        $(this).abavo_accordion({
            showFirstEntry: $(this).data('showfirstentry'),
            slideDuration: $(this).data('slideduration')
        });
    });
});