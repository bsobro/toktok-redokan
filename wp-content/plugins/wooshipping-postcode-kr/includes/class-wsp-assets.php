<?php 
/**
 * 우편번호 검색 > 스타일시트 & 스크립트 로딩
 *
 * @class			WSP_Assets
 * @version		1.0.0
 * @package		WooShipping
 * @category		Postcode
 * @author 		gaegoms (gaegoms@gmail.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
class WSP_Assets {
	
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}
	
	public static function enqueue_assets() {
		$base_url = str_replace( array( 'http:', 'https:' ), '', untrailingslashit( plugins_url( '/', WSP_FILE ) ) ) . '/assets/';
	
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		
		wp_enqueue_style( 'wooshipping-postcode', $base_url . 'wooshipping-postcode.css', array(), WSP_VERSION );
		wp_enqueue_script( 'wooshipping-postcode', $base_url . 'wooshipping-postcode.js', array( 'jquery' ), WSP_VERSION, true );
		$api_url = is_ssl() ? '//spi.maps.daum.net/imap/map_js_init/postcode.v2.js' : '//dmaps.daum.net/map_js_init/postcode.v2.js';
		wp_enqueue_script( 'wooshipping-postcode-daum', $api_url , array( 'jquery' ), WSP_VERSION, true );
	
		$args = array(
				'ajax_url'			=> admin_url( 'admin-ajax.php' ),
				'label'				=> get_option( 'wooshipping_postcode_trigger_text', __( 'Find PostCode', WSP_LANG ) ),
				'use_fullname'	=> get_option( 'wooshipping_postcode_use_lastname', 'no' ),
				'use_company'	=> get_option( 'wooshipping_postcode_use_company', 'no' ),
				'use_country'	=> get_option( 'wooshipping_postcode_use_country', 'yes' ),
		);
		
		wp_localize_script( 'wooshipping-postcode', 'wooshipping_postcode_params', apply_filters( 'wooshipping_postcode_localize_script', $args ) );
	}
}

WSP_Assets::init();