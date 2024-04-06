$respmenu.mmenu({
    extensions: [
        'position-back',
        'pagedim-black'
    ],
    navbar: {
        title: (typeof areatitle !== 'undefined') ? areatitle : 'Menu'
    },
    onClick: {
        setSelected: false
    }
});

var mmenu = $respmenu.data('mmenu');

$(function() {
    $('#hamburger').click(function(e) {
        e.preventDefault();

        window.scrollTo(0, 0);
        mmenu.open();
    });
});
