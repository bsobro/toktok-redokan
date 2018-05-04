<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/widget/store-location.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version     0.0.1
 */
extract( $instance );
global $WCMp;

?><div class="wcmp-store-location-wrapper">
		<div id="store-maps" class="store-maps" class="wcmp-gmap" style="height: 300px;"></div>
		<a href="<?php echo $gmaps_link ?>" target="_blank"><?php _e( 'Show in Google Maps', 'rehub_framework' ) ?></a>
</div>
<?php 
wp_add_inline_script( 'wcmp-gmaps-api', 
  '(function ($) {
    var myLatLng = {lat: '.$store_lat.', lng: '.$store_lng.'};
    var map = new google.maps.Map(document.getElementById("store-maps"), {
        zoom: 15,
        center: myLatLng
    });
    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        title: "'.$location.'"
    });
})(jQuery);');