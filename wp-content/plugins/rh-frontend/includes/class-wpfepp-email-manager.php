<?php

/**
 * The WPFEPP_Email_Manager class is responsible for sending email notifications to the user and the site admin whenever a particular form is successfully submitted.
 *
 * @package WPFEPP
 * @since 2.0.0
 **/
class WPFEPP_Email_Manager
{
	/**
	 * Plugin version.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;
	
	/**
	 * Plugin's email settings.
	 *
	 * @access private
	 * @var string
	 **/
	private $settings;

	/**
	 * Class constructor.
	 *
	 * @var $_version string Plugin version.
	 **/
	public function __construct( $version ) {
		$this->load_dependencies();
		$this->version = $version;
		$this->settings = get_option( 'wpfepp_email_settings' );
	}

	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-copyscape.php';
	}

	/**
	 * Adds the actions of the class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_action( 'wpfepp_form_actions', array( $this, 'send_emails' ), 10, 2 );
	}

	/**
	 * The main function of this class. Sends emails to the user and the site admin.
	 *
	 * @var $post_data array Contains the values submitted by the user.
	 * @var $form WPFEPP_Form Form instance that called this function.
	 **/
	public function send_emails( $post_data, $form ){
		$form_settings = $form->get_settings();

		if( $post_data['action'] == 'updated' && $post_data['post_status'] == 'pending' ) {
			$post_action = $post_data['action'];
		} elseif ( $post_data['action'] == 'created' ) {
			$post_action = $post_data['action'];
		} else {
			$post_action = '';
		}
			
		if( empty( $post_action ) || ( !$form_settings['user_emails'] && !$form_settings['admin_emails'] && !$form_settings['admin_email_up'] ) )
			return;

		$emails = $this->prepare_emails( $form->get_emails(), $post_data, $form->post_type() );

		add_filter( 'wp_mail_from', array( $this, 'from_email' ), 9999 );
		add_filter( 'wp_mail_from_name', array( $this, 'from_name' ), 9999 );
		add_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ), 9999 );

		$author_id = get_post_field( 'post_author', $post_data['post_id'] );
		$author_email = get_the_author_meta( 'user_email', $author_id );
		$admin_email = get_bloginfo( 'admin_email' );

		if( $post_action == 'created' && $form_settings['user_emails'] ){
			$result = wp_mail( $author_email, $emails['user_email_subject'], $emails['user_email_content'] );
		}

		if( $post_action == 'created' && $form_settings['admin_emails'] && $author_email != $admin_email ) {
			$result = wp_mail( $admin_email, $emails['admin_email_subject'], $emails['admin_email_content'] );
		} elseif( $post_action == 'updated' && $form_settings['admin_email_up'] && $author_email != $admin_email ) {
			$result = wp_mail( $admin_email, $emails['admin_email_subject_up'], $emails['admin_email_content_up'] );
		}

		remove_filter( 'wp_mail_from', array( $this, 'from_email' ), 9999 );
		remove_filter( 'wp_mail_from_name', array( $this, 'from_name' ), 9999 );
		remove_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ), 9999 );
	}

	/**
	 * Prepares the email subjects and messages for sending by replacing placeholders with suitable values.
	 *
	 * @var $emails array The array in which we want to replace placeholders with values.
	 * @var $post_data array User submitted data.
	 * @return $emails array A modified array with all the placeholders replaced with values.
	 **/
	public function prepare_emails( $emails, $post_data, $post_type ){

		$values['post_title'] = wp_strip_all_tags( $post_data['title'] );
		$values['post_permalink'] = get_post_permalink( $post_data['post_id'] );
		$author_id = get_post_field( 'post_author', $post_data['post_id'] );
		$values['author_name'] = get_the_author_meta('display_name', $author_id);
		$values['site_name'] = get_bloginfo('name');
		$values['site_url'] = get_bloginfo('url');
		$admin_info = get_userdata(1);
		$values['admin_name'] = ($admin_info) ? $admin_info->display_name : __( "Admin", "wpfepp-plugin" );
		$values['edit_link'] = sprintf( admin_url( 'edit.php?post_type=%s' ), $post_type );
		$values['copyscape_status'] = get_post_meta( $post_data['post_id'], WPFEPP_CopyScape::$meta_key, true );

		foreach ( $emails as $key => $email_part ) {
			$emails[$key] = $this->fill_placeholders( $email_part, $values );
		}

		return $emails;
	}

	public function fill_placeholders($string, $values){
		foreach ($values as $key => $val) {
			$placeholder = '%%'.strtoupper($key).'%%';
			$string = str_replace($placeholder, $val, $string);
		}
		return stripslashes($string);
	}

	public function from_email( $email ) {

		if($this->settings['sender_address'] && is_email($this->settings['sender_address']))
	    	return $this->settings['sender_address'];

	    return $email;
	}

	public function from_name( $name ) {

		if($this->settings['sender_name'])
	    	return $this->settings['sender_name'];

	    return $name;
	}

	public function set_content_type( $content_type ){
		return 'text/'.$this->settings['email_format'];
	}

} // END class WPFEPP_Email_Manager

?>