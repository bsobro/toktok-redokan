<?php
/**
 * 관리자 > 설정
 * 
 * @class			WSP_Admin_Settings
 * @version		1.0.0
 * @package		WooShipping
 * @category		Postcode
 * @author 		gaegoms (gaegoms@gmail.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WSP_Admin_Settings {
	
	public function __construct() {		
		add_filter( 'woocommerce_get_settings_shipping', array( $this, 'add_settings' ) );		
	}
	
	public function add_settings( $settings ) {
		wc_enqueue_js( "	$( '#wooshipping_postcode_enable' ).each( test ).on( 'click', test );
				function test() {
					var fieldbox = $( '.wsp-field' ).closest( 'tr' );
					if ( $( this ).is( ':checked') ) fieldbox.show();
					else fieldbox.hide();
				}
		" );
		$prefix = 'wooshipping_postcode_';
		
		$pages_options = array();
		$pages = get_pages( array(
				'sort_order' => 'asc',
				'sort_column' => 'post_title',
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'child_of' => 0,
				'parent' => -1,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
		) );
		
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
		
		$new = array(
				array( 'title' => __( 'PostCode and Address Format for Korean', WSP_LANG ), 'type' => 'title', 'id' => 'postcode_options' ),
				array(
						'id'					=> $prefix . 'available_pages',
						'title'				=> __( 'Available Pages', WSP_LANG ),
						'default'			=> array( wc_get_page_id( 'checkout' ), wc_get_page_id( 'myaccount' ) ),
						'type'				=> 'multiselect',
						'options'			=> $pages_options,
						'class'				=> 'wsp-field wc-enhanced-select',
						'desc'				=> __( 'Please select a page to use the new address entry form. The default is my page and payment page.', WSP_LANG ),
						'desc_tip'		=> true,
				),
				array(
						'id'					=> $prefix . 'use_company',
						'title'				=> __( 'Show Company Field', WSP_LANG ),
						'desc'				=> __( 'Display', WSP_LANG ),
						'default'			=> 'no',
						'type'				=> 'checkbox',
						'desc'				=> __( 'Shipping Displays company address fields in the input field.', WSP_LANG ) . __( '(Default:no)', WSP_LANG ),
				),
				array(
						'id'					=> $prefix . 'use_country',
						'title'				=> __( 'Show Country Field', WSP_LANG ),
						'desc'				=> __( 'Display', WSP_LANG ),
						'default'			=> 'yes',
						'type'				=> 'checkbox',
						'desc'				=> __( 'Shipping address in the input field to display a country selection field.', WSP_LANG ) . __( '(Default:yes)', WSP_LANG ),
				),
				array(
						'id'					=> $prefix . 'use_shipping_phone',
						'title'				=> __( 'Shipping Phone Field', WSP_LANG ),
						'desc'				=> __( 'Display', WSP_LANG ),
						'default'			=> 'no',
						'type'				=> 'checkbox',
						'desc'				=> __( 'Billing address, shipping address, as well as help in adding a field to enter the recipient\'s phone number can receive.', WSP_LANG ),
				),
				array(
						'id'					=> $prefix . 'use_lastname',
						'title'				=> __( 'Last Name Field', WSP_LANG ),
						'desc'				=> __( 'Display', WSP_LANG ),
						'default'			=> 'no',
						'type'				=> 'checkbox',
						'desc'				=> __( 'If you uncheck, in the country field when you choose the Republic of Korea, Enter your first and last name in a single field.', WSP_LANG ) . __( '(Default:no)', WSP_LANG ),
				),
				array(
						'id'					=> $prefix . 'trigger_text',
						'title'				=> __( 'Postcode Button Text', WSP_LANG ),
						'default'			=> __( 'Find PostCode', WSP_LANG ),
						'type'				=> 'text',
						'desc'				=> __( 'You can specify the text that appears when you select a zip code search button Republic of Korea in a country field.', WSP_LANG ),
						'desc_tip'		=> true,
				),
				array( 'type' => 'sectionend', 'id' => 'postcode_options' ),
		);
		
		return array_merge( $settings, $new );
	}
		
}

new WSP_Admin_Settings();