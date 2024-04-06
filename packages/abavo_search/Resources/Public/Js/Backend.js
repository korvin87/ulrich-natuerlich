;
(function ($) {


// Index Processing Request
function getIndexingProgressState(url, doNotReload) {
	var indexingProgressState = setInterval(function () {

		$.ajax({
			url: url,
			context: document.body
		}).done(function (data) {

			if (!$.isEmptyObject(data)) {
				if (!data.active) {
					clearInterval(indexingProgressState);
					if (!doNotReload) {
						window.location.reload(1);
					}
				}

			} else {
				clearInterval(indexingProgressState);
				alert('Error in AJAX Request: ' + url);
			}
		});
	}, 2000);
}

// Timestamp to human readable format
function ts2DateTime(unixtime) {
	var newDate = new Date();
	newDate.setTime(unixtime * 1000);
	return newDate.toUTCString();
}



$(function ($) {

	$(".tx-abavo-module .control #updateindex").click(function (event) {
		event.preventDefault();
		// Hide Button
		$this = $(this);
		$this.hide();
		// Init indexing
		$.ajax({
			url: $this.attr('href'),
			context: document.body
		}).done(function (data) {
			$("body").html(data);
		});
		// Call refresh method
		getIndexingProgressState($this.data('refreshurl'), true);
		$(".tx-abavo-module .control #animation").addClass('active');
	});

	// DataTables
	if ($("#statstable").length) {
		require(['datatables'], function () {
			$('#statstable').DataTable({
				"processing": true,
				"serverSide": true,
				"autoWidth": true,
				"ajax": {
					'url': $('#statstable').data('dataurl') + '&_timestamp=' + Date.now(),
					'cache': false
				},
				"pageLength": 10,
				"columns": [
					{"data": "sys_language_uid"},
					{"data": "type"},
					{"data": "val"},
					{"data": "refid"},
					{"data": "hits"},
					{"data": "tstamp"}
				],
				"order": [[0, "asc"], [4, "desc"]],
				"columnDefs": [
					{"width": "10%", "targets": 0},
					{"width": "10%", "targets": 1},
					{"width": "10%", "targets": 3},
					{"width": "10%", "targets": 4},
					{"width": "25%", "targets": 5},
					{"sClass": "dt-right", "aTargets": [4]},
					{"sClass": "dt-right", "aTargets": [5]}
					/*
					 {
					 "targets": [4],
					 "visible": false
					 }
					 */
				],
				initComplete: function () {
					// Lanuage selection
					$('#column0_search').on('change', function () {
						$('#statstable').DataTable().column(0).search(this.value).draw();
					});
					// Type selection
					$('#column1_search').on('change', function () {
						$('#statstable').DataTable().column(1).search(this.value).draw();
					});
				},
				drawCallback: function (settings) {
					var api = this.api();

					api.rows({page: 'current'}).data().each(function (stat, rowIndex) {

						// DateTime Format
						api.cell(rowIndex, 5).data('<span class="hidden">' + stat.tstamp + '</span>' + ts2DateTime(stat.tstamp));

						// Language Description
						api.cell(rowIndex, 0).data(LANGUAGES[stat.sys_language_uid].title);
					})
				}
			});
		});
	}
});


})(TYPO3.jQuery || jQuery);