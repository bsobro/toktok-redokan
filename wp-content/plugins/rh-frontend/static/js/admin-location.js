jQuery( document ).ready( function( $ ) {
	
	if ("undefined" !== typeof wpfeppl && '1' == wpfeppl.enable_map ) {
		jQuery( '.wpfepp_map_start_location' ).mapify({
			mapHeight		: '200px',
			startGeoLat		: wpfeppl.start_geo_lat, 
			startGeoLng 	: wpfeppl.start_geo_long, 
			latInputId 			: 'wpfepp_start_geo_lat', 
			lngInputId 		: 'wpfepp_start_geo_long'
		});
	}
});
