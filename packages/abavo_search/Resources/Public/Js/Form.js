/* 
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

$(function () {
	// Autocomplete
	initAbavosearchAutocomplete($('body'));

	// Autocomplete for correct width (http://stackoverflow.com/questions/5643767/jquery-ui-autocomplete-width-not-set-correctly/11845718#11845718)
	if (typeof jQuery.ui !== 'undefined') {
		jQuery.ui.autocomplete.prototype._resizeMenu = function () {
			var ul = this.menu.element;
			ul.outerWidth(this.element.outerWidth());
		};
	}

	$(document)
			// checkall
			.on('click', '.search-form #checkall', function () {
				$(this).parents('.search-form').find('input:checkbox').prop('checked', this.checked);
			})
			.on('click', '.search-form .facet-check', function () {
				$(this).parents('.search-form').find('#checkall').prop('checked', false);
			})

			// enable/disable Search-Button
			.on('keyup keypress blur change click', '.search-form .search-word', function () {
				if ($(this).val().length >= $(this).data('minlen')) {
					$(this).parents('.search-form').find('.search-submit-button').prop('disabled', false);
					$(this).parents('.search-form').find('.submitOnChange').prop('disabled', false);
				} else {
					$(this).parents('.search-form').find('.search-submit-button').prop('disabled', true);
					$(this).parents('.search-form').find('.submitOnChange').prop('disabled', true);
				}
			})

			// show overlay if exists
			.on('submit', '.search-form', function () {
				$('#loadingoverlay').show();
			})

			// submit sorting
			.on('change', '.search-form .submitOnChange', function () {
				console.log($(this).closest('form'));
				$(this).closest('form').submit();
			})
			;

	/**
	 * AbavoSearch Form jQuery plugin
	 *
	 * @returns {AbavoSearch}
	 */
	$.fn.abavoSearchForm = function () {

		// defaults

		/**
		 * Get a URL parameter by name
		 *
		 * @param {String} name
		 * @returns {unresolved}
		 */
		this.getURLParameter = function (name) {
			return decodeURIComponent((RegExp('[?|&]' + name + '=' + '(.+?)(&|$)').exec(location.search) || [null, null])[1]);
		};

		/**
		 * Append/Replace a URL parameter
		 *
		 * @param {type} name
		 * @param {type} value
		 * @param {type} url
		 * @returns {undefined}
		 */
		this.appendURLParameter = function (name, value, url) {
			if (history.pushState) {
				var currentURL = url || window.location.href;

				var currentURLArray = currentURL.split('?');
				var urlParts = [currentURLArray[0]];
				var newParam = name + '=' + value;

				if (location.search.length) {
					var params = currentURLArray[1].split('&');
					for (var i = 0 in params) {
						if (params[i] !== newParam) {
							urlParts.push(params[i]);
						}
					}
				}

				urlParts.push(newParam);

				var newURL = '';
				for (var i = 0 in urlParts) {
					if (i === '0') {
						newURL += urlParts[i];
						continue;
					}

					newURL += ((i === '1') ? '?' : '&') + urlParts[i];
				}

				window.history.pushState({path: newURL}, '', newURL);
			}
		};

		/**
		 *
		 * @param {String} name
		 * @returns {String}
		 */
		this.removeParameter = function (name)
		{
			var url = window.location.href.split('?')[0] + '?';
			var sPageURL = decodeURIComponent(window.location.search.substring(1)),
					sURLVariables = sPageURL.split('&'),
					sParameterName,
					i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] !== name) {
					url = url + sParameterName[0] + '=' + sParameterName[1] + '&';
				}
			}
			return url.substring(0, url.length - 1);
		};


		// Modify GET param for search
		var searchField = this.find('[type=search]');

		if (searchField.val()) {
			// set search param
			this.appendURLParameter(searchField.prop('name'), searchField.val(), this.removeParameter('cHash'));
		}
	};

	$('.js-abavo-search-form').abavoSearchForm();
});
