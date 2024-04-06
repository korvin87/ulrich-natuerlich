/**
 * abavo_search - Autocomplete_jqueryui.js
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 06.06.2018 - 09:55:21
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

function initAbavosearchAutocomplete($wrapper) {
	$wrapper.find('.search-form .autocomplete').each(function (i, el) {
		var $el = $(el),
			$form = $el.parents('form');

		$el.autocomplete({
			source: function (request, response) {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					cache: false,
					url: $el.data('autocompleteurl'),
					data: {
						'tx_abavosearch_pidata': {
							'term': request.term
						}
					},
					success: response,
					error: function () {
						response([]);
					}
				})
			},
			minLength: $el.data('minlen'),
			select: function (event, ui) {
				if (typeof ui.item.uri === 'undefined') {
					$el.val(ui.item.value);
					$form.submit();
				} else {
					location.href = ui.item.uri;
				}
			}
		});
	});
}
