/* 
 * abavo_form
 * 
 * @copyright   2016 abavo GmbH <dev(at)abavo.de>
 * @license     Proprietary
 */

$(function () {
	var $document = $(document);

	/**
	 * Refresh form trigger
	 */
	$document.on('change', '.js-trigger-refresh-form', function () {
		try {
			var $form = $(this).parents('form');
			$form.attr('action', $form.data('refreshaction')).submit();

		} catch (err) {
			console.log(err.message);
		}
	});

	/**
	 * AJAX Submit
	 */
	$document.on('submit', '.tx-abavo-form form', function (e) {
		try {
			var $form = $(this),
					$wrapper = $form.parents('.tx-abavo-form'),
					cid = $form.parents('.ce-element').attr('id');

			if ($form.data('ajaxsubmit')) {
				var formData = $form.serializeArray();

				e.preventDefault();

				if (typeof $ajaxloader !== 'undefined') {
					$ajaxloader.show();
				}

				$.each($('#' + cid + ' .tx-abavo-form form button'), function (key, item) {
					formData.push({name: item.name, value: item.value});
				});

				$.ajax({
					url: $form.data('ajaxsubmiturl') + '&type=' + $form.data('pagetype'),
					type: 'POST',
					data: formData,
					cache: false

				}).done(function (result, textStatus, request) {

					var forwardUrl = request.getResponseHeader('AbavoForm_RedirectUrl')
					if (forwardUrl !== null) {
						window.location.href = forwardUrl;
					} else {
						$wrapper.html(result).trigger('submitFinished');
					}

					if (typeof $ajaxloader !== 'undefined') {
						$ajaxloader.hide();
					}

				}).fail(function (e) {
					var message = 'Error in AJAX-Request';

					$wrapper.trigger('submitFinished');

					if (e.responseText) {
						message = e.responseText;
					}

					if (e.message) {
						message = e.message;
					}

					if (typeof $ajaxloader !== 'undefined') {
						$ajaxloader.hide();
					}

					if (typeof abavoLbmessage !== 'undefined') {
						abavoLbmessage({
							text: message,
							type: 'alert'
						});
					}
				});

			}
		} catch (err) {
			console.log(err.message);
		}
	});

	/**
	 * AJAX Validation
	 */
	$document.on('change', '.js-ajax-validation', function () {

		try {
			var $input = $(this),
					$form = $input.parents('.tx-abavo-form').find('form'),
					validationUrl = $form.data('validationurl'),
					pluginParam = $form.data('pluginparam'),
					pluginParamValidateSuffix = 'ajaxvalidate',
					data = {},
					$row = $input.parents('.row'),
					fieldName = $input.attr('name');

			data[pluginParam + pluginParamValidateSuffix] = {
				field: fieldName.replace(pluginParam, pluginParam + pluginParamValidateSuffix),
				value: $input.val()
			};

			$row.removeClass('ajax-validation-success ajax-validation-error').addClass('ajax-validation-loading');

			$.ajax({
				url: validationUrl + '&type=815',
				type: 'POST',
				data: data,
				cache: false

			}).done(function (data) {
				// clean state
				$input.removeClass('f3-form-ok f3-form-error');
				$row.find('.error-block').remove();

				// check serverside validation
				if (!data.isValid && data.messages) {
					var items = [];

					$.each(data.messages, function (key, val) {
						items.push('<div class="entry" id="' + key + '">' + val + '</div>');
					});

					$('<div />', {
						'class': 'error-block',
						html: items.join('')
					}).insertAfter($input);

					$input.addClass('f3-form-error');
					$row.addClass('ajax-validation-error');
				}

				if (data.isValid) {
					$input.addClass('f3-form-ok');
					$row.addClass('ajax-validation-success');

					/*
					 * "background"-submit
					 */
					if ($input.data('backgroundsubmitonchange')) {
						var formData = $form.serializeArray();

						$.ajax({
							url: $form.data('ajaxsubmiturl') + '&' + pluginParam + '[submitMode]=' + 'background' + '&type=' + $form.data('pagetype'),
							type: 'POST',
							data: formData,
							cache: false,

							error: function (e) {
								var message = 'Error in AJAX-Request';
								if (e.message) {
									message = e.message;
								}
								console.log(message);
							}
						});
					}
				}

			}).fail(function (e) {
				var message = 'Error in AJAX-Request';
				if (e.message) {
					message = e.message;
				}
				console.log(message);

			}).always(function () {
				$row.removeClass('ajax-validation-loading');

			});

		} catch (err) {
			console.log(err.message);
		}
	});


	/*
	 * Prevent browser history back
	 * Based on https://stackoverflow.com/questions/6359327/detect-back-button-click-in-browser#answer-18382442
	 */
	if ($('.tx-abavo-form [data-preventbrowserbackurl]').length) {
		if (window.history && window.history.pushState) {

			$(window).on('popstate', function () {
				var hashLocation = location.hash;
				var hashSplit = hashLocation.split("#!/");
				var hashName = hashSplit[1];

				if (hashName !== '') {
					var hash = window.location.hash;
					if (hash === '') {
						window.location.href = $('.tx-abavo-form [data-preventbrowserbackurl]').data('preventbrowserbackurl');
					}
				}
			});

			window.history.pushState('forward', null, null);
		}
	}

	// Remove all GET-Params on preventbrowserback
	if (location.search.indexOf('preventbrowserback=1') >= 0) {
		url = window.location.href.split('?')[0];
		window.history.pushState('{}', '', url);
	}

});
