<?php
/*
Plugin Name: RH Grandchild Custom
Plugin URI: http://wpsoul.net/
Description: Specialized plugin for customization ReHub child themes
Version: 1.0.3
Author: Check docs
Author URI: http://rehub.wpsoul.com/documentation/docs.html#grandchild
License: GPL2
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Path and URL
if ( ! defined( 'RH_GRANDCHILD_DIR' ) ) {
	define( 'RH_GRANDCHILD_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'RH_GRANDCHILD_URL' ) ) {
	define( 'RH_GRANDCHILD_URL', plugin_dir_url( __FILE__ ) );
}

// Search for templates in plugin 'templates' dir, and load if exists
function rh_grandchild_template( $template ) {
  if ( file_exists( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' . basename( $template ) ) )
    $template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/' . basename( $template );
  return $template;
}
add_filter( 'template_include', 'rh_grandchild_template', 11 );

// Custom functions go here
function rh_grandchild_func() {
	include( RH_GRANDCHILD_DIR . 'rh-grandchild-func.php' );
}

// Custom styles go here
function rh_grandchild_style() {
    wp_enqueue_style( 'grandchild-style', RH_GRANDCHILD_URL . 'rh-grandchild-style.css', false, 1.0 );
	wp_enqueue_script( 'grandchild-script', RH_GRANDCHILD_URL . 'rh-grandchild-script.js', array( 'jquery' ), 1.0, true );
}

//Here, we check Buddypress files from plugin first
function rh_grandchild_load_template_filter( $stack ) {   
	array_unshift($stack, untrailingslashit( RH_GRANDCHILD_DIR ) . '/buddypress');
	return $stack;
}
add_filter( 'bp_get_template_stack', 'rh_grandchild_load_template_filter');

// Search for woocommerce templates in plugin 'woocommerce' dir, and load if exists
function rh_grandchild_wc_template( $located, $template_name ) {
	if ( file_exists( untrailingslashit( RH_GRANDCHILD_DIR ) . '/woocommerce/'. $template_name ) )
		$located = untrailingslashit( RH_GRANDCHILD_DIR ) . '/woocommerce/'. $template_name;
  	return $located;
}
add_filter( 'wc_get_template', 'rh_grandchild_wc_template', 11, 2 );


//add_action( 'wp_enqueue_scripts', 'rh_grandchild_style', 11 ); // Delete two slashes in begining of line to enable custom css and js files

?>