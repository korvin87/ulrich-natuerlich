/* 
 * abavo_search
 * 
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
 
$(function () {
	// Hit-Counter
	$('.tx-abavo-search .result a').on('click', function (e) {
        var $this = $(this),
            url = $this.attr('href'),
            uid = $this.closest('.result').data('uid'),
            target = $this.attr('target');

		if (uid === parseInt(uid, 10)) {
            // TODO: SearchService results excluded from stats; Find a way to refer it;
			$.ajax({
				type: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					type: '170411',
					'tx_abavosearch_pidata': {
						'controller': 'Ajax',
						'action': 'hitindex',
						'uid': uid
					}
				}
			});
		}

		// Redirect
		if (typeof target === 'undefined' || target.toLowerCase() === '_self') {
			e.preventDefault();
			setTimeout(function () {
				location.href = url;
			}, 300);
		}
	});
});
