/*
 Example:

 $document.on('click', 'a.js-new-selection', function (e) {
 var url = $(this).attr('href');

 e.preventDefault();

 abavoLbmessage({
 text: TYPO3.lang['js.configurator.confirm-new-selection']['0']['target'],
 type: 'confirm',
 callback: function() {
 getAjaxContent(url, '#js-Ajax-Content');
 }
 });
 });
 */
var abavoLbmessage = function (options) {
    var options = $.extend({
            text: '',
            type: 'alert',
            status: 'none',
            modal: true,
            showCloseBtn: false,
            callback: false
        }, options),
        mp_available = (typeof $.magnificPopup == 'object') ? true : false,
        mpInstance,
        lbmessage = '',
        lbcontent = options.text,
        lbcontentclass = '',
        lbbuttons = '<a href="#" class="button button--small button--primary lbmessage-ok">' + TYPO3.lang['js.abavolbmessage.ok']['0']['target'] + '</a>';

    if (mp_available) {
        mpInstance = $.magnificPopup.instance;

        if (options.type == 'confirm') {
            lbbuttons = lbbuttons + '<a href="#" class="button button--small button--secondary lbmessage-cancel">' + TYPO3.lang['js.abavolbmessage.cancel']['0']['target'] + '</a>';
        }

        if (options.status == 'success') {
            lbcontentclass = 'fontello-icon fontello-pos-before-absolute fontello-icon-ok';
        } else if (options.status == 'info') {
            lbcontentclass = 'fontello-icon fontello-pos-before-absolute fontello-icon-attention-alt';
        }

        if (lbcontent != '') {
            lbmessage = lbmessage + '<div class="lbcontent ' + lbcontentclass + '">' + lbcontent + '</div>';
        }

        if (lbbuttons != '') {
            lbmessage = lbmessage + '<div class="lbbuttons">' + lbbuttons + '</div>';
        }

        mpInstance.open({
            items: {
                src: lbmessage
            },
            type: 'inline',
            mainClass: 'lbmessage type-' + options.type + ' status-' + options.status,
            showCloseBtn: options.showCloseBtn,
            modal: options.modal,
            removalDelay: 200,
            callbacks: {
                open: function () {
                    $('.lbbuttons a.lbmessage-cancel').click(function (e) {
                        e.preventDefault();
                        mpInstance.close();
                    });

                    $('.lbbuttons a.lbmessage-ok').click(function (e) {
                        e.preventDefault();
                        mpInstance.close();

                        if (options.callback && typeof(options.callback) === 'function') {
                            options.callback();
                        }
                    });
                }
            }
        });

    } else {

        if (options.type == 'alert') {
            alert(options.text);

        } else if (options.type == 'confirm') {
            if (window.confirm(options.text) && options.callback && typeof(options.callback) === 'function') {
                options.callback();
            }
        }
    }

    return this;
};
