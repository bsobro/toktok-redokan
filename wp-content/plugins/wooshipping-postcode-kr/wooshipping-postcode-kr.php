<?php
/**
 * Plugin Name: WooShipping - Postcode KR
 * Description: Korean postal code search form using Daum API
 * Plugin URI: http://planet8.co/
 * Version: 1.0.4
 * Author: Planet8
 * Author URI: http://planet8.co
 * Requires at least: 3.8
 * Tested up to: 4.0
 *
 * Text Domain: wooshipping-postcode
 * Domain Path: /languages/
 *
 * @package		WooShipping
 * @category		Postcode
 * @author		gaegoms (gaegoms@gmail.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'WC' ) ) {
	return;
}

if ( ! class_exists( 'WooShipping_Postcode' ) ) :

final class WooShipping_Postcode {
	
	public $version = '1.0.4';
	
	public $api = null;
	
	protected static $_instance = null;
	
	public static function instance() {
		
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	public function __construct() {	
		
		$this->define_constants();
		$this->load_plugin_textdomain();
		$this->includes();
		
		add_action( 'wp', array( $this, 'init' ) );
		add_action( 'wooshipping-postcode-available-pages', array( $this, 'available_pages' ) );
		
		do_action( 'wooshipping_postcode_loaded' );
	}
	
	private function define_constants() {
		define( 'WSP_FILE', __FILE__ );
		define( 'WSP_LANG', 'wooshipping-postcode' );
		define( 'WSP_VERSION', $this->version );
	}
	
	private function load_plugin_textdomain() {
		load_plugin_textdomain( WSP_LANG, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	private function includes() {		
		if ( is_admin() ) 
			include_once( 'includes/admin/class-wsp-admin.php' );		
	}
	
	public function init() {
		if ( apply_filters( 'wooshipping-postcode-available-pages', false ) && ! defined( 'DOING_CRON' ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				include_once( 'includes/class-wsp-assets.php' );
			}
			include_once( 'includes/class-wsp-fields.php' );
		}
	}
	
	public function available_pages( $enabled ) {
		$default_pages = array(
				wc_get_page_id( 'myaccount' ),
				wc_get_page_id( 'checkout' ),
		);
		if ( in_array( get_the_ID(), get_option( 'wooshipping_postcode_available_pages', $default_pages ) ) ) {
			$enabled = true;
		}
		
		return $enabled;
	}
	
}

function wooshipping_postcode() {
	return WooShipping_Postcode::instance();
}

endif;

$GLOBALS['wooshipping_postcode'] = wooshipping_postcode();