/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

// DEFAULTS
var COUNTRY_CODES = '';
var QUERY_LIMIT = 0;


// Loading animation
function osmLoadAnimation(mapuid, action){

	switch(action){
	case 1:
		var styleTo = 'block';
		break;
	case 0:
		var styleTo = 'none';
		break;
	default:
		console.log('osmLoadAnimation: no action defined');
	}
	document.getElementById( 'tx-abavo-maps-' + mapuid + '-loadingbox' ).style.display = styleTo;
}

// SWITCH BETWEEN INFOBUBBLES
function switchOsmForm( showDiv, hideDiv){
	document.getElementById(hideDiv).style.display = 'none';
	document.getElementById(showDiv).style.display = 'block';
}

// GENERATE OSM MARKER
function makeMarker(map, lon, lat, iconSource, mapUid, markerUid) {
	
	// Only place markers with real UIDs (Not dummy objects with "0"-Key!)
	if (markerUid > 0){
		markerIcon = new LeafIcon({iconUrl: iconSource});
		markerContent = '<b>' + arrOsmMarker[mapUid]['title'][markerUid] + '</b>' + arrOsmMarker[mapUid]['description'][markerUid];

		// Insert Navigation form
		markerContent += (( ROUTING == 1) ? makeOsmNavForm( 'marker_'+markerUid, lon, lat, mapUid, arrOsmMarker[mapUid]['title'][markerUid] ) : '');

		// Make marker
		var marker = L.marker([lat,lon], {icon: markerIcon}).addTo(window['osm_leaflet_map_' + mapuid]);

		// if no url Add Popup to map, else: link to url
		if (arrOsmMarker[mapUid]['url'][markerUid] == ''){
			marker.bindPopup( markerContent );
		}else{
			marker.on("click",function(){
				document.getElementById( 'tx-abavo-maps-' + mapUid + '-links' ).innerHTML = (arrOsmMarker[mapUid]['url'][markerUid]);
				document.getElementById( 'tx-abavo-maps-marker-' + markerUid + '-link' ).click();
			});
		}
	}
	
	// Push boundaries to array tor center later
	window['totalBounds_osm_map_' + mapUid].push([lat,lon]);
}

// MAKE MARKER ASYNC
function makeMarkerAsync(mapKey, markerKey, iconSource) {

	window['promise'+markerKey] = $.getJSON( "http://nominatim.openstreetmap.org/search?q="+arrOsmMarker[mapKey]['querys'][markerKey]+"&format=json", function( data ) {

		if (typeof data[0] !== 'undefined' && data[0] !== null) {
			makeMarker(window['osm_map_' + mapKey], data[0]['lon'], data[0]['lat'], iconSource, mapKey, markerKey);
			updateMarkerLatLnl(arrOsmMarker[mapKey]['ukey'][markerKey], data[0]['lon'], data[0]['lat'], arrOsmMarker[mapKey]['repo'][markerKey]);
		}else{
			var message = arrOsmMarker[mapKey]['title'][markerKey] + ' QUERY [' + arrOsmMarker[mapKey]['querys'][markerKey] + ']';

			$('#tx-abavo-maps-'+mapKey+'-messagebox .tx-abavo-maps-messages-header').html('Error(s):');
			$('#tx-abavo-maps-'+mapKey+'-messagebox .tx-abavo-maps-messages-body').append( '<li>' + labelGecodingfailed +': <a href="http://nominatim.openstreetmap.org/search.php?q=' + arrOsmMarker[mapKey]['querys'][markerKey] + '" target="_blank">'+ message + '</a></li>' );
			$('#tx-abavo-maps-'+mapKey+'-messagebox').show();
			console.log( labelGecodingfailed +':' + message );
		}
	});
}


// CENTER MAP AND ZOOM TO ALL BOUNDS
function centerMapToBounds(mapUid){

	window['osm_leaflet_map_' + mapUid].fitBounds(window['totalBounds_osm_map_' + mapUid]);
	if ( window['markerCount_osm_map_' + mapUid] == 1 ){
		window['osm_leaflet_map_' + mapUid].setZoom(window['zoom_osm_map_' + mapUid]);
	}
	osmLoadAnimation(mapUid, 0);
}

/*
 * Add a circle to a map
 */
function addCircle2Map(mapobj, lat, lng, radius, color, setCenterToCircle){
	
	color = (!color) ? '#7092FF' : color;
   
	L.circle([lat, lng], radius, {
		weight: 2,
		color: color,
		opacity: 0.8,
		fillColor: color,
		fillOpacity: 0.35
	}).addTo(mapobj);
	
	if (setCenterToCircle===true){
		// zoom the map to the rectangle bounds
		mapobj.fitBounds(bounds);
	}
}

/*
 * Add a Rectangle to a map
 */
function addRectangle2Map(mapobj, latNE, lngNE, latSW, lngSW, color, setCenterToCircle){
	
	color = (!color) ? '#7092FF' : color;
	
	// define rectangle geographical bounds
	var bounds = [[latNE, lngNE], [latSW, lngSW]];

	// create an orange rectangle
	L.rectangle(bounds, {
		weight: 2,
		color: color,
		opacity: 0.8,
		fillColor: color,
		fillOpacity: 0.35
	}).addTo(mapobj);

	if (setCenterToCircle===true){
		// zoom the map to the rectangle bounds
		mapobj.fitBounds(bounds);
	}
}

/*
 * Add a Polygon to a OSM map
 */
function addPolygon2Map(mapobj, bounds, color, setCenterToCircle){
	
	var path = [];
	bounds.forEach(function(latlng, i) {
		var tempLatlng = latlng.split(',');
		path.push( L.latLng(tempLatlng[0],tempLatlng[1]) );
	});
	
	L.polygon(path, {
		weight: 2,
		color: color,
		opacity: 0.8,
		fillColor: color,
		fillOpacity: 0.35
	}).addTo(mapobj);

	// zoom the map to the polygon
	if (setCenterToCircle===true){
		mapobj.fitBounds(bounds);
	}
}

/*
 * Simple Geocoding to dom node data attribute
 */
function geoCodeAdress2NodeDataAttrib(node){

	if (node!=''){
		$this = $(node);
		
		// Clear early results
		$('.osm-nominatim-response').remove();
		
		// Get lat/lon
		//$.getJSON( "http://nominatim.openstreetmap.org/search?q="+$this.val()+"&format=json", function( responseData ) { TOTO: Refactoring: change other usages nominatim.openstreetmap.org to open.mapquestapi.com
		// Documentation http://open.mapquestapi.com/nominatim/
		$.getJSON( "https://open.mapquestapi.com/nominatim/v1/search.php?format=json"
				+ ((QUERY_LIMIT > 0) ? "&limit=" + QUERY_LIMIT : "") 
				+ "&countrycodes=" + COUNTRY_CODES 
				+ "&key=" + MAPQUESTAPI_KEY 
				+ "&q="+encodeURIComponent($this.val()), function( responseData ) {
			
			if (typeof responseData[0] !== 'undefined' && responseData[0] !== null) {
				$this.attr('data-geocoderstatus', 'OK');
				$this.attr('data-latitude', responseData[0]['lat']);
				$this.attr('data-longitude', responseData[0]['lon']);
				$this.attr('data-displayname', responseData[0]['display_name']);

				// Return result-list
				var bounds = '';
				responseData.forEach(function(place, i) {
					if (i >= 0	){
						bounds +='<li data-lat="'+place['lat']+'" data-lon="'+place['lon']+'" title="'+place['display_name']+'">'+place['display_name']+'</li>';
					}
				});
				if (bounds.length > 0){
					$this.after('<ul class="osm-nominatim-response">'+bounds+'</ul>');

					$('.osm-nominatim-response li').on("click",function(){
						$this.attr('data-latitude', $(this).data('lat'));
						$this.attr('data-longitude', $(this).data('lon'));
						$this.attr('data-displayname', $(this).data('title'));
						$this.val($(this).attr('title'));
						$('.osm-nominatim-response').remove();
					});
					$('.osm-nominatim-response').on("mouseleave",function(e){
						$(this).remove();
					});
				}
				
			} else {
				console.log('Geocode was not successful.');
				$this.attr('data-geocoderstatus', 'ZERO_RESULTS');
				$this.attr('data-latitude', '');
				$this.attr('data-longitude', '');
				$this.attr('data-displayname', '');
			}
		});

	}
}


/*
 *  ROUTING
 */

// MAKE NAVIGATION FORMS
function makeOsmNavForm(id, lon, lat, mapuid, title){

	var navForm = '<div id="' + id + '" class="tx_abavo_osm_FormWrap"><br/>' + labelRouting + ': <a href="javascript:switchOsmForm(\'osmwrap_s_' + id + '\',\'osmwrap_t_' + id + '\')">' + labelTo + '<\/a> - <a href="javascript:switchOsmForm(\'osmwrap_t_' + id + '\',\'osmwrap_s_' + id + '\')">'+ labelFrom +'<\/a>';
	var form_suffix = '<input type="text" name="addr" value="" /><br><input value="'+labelGo+'" type="submit"></form></div>';

	navForm += '<div id="osmwrap_s_' + id + '">'+labelStart+':<form id="osmaddress_s_' + id + '" name="osmaddress_s" action="javascript:calcOsmRoute(\''+ lon + '\',\'' + lat + '\', \'osmaddress_s_' + id + '\', \'s\', \'' + mapuid +'\', \'' + title + '\')" method="get">' + form_suffix;
	navForm += '<div id="osmwrap_t_' + id + '" style="display:none">'+labelEnd+':<form id="osmaddress_t_' + id + '" name="osmaddress_t" action="javascript:calcOsmRoute(\''+ lon + '\',\'' + lat + '\', \'osmaddress_t_' + id + '\', \'t\', \'' + mapuid +'\', \'' + title + '\')" method="get">' + form_suffix;		
	navForm += '</div>';
	
	return navForm;
}


// CALCULATE OSM ROUTES
function calcOsmRoute( lon, lat, routeForm, routeMode, mapuid, titel ) {

	var mapobj = window['osm_leaflet_map_' + mapuid];
	var address = document.getElementById(routeForm).addr.value;
	var flat = '';
	var flon = '';
	var tlat = '';
	var tlon = '';
	
	// Start loading animation
	osmLoadAnimation(mapuid, 1);

	if (address != ''){
	
		// Get lat/lon
		$.getJSON( "http://nominatim.openstreetmap.org/search?q="+address+"&format=json", function( responseData ) {

			if (typeof responseData[0] !== 'undefined' && responseData[0] !== null) {

				switch(routeMode){
				case 's':
					flat = responseData[0]['lat'];
					flon = responseData[0]['lon'];
					tlat = lat;
					tlon = lon;
					break;
				case 't':
					flat = lat;
					flon = lon;
					tlat = responseData[0]['lat'];
					tlon = responseData[0]['lon'];
					var setTargetMarker = true;
					break;
				default:
					console.log('no routeMode defined');
				}

				// TODO: Feature direction service possible with key:http://www.mapquestapi.com/directions/
				//var routeSouce = 'http://www.yournavigation.org/api/1.0/gosmore.php&format=kml&layer=mapnik&v=motorcar&fast=1&flat=' + flat + '&flon=' + flon + '&tlat=' + tlat + '&tlon=' + tlon;
				//var routeSouce = 'http://router.project-osrm.org/viaroute?hl=de&loc=' + flat + ',' + flon + '&loc=' + tlat + ',' + tlon + '&output=gpx';
				//var dataSource = 'typo3conf/ext/abavo_maps/Resources/Public/Js/Vendor/OpenLayers/transport.php?method=GET&url=' + routeSouce;
				var dataSource = 'typo3conf/ext/abavo_maps/Resources/Public/Php/transport.php?method=GET&url=http://www.yournavigation.org/api/1.0/gosmore.php&format=kml&v=motorcar&fast=1&layer=mapnik&flat=' + flat + '&flon=' + flon + '&tlat=' + tlat + '&tlon=' + tlon
				
				$.ajax({
					url: dataSource	,
					//context: document.body
					dataType: "xml",
				  }).success(function(data) {

						var lines = $(data).find('coordinates')[0].innerHTML.split(/\n/);
						var route = [];
						
						for (var i=0; i < lines.length; i++) {

							// only push this line if it contains a non whitespace character.
							if (/\S/.test(lines[i])) {
								var arrLatlng = new Array( $.trim(lines[i]).split(',') );
								var point = new L.LatLng(arrLatlng[0][1], arrLatlng[0][0]);
								route.push(point);
							}
						}

						// create a red polyline from an arrays of LatLng points
						var polyline = L.polyline(route, {color: ROUTING_COLOR}).addTo(mapobj);

						// Stop loading animation
						osmLoadAnimation(mapuid, 0);
						
						// Place markers
						if ( setTargetMarker === true){
							var markerLon = tlon;
							var markerLat = tlat;
							var iconSource = '/typo3conf/ext/abavo_maps/Resources/Public/Icons/marker_finish.png';
						}else{
							var markerLon = flon;
							var markerLat = flat;
							var iconSource = '/typo3conf/ext/abavo_maps/Resources/Public/Icons/marker_start.png';
						}
						
						markerIcon = new LeafIcon({iconUrl: iconSource});
						L.marker([markerLat,markerLon], {icon: markerIcon}).addTo(mapobj);


						// zoom the map to the polyline
						mapobj.fitBounds(polyline.getBounds());
				  });

			}else{
				console.log( 'Routing failed: '+ address );
			}
		});
	}
}
