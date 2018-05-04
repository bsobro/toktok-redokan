<?php

/**
 * Creates a table of posts along with a form for editing posts. Also responsible for handling post deletion requests.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Post_List
{
	/**
	 * Plugin version.
	 *
	 * @var string
	 **/
	private $version;

	/**
	 * Form id.
	 *
	 * @var string
	 **/
	private $formid;

	/**
	 * Show all shortcode parameter.
	 *
	 * @var string
	 **/
	private $show_all;		
	
	/**
	 * An instance of the WPFEPP_Form class for editing posts.
	 *
	 * @var WPFEPP_Form
	 **/
	private $form;

	/**
	 * A boolean flag that keeps track of whether the form exists in the database table or not.
	 *
	 * @var boolean
	 **/
	private $valid;

	/**
	 * The post type for which we want to display posts
	 *
	 * @var string
	 **/
	private $post_type;

	/**
	 * Class constructor. Initializes the attributes of the object.
	 **/
	public function __construct( $version, $form_id = -1, $show_all = 1 ) {
		$this->version = $version;
		$this->formid = $form_id;
		$this->show_all = $show_all;

		if( $form_id < 0 ) {
			$this->valid = false;
			return;
		}

		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-form.php';
		$this->form = new WPFEPP_Form( $this->version, $form_id, false );

		if( ! $this->form->valid() ) {
			$this->valid = false;
			return;
		}

		$this->valid = true;
		$this->post_type = $this->form->post_type();
	}

	/**
	 * Adds the actions of the class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions() {
		add_action( 'wp', array( $this, 'handle_deletion_request' ) );
		add_action( 'wp_ajax_wpfepp_delete_post', array( $this, 'delete_post_ajax' ) );
	}

	/**
	 * Outputs the HTML of the post list or the form (depending on $_GET variables).
	 **/
	public function display() {
		if( !$this->valid ) {
			_e( "No form with the specified ID was found", "wpfepp-plugin" );
			return;
		}
		if( !is_user_logged_in() ) {
			$class = apply_filters( 'wpfepp_login_url_class', 'wpfepp-login-url' );
			printf( __( "You need to %s first.", "wpfepp-plugin" ), sprintf( '<a href="%s" class="%s">%s</a>', wp_login_url(), $class, __( "login", "wpfepp-plugin" ) ) );
			return;
		}
		if( $this->form->post_type() == 'product' && !class_exists( 'Woocommerce' ) && current_user_can( 'install_plugins' ) ) {
			printf( '<div class="wpfepp wpfepp-posts"><div class="wpfepp-message error display">%s</div></div>', __( "Woocommerce plugin is deactivated or not installed.", "wpfepp-plugin" ) );
			return;
		}
		if( isset( $_GET['wpfepp_post'] ) && isset( $_GET['wpfepp_action'] ) && is_numeric( $_GET['wpfepp_post'] ) && $_GET['wpfepp_action'] == 'edit' ) {
			$this->form->display( $_GET['wpfepp_post'] );
		} else {
			$this->print_list();
		}	
	}

	/**
	 * Takes care of post deletion and redirects the user back to the post list table with a new query variable 'wpfepp_deleted'.
	 **/
	public function handle_deletion_request() {
		if( isset( $_GET['wpfepp_post'] ) && isset( $_GET['wpfepp_action'] ) && is_numeric( $_GET['wpfepp_post'] ) && isset( $_GET['_wpnonce'] ) && $_GET['wpfepp_action'] == 'delete' ) {
			$blog_page = isset( $_GET['p'] ) ? array( 'p', $_GET['p'] ) : array();
			$result = $this->delete_post( $_GET['wpfepp_post'], $_GET['_wpnonce'] );
			$success_vars = ( $result['success'] ) ? array( 'wpfepp_deleted' => 1 ) : array();
			$sendback = esc_url_raw( add_query_arg( array_merge( $blog_page, $success_vars ), '' ) );
			wp_redirect( $sendback );
		}
	}

	/**
	 * Deletes posts after checking nonce and making sure that the current user has permission to perform the deletion. Uses WordPress' own wp_delete_post().
	 *
	 * @param int $post_id The id of the post that we want to delete.
	 * @param string $delete_nonce A nonce string that ensures that the request is coming from the right person.
	 * @return array An associative array containing a status flag and a message to display to the user.
	 **/
	private function delete_post( $post_id, $delete_nonce ) {
		$data = array( 'success' => false, 'message' => '' );
		do{
			if( ! wp_verify_nonce( $delete_nonce, 'wpfepp-delete-post-'.$post_id.'-nonce' ) ) {
				$data['message'] = __( "Sorry! You failed the security check", "wpfepp-plugin" );
				break;
			}	
			if( ! $this->current_user_can_delete( $post_id ) ) {
				$data['message'] = __( "You don't have permission to delete this post", "wpfepp-plugin" );
				break;
			}

			//Here we update user meta limit for this form if form has limits for submit
			$post_author_id = get_post_field( 'post_author', $post_id );
			$getlimitednumber = get_post_meta($post_id, '_form_limit_number', true);
			$formid = get_post_meta($post_id, 'wpfepp_submit_with_form_id', true);
			if($getlimitednumber && $post_author_id){
				$user_numb_post_meta = '_rhf_user_submit_counter_form_'. $formid;
				$author_number_post_package = get_user_meta( $post_author_id, $user_numb_post_meta, true );
				if($author_number_post_package >=0){
					$author_number_post_package = (int)$author_number_post_package + 1;
					update_user_meta( $post_author_id, $user_numb_post_meta, $author_number_post_package );			
				}

			}

			$result = wp_delete_post( $post_id, false );
			if( ! $result ) {
				$data['message'] = __( "The article could not be deleted", "wpfepp-plugin" );
				break;
			}

			$data['success'] = true;
			$data['message'] = __( "The article has been deleted successfully!", "wpfepp-plugin" );
		}
		while (0);

		return $data;
	}

	/**
	 * Ajax function that processes the deletion request and prints out the appropriate response as a json encoded string.
	 *
	 * @param array The $_POST array.
	 * @return string A json encoded string.
	 **/
	public function delete_post_ajax() {
		die( json_encode( $this->delete_post( $_POST['post_id'], $_POST['delete_nonce'] ) ) );
	}

	/**
	 * Outputs HTML of the post list table.
	 **/
	private function print_list() {
		$check_form_id = $this->formid;
		$show_all = $this->show_all;
		include( 'partials/post-list.php' );
	}

	/**
	 * By default WordPress does not allow subscribers and contributors to delete their own posts. This function aims rectifies this problem.
	 *
	 * @param string $action The action to check.
	 * @param int Post id.
	 * @return boolean Whether or not the current user can perform the specified action.
	 **/
	private function current_user_can_delete( $post_id ) {
		$post_author_id = get_post_field( 'post_author', $post_id );
		$current_user = wp_get_current_user();
		return ( $post_author_id == $current_user->ID || current_user_can( 'delete_post', $post_id ) );
	}
}

?>