jQuery(document).ready(function(){
	function initialize() {
		var mapType = contact_details.map_type;
		var myLatlng 	= new google.maps.LatLng( contact_details.geocoords_x, contact_details.geocoords_y );
	  	var myOptions 	= {
		  zoom: parseInt( contact_details.zoom ),
		  center: myLatlng,
		  mapTypeId: google.maps.mapType // SATELLITE, HYBRID, TERRAIN
		};
		if ( contact_details.map_mouse_scroll === 'false' ) {
	  		myOptions.scrollwheel = false;
	  	} // End If Statement
	  	var map 		= new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);
  		var point 		= new google.maps.LatLng( contact_details.geocoords_x, contact_details.geocoords_y );
		var root 		= contact_details.plugin_url;
		var callout 	= contact_details.map_callout;
		var the_link 	= contact_details.map_link;
		var the_title 	= contact_details.marker_title;
	 	var color 		= contact_details.marker_color;
		createMarker(map,point,root,the_link,the_title,color,callout);
	} // End initialize()

	function handleNoFlash( errorCode ) {
		if (errorCode == FLASH_UNAVAILABLE) {
			alert( "Error: Flash doesn't appear to be supported by your browser" );
			return;
		} // End If Statement
	} // End handleNoFlash()

	if ( jQuery( '#single_map_canvas' ).length ) {
		initialize();
	} // End If Statement
});