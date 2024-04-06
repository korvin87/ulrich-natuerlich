/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

function updateMarkerLatLnl(ukey, lon, lat, repo) {

	$.ajax({
		async: 'true',
		url: '/index.php',
		type: 'POST',
		data: {
			eID: 'abavo_maps_ajaxDispatcher',
			request: {
				pluginName: 'Pimain',
				controller: 'Marker',
				action: 'update',
				arguments: {
					'ukey': ukey,
					'lat': lat,
					'lng': lon,
					'repo': repo,
				},
				dataType: 'html',
			}
		},
		dataType: 'html',
		statusCode: {
			200: function () {
				console.log('marker ' + ukey + ' update successed.');
			}
		},
		error: function (error) {
			console.log('marker ' + ukey + ' update failed' + error);
		}
	});

}