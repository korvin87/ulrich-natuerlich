/**
 * ulrich_products - ProductList.js
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 25.05.2018 - 13:00:32
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

var $infinityLoadContent = $('#js-infinity_load_content');
var $infinityLoadItemTemplate = $('#js-infinity_load_item_template');
var $infinityLoadTrigger = $('#js-infinity_load_trigger');
var $infinityLoadFilterTrigger = $('.js-infinity_load_filter-trigger');


/**
 * Render items method
 *
 * @param {obj} responseData The response JSON
 * @return {void}
 */
function renderItems(responseData) {
	try {
		// Render items
		responseData.items.forEach(function (item) {
			$infinityLoadContent.append('<li role="menuitem" class="item" id="item-' + item.uid + '"><a href="' + item.uri + '">' + item.title + '</a></li>');
		});

		// Toggle load_more link
		$infinityLoadTrigger.toggle(responseData.showMoreLink);

	} catch (error) {
		abavoLbmessage({text: 'ProductList.js->renderItems: ' + error.message});
	}
}

/**
 * Get Items method
 *
 * @return {void}
 */
function getItems(resetItems) {

	try {
		// Reset item list?
		if (resetItems === true) {
			$infinityLoadContent.html('');
			$infinityLoadContent.data('offset', 0);
			$infinityLoadTrigger.show();
		}

		// Do request?
		if (Boolean($infinityLoadTrigger.data('requestfinished')) === true && $infinityLoadTrigger.is(':visible')) {
			$infinityLoadTrigger.data('requestfinished', false);

			// Create requestParams
			var requestQueryParams = $infinityLoadContent.data();
			var requestParams = {'tx_ulrichproducts_api[query]': requestQueryParams, '_ts': Date.now()};

			// Requesting JSON
			$.getJSON($infinityLoadTrigger.data('resource'), requestParams).done(function (response) {

				// Render items
				if (response.data) {
					renderItems(response.data);
				}

				// Update trigger/content data
				$infinityLoadContent.data('offset', $infinityLoadContent.data('offset') + $infinityLoadContent.data('limit'));
				$infinityLoadTrigger.data('requestfinished', true);

				// Show message?
				if (response.data.message) {
					abavoLbmessage({text: data.message});
				}
			}).fail(function () {
				abavoLbmessage({text: 'Error in AJAX request: ' + this.url});
			});
		}

	} catch (error) {
		abavoLbmessage({text: 'ProductList.js->getItem: ' + error.message});
	}
}

/**
 * Document ready
 */
$(document).ready(function () {

	if ($infinityLoadContent) {
		getItems();

		// Link action: load_more
		$infinityLoadTrigger.click(function (event) {
			event.preventDefault();
			getItems();
		});

		// Filter action: load with filter
		$infinityLoadFilterTrigger.click(function (event) {
			event.preventDefault();
			$infinityLoadContent.data('char', $(this).data('char'));
			$infinityLoadFilterTrigger.removeClass('active');
			$(this).addClass('active');
			getItems(true);
		});

		// Autoload: If $infinityLoadTrigger is in viewport
		$(window).on('resize scroll', function () {

			if ($infinityLoadTrigger.inView() === true) {
				getItems();
			}
		});
	}
});
