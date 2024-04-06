/**
 * abavo_search - Autocomplete_jqueryui.js
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 06.06.2018 - 09:55:21
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

var delayAutocomplete = (function() {
    var timer = 0;
    return function(callback, ms) {
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

function initAbavosearchAutocomplete($wrapper) {
    $wrapper.find('.search-form .autocomplete').each(function (i, el) {
        var $el = $(el),
            $form = $el.parents('form'),
            minlength = (typeof $el.data('minlen') !== 'undefined') ? $el.data('minlen') : 3;

        $el.tooltipster({
			contentAsHTML: true,
			IEmin: 9,
            delay: 0,
            content: '',
            interactive: true,
            side: ['bottom', 'left', 'top', 'right'],
            maxWidth: 400,
            animationDuration: 150,
            trigger: 'custom',
            triggerClose: {
                click: true,
                tap: true
            }
        });

        $el.on('keyup', function() {
            delayAutocomplete(function() {
                var term = $el.val();

                $el.tooltipster('close');

                if (term.length >= minlength) {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        cache: false,
                        url: $el.data('autocompleteurl'),
                        data: {
                            'tx_abavosearch_pidata': {
                                'term': term
                            }
                        }
                    }).done(function(data) {
                        if (data.length) {
                            var content = '<ul class="search-autocomplete-result">';

                            for (var i = 0; i < data.length; i++) {
								var href = (typeof data[i]['uri'] === 'undefined') ? 'javascript:void(0);' : data[i]['uri'];
								var selectorClass = (typeof data[i]['uri'] === 'undefined') ? 'js-trigger-searchform-submit' : '';
                                content = content + '<li><a href="' + href + '" class="' + selectorClass + '">' + data[i]['label'] + '</a></li>';
                            }

                            content = content + '</ul>';

                            $el.tooltipster('content', content).tooltipster('open');

                            $($el.tooltipster('elementTooltip')).find('a.js-trigger-searchform-submit').click(function(e) {
                                var val = $(this).text();

                                $el.val(val).tooltipster('close');
                                $form.submit();
                            });
                        }
                    });
                }
            }, 250);
        });
    });
}

