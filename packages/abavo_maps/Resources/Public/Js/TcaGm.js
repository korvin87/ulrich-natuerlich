/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

var map;
var geocoder;
var marker;
var circle;
var rec;
var polygon;
var curLatitude = 0;
var curLongitude = 0;
var CUR_UID = 0;
var ZOOM_FACTOR = 13;
var FIELD_NAME = '';
var FIELD_ID = '';
var FIELD_CONFIG = '';
var	ADD_MARKER = false;
var BOUNDS = '';
var SHAPE_BODY = '';
var COLOR = '0000ff';
var AUTOCOMPLETE_ENABLED = false;
var AUTOCOMPLETE_FIELD = '';
var ERROR_MSG_GEOCODE = 'Error Geocoding your address with following reason:';


/*
 * Map initialization Method
 * Show the map
 */
function initMap() {
	
	document.getElementById('map-canvas').style.display = 'block';

	if ((curLatitude > 0) && (curLongitude > 0)){
		
		var latlng = new google.maps.LatLng(curLatitude, curLongitude);
		
		map = new google.maps.Map(document.getElementById('map-canvas'), {
			zoom: ZOOM_FACTOR,
			center: latlng
		});
		
		// Place init object
		if (ADD_MARKER){ 
			addMarker(latlng);
		}

	}else{
		if (document.getElementById('map-canvas').innerHTML == ''){
			map = new google.maps.Map(document.getElementById('map-canvas'), {
				zoom: 5,
				center:new google.maps.LatLng(0,0)
			});
		}
	}

	// Place init object
	switch (SHAPE_BODY) {
		case "circle":
			addCircle(BOUNDS);
			break;
		case "rectangle":
			addRectangle(BOUNDS);
			break;
		case "polygon":
			addPolygon(BOUNDS);
			break;
	}
	
	// Autocomplete
	if (AUTOCOMPLETE_FIELD != '' && AUTOCOMPLETE_ENABLED){
		var autocomplete = new google.maps.places.Autocomplete( document.getElementById(AUTOCOMPLETE_FIELD) );
		autocomplete.bindTo('bounds', map);
	}
}


/*
 * Add a marker object to map
 */
function addMarker(latlng){
	
	marker = new google.maps.Marker({
		map: map,
		draggable:true,
		position: latlng

	});

	// Add dragable listener
	google.maps.event.addListener(marker, 'dragend', function(event){
		updateInputFields(event.latLng);
	});
}


/*
 * Add a Circle object to map
 */
function addCircle(bounds){

	if (bounds != ''){
		var bounds = JSON.parse(bounds);
		var latitude = bounds.latitude;
		var longitude = bounds.longitude;
		var radius = bounds.radius*1000;
	}else{
		var latitude = 48.0331739;
		var longitude = 10.732553100000018;
		var radius = 20000;
	}
	
	circle = new google.maps.Circle({
		//center:new google.maps.LatLng(48.0331739,10.732553100000018),
		center:new google.maps.LatLng(latitude, longitude),
		map:map,
		radius:radius,
		strokeColor:COLOR,
		strokeOpacity:0.8,
		strokeWeight:2,
		fillColor:COLOR,
		fillOpacity:0.4,
		editable: true
	});

	// Center to bounds
	map.fitBounds(circle.getBounds());
	
	google.maps.event.addListener(circle, 'radius_changed', function(event){
		var curCircleLatlng = this.getCenter();
		var arrProperty = {};
		arrProperty['latitude'] = curCircleLatlng.lat();
		arrProperty['longitude'] = curCircleLatlng.lng();
		arrProperty['radius'] = Math.round(this.getRadius()/1000);

		document.getElementById(FIELD_CONFIG).value = JSON.stringify(arrProperty);
	});
	google.maps.event.addListener(circle, 'center_changed', function(event){
		var curCircleLatlng = this.getCenter();
		var arrProperty = {};
		arrProperty['latitude'] = curCircleLatlng.lat();
		arrProperty['longitude'] = curCircleLatlng.lng();
		arrProperty['radius'] = Math.round(this.getRadius()/1000);

		document.getElementById(FIELD_CONFIG).value = JSON.stringify(arrProperty);
	});
}

/*
 * Add a Rectangle object to map
 */
function addRectangle(bounds){
	
	if (bounds != ''){
		var bounds = JSON.parse(bounds);
		var latitude_sw = bounds.latitude_sw;
		var longitude_sw = bounds.longitude_sw;
		var latitude = bounds.latitude;
		var longitude = bounds.longitude;
	}else{
		var latitude_sw = 48.0;
		var longitude_sw = 10.7;
		var latitude = 48.1;
		var longitude = 11;		
	}
		
	rec = new google.maps.Rectangle({
		strokeColor: COLOR,
		strokeOpacity: 0.8,
		strokeWeight: 1,
		fillColor: COLOR,
		fillOpacity: 0.35,
		map: map,
		editable: true,
		draggable:true,
		bounds: new google.maps.LatLngBounds(
			new google.maps.LatLng(latitude_sw,longitude_sw),
			new google.maps.LatLng(latitude,longitude)
		)
	});
	
	// Center to bounds
	map.fitBounds(rec.getBounds());
	
	google.maps.event.addListener(rec, 'bounds_changed', function(event){
		var curRec = this.getBounds();
		var arrProperty = {};
		arrProperty['latitude'] = curRec.getNorthEast().lat();
		arrProperty['longitude'] = curRec.getNorthEast().lng();
		arrProperty['latitude_sw'] = curRec.getSouthWest().lat();
		arrProperty['longitude_sw'] = curRec.getSouthWest().lng();

		document.getElementById(FIELD_CONFIG).value = JSON.stringify(arrProperty);
	});
	
	google.maps.event.addListener(rec, 'mouseout', function(event){
		var curRec = this.getBounds();
		var arrProperty = {};
		arrProperty['latitude'] = curRec.getNorthEast().lat();
		arrProperty['longitude'] = curRec.getNorthEast().lng();
		arrProperty['latitude_sw'] = curRec.getSouthWest().lat();
		arrProperty['longitude_sw'] = curRec.getSouthWest().lng();

		document.getElementById(FIELD_CONFIG).value = JSON.stringify(arrProperty);
	});
}

/*
 * Add a Polygon object to map
 */
function updatePolygon(polygon){
	var polygonBounds = polygon.getPath();
	var arrBounds = new Array;
		
	polygonBounds.forEach(function(xy, i) {
		arrBounds.push(xy.lat() + ',' + xy.lng());
	});

	document.getElementById(FIELD_CONFIG).value = JSON.stringify(arrBounds);
}

function addPolygon(bounds){

	if (bounds != ''){
		var bounds = JSON.parse(bounds);
	}else{
		var bounds = ["48.983991,10.734863", "48.395715,10.888916", "48.508742,10.120850"];
	}
	
	var latLngBounds = new google.maps.LatLngBounds();
	var path = [];
	
	bounds.forEach(function(latlng, i) {
		var tempLatlng = latlng.split(',');
		path.push( new google.maps.LatLng(tempLatlng[0],tempLatlng[1]) );
		latLngBounds.extend( new google.maps.LatLng(tempLatlng[0],tempLatlng[1]) );
	});
	
	// Center to bounds
	map.fitBounds(latLngBounds);

	polygon = new google.maps.Polygon({
		path:path,
		map:map,
		strokeColor:COLOR,
		strokeOpacity:0.8,
		strokeWeight:1,
		fillColor:COLOR,
		fillOpacity:0.35,
		editable:true,
		draggable: true,
		geodesic: true
	});

	google.maps.event.addListener(polygon, 'mouseout', function() {
		updatePolygon(polygon);
	});

	google.maps.event.addListener(polygon.getPath(), 'set_at', function() {
		updatePolygon(polygon);
	});
}


/*
 * Geocode the given adress
 */
function codeAddress() {

	var address = document.getElementById(FIELD_ID).value;
	if (address !=''){
		
		if (ADD_MARKER){
			initMap();
		}
		geocoder = new google.maps.Geocoder();

		geocoder.geocode( { 'address': address}, function(results, status) {
			
			if (status == google.maps.GeocoderStatus.OK) {
				
				// Center map to geocoded location
				map.setCenter(results[0].geometry.location);
				
				// Update map/marker
				if ((marker != '') && (typeof marker !== "undefined")) {
					// Move marker if map was loaded
					marker.setPosition( new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng()) );
				}else{
					if (ADD_MARKER){
						addMarker(results[0].geometry.location);
					}
				}

				// Update Input fields
				if (ADD_MARKER){
					updateInputFields(results[0].geometry.location);
				}
				
				// Place object
				switch (SHAPE_BODY) {
					case "circle":
						circle.setCenter(results[0].geometry.location);
						break;
					case "rectangle":
						rec.setBounds(new google.maps.LatLngBounds(
							results[0].geometry.bounds.getSouthWest(),
							results[0].geometry.bounds.getNorthEast()
						));
						break;
					case "polygon":
						polygon.moveTo( new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng()) );
						break;
				}				

			} else {
				alert(ERROR_MSG_GEOCODE+': ' + status);
			}
		});
	}
}


/* FEATURE?: DrawingManager Method
 * 
 * @returns {google.maps.drawing.DrawingManager}
 */
/*
function createDrawingManager(){

	switch (SHAPE_BODY) {
		case "circle":
			var drawingModes = new Array(google.maps.drawing.OverlayType.CIRCLE);
			break;
		case "rectangle":
			var drawingModes = new Array(google.maps.drawing.OverlayType.RECTANGLE);
			break;
		case "polygon":
			var drawingModes = new Array( google.maps.drawing.OverlayType.POLYGON );
			break;
		default:
			var drawingModes = new Array();
			break;
			
	}

	var drawingControlOptions = {
		drawingModes: drawingModes,
		position: google.maps.ControlPosition.TOP_LEFT
	};

	var polyOptions = {
		strokeColor:COLOR,
		strokeOpacity:0.8,
		strokeWeight:1,
		fillColor:COLOR,
		fillOpacity:0.35,
		editable: true,
		draggable: true
	};

	drawingManagerOptions = {
		drawingMode: null,
		drawingControlOptions: drawingControlOptions,
		//markerOptions: { draggable: true },
		//polylineOptions: { editable: true },
		rectangleOptions: polyOptions,
		circleOptions: polyOptions,
		polygonOptions: polyOptions,
		map: map
	};

	var drawingManager = new google.maps.drawing.DrawingManager(drawingManagerOptions);
	console.log(drawingModes);
	
	// Add change listeners
	switch (SHAPE_BODY) {
		case "circle":
			
			google.maps.event.addListener(drawingModes[0], 'radius_changed', function(event){
				console.log(Math.round(this.getRadius()/1000));
			});
			google.maps.event.addListener(drawingModes[0], 'center_changed', function(event){
			   var curCircleLatlng = this.getCenter();
			   var arrProperty = {};
			   arrProperty['latitude'] = curCircleLatlng.lat();
			   arrProperty['longitude'] = curCircleLatlng.lng();
			   arrProperty['radius'] = Math.round(this.getRadius()/1000);
			   arrProperty['color'] = '#0000FF';

			   console.log(JSON.stringify(arrProperty));
			});
			
			break;
		case "rectangle":
			
			google.maps.event.addListener(rec, 'bounds_changed', function(event){
				var curRec = this.getBounds();
				var arrProperty = {};
				arrProperty['latitude_ne'] = curRec.getNorthEast().lat();
				arrProperty['longitude_ne'] = curRec.getNorthEast().lng();
				arrProperty['latitude_sw'] = curRec.getSouthWest().lat();
				arrProperty['longitude_sw'] = curRec.getSouthWest().lng();
				arrProperty['color'] = COLOR;

				console.log(JSON.stringify(arrProperty));
			});
			
			break;
		case "polygon":
			
			google.maps.event.addListener(polygon, 'mouseout', function() {
				updatePloygon(polygon);
			});

			google.maps.event.addListener(polygon.getPath(), 'set_at', function() {
				updatePloygon(polygon);
			});
			
			break;
	}

	return drawingManager;
}
*/

/*
 * Update form intput fields
 * 
 * @param object resultObj
 */
function updateInputFields(resultObj){
	
	document.getElementsByName('data['+FIELD_NAME+']['+CUR_UID+'][latitude]')[0].value = resultObj.lat();
	document.getElementsByName('data['+FIELD_NAME+']['+CUR_UID+'][longitude]')[0].value = resultObj.lng();
	
	if (TYPO3_BRANCH == '6.2') {
		document.getElementsByName('data['+FIELD_NAME+']['+CUR_UID+'][latitude]_hr')[0].value = resultObj.lat();
		document.getElementsByName('data['+FIELD_NAME+']['+CUR_UID+'][longitude]_hr')[0].value = resultObj.lng();
	}
	
	if (TYPO3_BRANCH === '7.5') {
		$("[data-formengine-input-name='data["+FIELD_NAME+"]["+CUR_UID+"][latitude]']").val(resultObj.lat());
		$("[data-formengine-input-name='data["+FIELD_NAME+"]["+CUR_UID+"][longitude]']").val(resultObj.lng());
	}
}