$(function() {
	var minChars = 2;

	function searchAlternativeWords(searchString, header) {

		if (!(searchString === '' || searchString.length < minChars)) {

			// Remove all special chars
			searchString.trim().replace(/[^a-z0-9äöüß]+/gi, '');

			$('#searchResultArea').show();

			$.ajax({
				type: 'GET',
				url: 'https://www.openthesaurus.de/synonyme/search',
				dataType: 'jsonp',
				data: {
					q: searchString,
					similar: true,
					format: "application/json"
				},
				success: function (data) {

					var items = [];
					if (typeof data['similarterms'] !== 'undefined') {

						$.each(data.similarterms, function (key, val) {
							var term = val.term.trim().replace(/[^a-zäöüß ]+/gi, ''); // Remove all special chars and numbers
							if (term != '' && (term.length > 2)) {
								items.push("<li id='alt-word-" + key + "' class='alt-word' href='' data-val='" + term + "'>" + term + "</li>");
							}
						});

						$('#searchResultArea').html('<h3>' + header + '</h3>');

						$("<ul/>", {
							"class": "word-list",
							html: items.join("")
						}).appendTo("#searchResultArea");

						$('#search-results .licence').show();

						$('#searchResultArea .alt-word').on('click', function () {
							$('.tx-abavo-search .search-word').val($(this).data('val'));
						});
					}else{
						$('#searchResultArea').html('');
					}
				},
				fail: function () {
					$('#searchResultArea').html('');
					console.log('GET https://www.openthesaurus.de/synonyme/search failed.');
				},
				statusCode: {
					404: function () {
						$('#searchResultArea').html('');
						console.log('STATUS 404 https://www.openthesaurus.de/synonyme/search.');
					}
				}
			});
		}
	}

	// Call Request
	searchAlternativeWords($('#searchResultArea').data('search'), $('#searchResultArea').data('title'));
});