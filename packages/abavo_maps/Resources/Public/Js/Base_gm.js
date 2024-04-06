/*
 * abavo_maps
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
var arrMapConfig = new Array();
var arrMarkers = new Array();
var arrMarkerCluster = new Array();
var arrBounds = new Array();
var infowindow = new google.maps.InfoWindow();
var arrInitMapCallbacks = new Array();

// INITIALIZE MAP ADDITIONAL
function initMap(current_map, mapOptions, gmuid) {
	arrBounds[gmuid] = new google.maps.LatLngBounds();

	// Infowindow
	google.maps.event.addListener(current_map, 'click', function () {
		infowindow.close();
	});

	// MARKER
	var arrMakersKeyToCount = new Array();
	var counterMarker = counterMarkerToGeocode = 0;
	var arrMakerWithoutLatLng = new Array();

	arrMakerWithoutLatLng[gmuid] = new Array();
	for (var mkey in arrMarkers[gmuid]) {
		arrMakersKeyToCount[counterMarker] = mkey;
		counterMarker++;
		// Get markers without latlng for delay handling
		if (arrMarkers[gmuid][mkey]['latLng'] == '(0, 0)') {
			arrMakerWithoutLatLng[gmuid][mkey] = mkey;
			counterMarkerToGeocode++;
		}
	}

	// LOG
	var logMessage = 'map:' + gmuid + ' | marker count:' + counterMarker;
	if (typeof arrMakerWithoutLatLng[gmuid] !== "undefined") {
		logMessage += ' | marker count need to geocode:' + counterMarkerToGeocode;
	}
	//console.log(logMessage);


	// SLOWDOWN METHODE BECAUSE OF GOOGLE^^
	// https://developers.google.com/maps/documentation/elevation/
	// https://developers.google.com/maps/documentation/business/articles/usage_limits#problems
	var i = 0, runCount = 1, run;
	var runEachMarker = function () {

		var mkey = [arrMakersKeyToCount[i]];
		codeAddress(arrMarkers[gmuid][mkey]['location'], current_map, arrMarkers[gmuid][mkey]['title'], mkey, gmuid, arrMarkers[gmuid][mkey]['latLng']);
		//console.log('run:'+i);

		// DELAY if marker has no latlng
		if (typeof arrMakerWithoutLatLng[gmuid][mkey] !== "undefined") {
			var delayRow = 5, delayBlock = 1000;
		} else {
			var delayRow = 0, delayBlock = 0;
		}

		// LOOP EACH MARKER
		if ((i++) < arrMakersKeyToCount.length - 1) {
			// Make 12 blocks
			if (runCount++ % 12 == 0) {
				delay = delayBlock;
				//console.log('new marker-block');
			} else {
				delay = delayRow;
			}
			// CALL SELF
			setTimeout(runEachMarker, delay);

		} else {
			// FIT ZOOM TO ALL MARKERS IF MORE THEN 1
			if (arrMapConfig[gmuid]['markerCount'] > 1) {
				current_map.fitBounds(arrBounds[gmuid]);
				google.maps.event.addListenerOnce(current_map, 'tilesloaded', function () {
					//this part runs when the mapobject is created and rendered
					//google.maps.event.addListenerOnce(current_map, 'tilesloaded', function(){
					//this part runs when the mapobject shown for the first time
					current_map.fitBounds(arrBounds[gmuid]);
					//});
				});
			} else {
				// SET MAP CENTER TO POSITION OF THE MARKER (markercount=1)
				if (typeof arrMarkers[gmuid][mkey]['marker'] !== "undefined") {
					current_map.setCenter(arrMarkers[gmuid][mkey]['marker'].getPosition());
				} else {
					// setting zoom manual, if marker isn´t geocode yet.
					current_map.fitBounds(arrBounds[gmuid]);
					google.maps.event.addListenerOnce(current_map, 'tilesloaded', function () {
						current_map.fitBounds(arrBounds[gmuid]);
						current_map.setZoom(arrMapConfig[gmuid]['zoom']);
					});
				}
			}

			// INIT CLUSTERING
			initClustering(gmuid, current_map);
		}

	}
	runEachMarker();

	//Callbacks
	if (typeof arrInitMapCallbacks[gmuid] !== 'undefined' && arrInitMapCallbacks[gmuid].length > 0) {
        arrInitMapCallbacks[gmuid].forEach(function(item) {
            if (typeof window[item] === 'function') {
                window[item](gmuid, current_map);
            }
        });
	}
}


// GET LAT/LONG FROM ADRESS STRING 
function codeAddress(address, current_map, title, mkey, gmuid, latlnt) {

	// USE LATLNT ONLY IF GIVEN
	if (latlnt == '(0, 0)') {

		geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': address}, function (results, status) {

			if (status == google.maps.GeocoderStatus.OK) {
				makeMarker(results[0].geometry.location, current_map, title, mkey, gmuid);
				updateMarkerLatLnl(arrMarkers[gmuid][mkey]['ukey'], results[0].geometry.location.lng(), results[0].geometry.location.lat(), arrMarkers[gmuid][mkey]['repo']);

			} else if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
				window.setTimeout(function () {
					codeAddress(address, current_map, title, mkey, gmuid, latlnt);
					console.log('OVER_QUERY_LIMIT: ' + mkey);
				}, 1000);

			} else {
				console.log('Geocode was not successful for the following reason: ' + status);
			}

		});

	} else {
		makeMarker(latlnt, current_map, title, mkey, gmuid);
	}
}


// GENERATE MARKER
function makeMarker(latlng, current_map, title, mkey, gmuid) {

	dynPosition = latlng;
	if (arrMarkers[gmuid][mkey]['pinicon'] != '') {
		var iconFile = abavogmapurlPrefix + arrMarkers[gmuid][mkey]['pinicon'];
	}

	arrMarkers[gmuid][mkey]['marker'] = new google.maps.Marker({
		map: current_map,
		//animation: google.maps.Animation.DROP,
		position: dynPosition,
		title: title,
		icon: iconFile
	});

	// FILL BOUNDS ARRAY WITH POSITIONS 
	arrBounds[gmuid].extend(arrMarkers[gmuid][mkey]['marker'].getPosition());

	// ADD LISTENERS
	google.maps.event.addListener(arrMarkers[gmuid][mkey]['marker'], 'click', function (e) {
		var clickFunction = arrMarkers[gmuid][mkey]['clickFunction'];

		if (typeof window[clickFunction] === 'function') {
            window[clickFunction](e, latlng, current_map, title, mkey, gmuid);

		} else {
            if (arrMarkers[gmuid][mkey]['url'] == '') {
                // open info bubble
                arrMarkers[gmuid][mkey]['infowindow_map_' + gmuid].open(current_map, arrMarkers[gmuid][mkey]['marker']);
                //to open markerinfobubble on load use: arrMarkers[mkey]['infowindow_map_'+gmuid].open(current_map, marker[mkey]);
            } else {
                // link to url
                document.getElementById('tx-abavo-maps-' + gmuid + '-links').innerHTML = (arrMarkers[gmuid][mkey]['url']);
                document.getElementById('tx-abavo-maps-marker-' + mkey + '-link').click();
            }

            // add autocomplete
            if (document.getElementById('start_' + gmuid + '_' + mkey)) {
                setTimeout(function () {
                    var autocomplete = new google.maps.places.Autocomplete(document.getElementById('start_' + gmuid + '_' + mkey));
                    autocomplete.bindTo('bounds', current_map);
                }, 100);
            }
		}
	});

}

/*
 * CALCULATE ROUTES method
 * 
 * @param {mixed} start
 * @param {mixed} end
 * @param {int} gmuid
 * @return {void}
 */
function calcRoute(start, end, gmuid) {
	var request = {
		origin: start,
		destination: end,
		travelMode: google.maps.TravelMode.DRIVING
	};

	// Direction
	window['directionsDisplay_' + gmuid] = new google.maps.DirectionsRenderer();
	window['directionsDisplay_' + gmuid].setMap(window['map_' + gmuid]);
	window['directionsDisplay_' + gmuid].setPanel(document.getElementById('abavo-maps-' + gmuid + '-directions-panel'));

	window['directionsService_' + gmuid] = new google.maps.DirectionsService();

	window['directionsService_' + gmuid].route(request, function (response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			window['directionsDisplay_' + gmuid].setDirections(response);
		}
	});
}

// INFOWINDOW
function addInfowindow(obj, mapobj) {
	google.maps.event.addListener(obj, 'click', function (event) {
		var contentString = '<div class="map-info-window"><b>' + this.name + "</b><br>" + this.description + '</div>';
		infowindow.setContent(contentString);
		infowindow.setPosition(event.latLng);
		infowindow.open(mapobj);
	});
}


/*
 * Add a circle to a map
 */
function addCircle2Map(mapobj, lat, lng, radius, color, setCenterToCircle, id, name, description) {

	id = (!id) ? new Date().getTime() : id;
	color = (!color) ? '#7092FF' : color;

	window['cityCircle_' + id] = new google.maps.Circle({
		name: name,
		description: description,
		strokeColor: color,
		strokeOpacity: 0.8,
		strokeWeight: 1,
		fillColor: color,
		fillOpacity: 0.35,
		map: mapobj,
		center: new google.maps.LatLng(lat, lng),
		radius: radius //Meter
	});

	if (setCenterToCircle === true) {
		mapobj.setCenter(window['cityCircle_' + id].center);
	}

	if (name != '') {
		addInfowindow(window['cityCircle_' + id], mapobj);
	}
}

/*
 * Add a Rectangle to a map
 */
function addRectangle2Map(mapobj, latNE, lngNE, latSW, lngSW, color, setCenterToCircle, id, name, description) {

	id = (!id) ? new Date().getTime() : id;
	color = (!color) ? '#7092FF' : color;

	window['cityRectangle_' + id] = new google.maps.Rectangle({
		name: name,
		description: description,
		strokeColor: color,
		strokeOpacity: 0.8,
		strokeWeight: 1,
		fillColor: color,
		fillOpacity: 0.35,
		map: mapobj,
		bounds: new google.maps.LatLngBounds(
				new google.maps.LatLng(latSW, lngSW),
				new google.maps.LatLng(latNE, lngNE)
				)
	});

	if (setCenterToCircle === true) {
		mapobj.setCenter(window['cityRectangle_' + id].center);
	}

	if (name != '') {
		addInfowindow(window['cityRectangle_' + id], mapobj);
	}
}

/*
 * Add a Polygon to a map
 */
function addPolygon2Map(mapobj, bounds, color, name, description) {

	var id = new Date().getTime();

	var path = [];
	bounds.forEach(function (latlng, i) {
		var tempLatlng = latlng.split(',');
		path.push(new google.maps.LatLng(tempLatlng[0], tempLatlng[1]));
	});

	window['cityPolygon_' + id] = new google.maps.Polygon({
		name: name,
		description: description,
		path: path,
		map: mapobj,
		strokeColor: color,
		strokeOpacity: 0.8,
		strokeWeight: 1,
		fillColor: color,
		fillOpacity: 0.35,
	});

	if (name != '') {
		addInfowindow(window['cityPolygon_' + id], mapobj);
	}
}

function initClustering(gmuid, mapobj) {

	// CLUSTERING
	arrMarkerCluster[gmuid] = new MarkerClusterer(
			mapobj, [], {imagePath: clusterImagesPath}
	);

	google.maps.event.addListenerOnce(mapobj, 'center_changed', function () {
		setTimeout(function () {
			$.each(arrMarkers[gmuid], function (i, item) {
				if (typeof arrMarkers[gmuid][i] !== 'undefined' && arrMarkers[gmuid][i] !== null) {
					arrMarkerCluster[gmuid].addMarker(item.marker);
				}
			});
		}, 200);
	});
}

/*
 * Simple Geocoding to dom node data attribute
 */
function geoCodeAdress2NodeDataAttrib(node) {

	if (node != '') {
		$this = $(node);
		geocoder = new google.maps.Geocoder();
		geocoder.geocode({'address': $this.val()}, function (results, status) {

			$this.attr('data-geocoderstatus', status);

			if (status == google.maps.GeocoderStatus.OK && status != 'ZERO_RESULTS') {
				$this.attr('data-latitude', results[0].geometry.location.lat());
				$this.attr('data-longitude', results[0].geometry.location.lng());
			} else {
				console.log('Geocode was not successful for the following reason: ' + status);
				$this.attr('data-latitude', '');
				$this.attr('data-longitude', '');
			}

		});
	}
}


/*
 * SWITCH BETWEEN INFOBUBBLES Method
 * 
 * @deprecated
 */
function switchForm(state, current_map, mkey, gmuid) {
	arrMarkers[gmuid][mkey]['infowindow_map_' + gmuid + '_0'].close();
	arrMarkers[gmuid][mkey]['infowindow_map_' + gmuid + '_1'].close();
	arrMarkers[gmuid][mkey]['infowindow_map_' + gmuid + '_2'].close();

	if (state == 'to') {
		arrMarkers[gmuid][mkey]['infowindow_map_' + gmuid + '_1'].open(current_map, arrMarkers[gmuid][mkey]['marker']);
	} else {
		arrMarkers[gmuid][mkey]['infowindow_map_' + gmuid + '_2'].open(current_map, arrMarkers[gmuid][mkey]['marker']);
	}

}
