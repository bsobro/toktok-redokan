<?php
/**
 * @package Joonas
 */
/*
Plugin Name: 00 Joonas Plugin
Plugin URI: https://joonas.tistory.com/
Description: asdfasdfasdf
Version: 0.1.0
Author: Joona Yoon
Author URI: https://joonas.tistory.com/
License: None
Text Domain: JoonasPlugin
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'JOONAS_VERSION', '4.0.3' );
define( 'JOONAS__MINIMUM_WP_VERSION', '4.0' );
define( 'JOONAS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JOONAS__TEMPLATE_DIR', plugin_dir_path( __FILE__ ) . 'templates/' );
define( 'JOONAS_DELETE_LIMIT', 100000 );

register_activation_hook( __FILE__, array( 'Joonas', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Joonas', 'deactivate' ) );

require_once( JOONAS__PLUGIN_DIR . 'class.joonas.php' );
// require_once( JOONAS__PLUGIN_DIR . 'class.joonas-widget.php' );
require_once( JOONAS__PLUGIN_DIR . 'class.joonas-rest-api.php' );

add_action( 'init', array( 'Joonas', 'init' ) );

add_action( 'rest_api_init', array( 'Joonas_REST_API', 'register_endpoints' ) );

