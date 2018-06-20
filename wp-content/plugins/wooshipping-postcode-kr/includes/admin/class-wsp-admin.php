<?php
/**
 * 관리자
 * 
 * @class			WSP_Admin
 * @version		1.0.0
 * @package		Core
 * @category		Dashboard
 * @author 		gaegoms (gaegoms@gmail.com)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WSP_Admin {

	public function __construct() {
		
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( WSP_FILE ), array( $this, 'change_plugin_action_links' ) );
		
	}
	
	public function includes() {
		
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings' ) {
			include_once( 'class-wsp-admin-settings.php' );
		}
	
	}
	
	public function conditional_includes() {

		$screen	= get_current_screen();		

		switch ( $screen->id ) {			
			case 'users' :
			case 'user' :
			case 'profile' :
			case 'user-edit' :
			
		}
	}
	
	public function change_plugin_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping#wooshipping_postcode_use_company' ) . '">' . __( 'Settings', WSP_LANG ) . '</a>';

		array_unshift( $links, $settings_link ); 
		return $links; 
	}
	
}

return new WSP_Admin();
