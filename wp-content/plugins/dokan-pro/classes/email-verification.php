<?php

/**
 * Dokan Email Verification class
 *
 * @since 2.7.3
 *
 * @package dokan-pro
 *
 */

Class Dokan_Email_Verification {

    private $base_url;

    /**
     * Load automatically when class instantiated
     *
     * @since 2.7.3
     *
     * @uses actions|filter hooks
     */
    public function __construct() {
        $this->base_url = dokan_get_page_url( 'myaccount', 'woocommerce' );
        $this->init_hooks();
    }

    /**
     * Instantiate the class
     *
     * @since 2.6
     *
     * @return object
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Email_Verification();
        }

        return $instance;
    }

    /**
     * call actions and hooks
     */
    public function init_hooks() {
        //add settings menu page
        add_filter( 'dokan_settings_sections', array( $this, 'dokan_email_verification_settings' ) );
        add_filter( 'dokan_settings_fields', array( $this, 'dokan_email_settings_fields' ) );

        if ( 'on' != dokan_get_option( 'enabled', 'dokan_email_verification' ) ) {
           return;
        }

        add_action( 'woocommerce_created_customer', array( $this,'send_verification_email'), 5, 3 );
        add_action( 'woocommerce_registration_redirect', array( $this, 'check_verification' ), 99 );
        add_action( 'woocommerce_login_redirect', array( $this, 'check_verification' ), 99, 2 );
        add_action( 'init', array( $this,'validate_email_link' ), 100 );
        add_action( 'woocommerce_email_footer', array( $this,'add_activation_link' ) );
    }

    /**
     * Set Verification meta
     *
     * @param type $customer_id
     *
     * @param type $new_customer_data
     *
     * @param type $password_generated
     *
     * @return void
     */
    function send_verification_email( $customer_id, $new_customer_data, $password_generated ) {
        $user            = get_user_by( 'id', $customer_id );
        $code            = sha1( $customer_id . $user->user_email . time() );

        // update user meta
        add_user_meta( $customer_id, '_dokan_email_verification_key', $code, true );
        add_user_meta( $customer_id, '_dokan_email_pending_verification', true, true);
    }

    /**
     * Check for verification when a user logs in
     *
     * @param type $redirect
     *
     * @param WP_User $user
     *
     * @return String $redirect
     */
    function check_verification( $redirect, $user = array() ) {

        $user_id = get_current_user_id();
        $notice = dokan_get_option( 'registration_notice', 'dokan_email_verification' );

        if ( !empty( $user ) ) {
            $user_id = $user->ID;
            $notice = dokan_get_option( 'login_notice', 'dokan_email_verification' );
        }

        $pending_verification = get_user_meta( $user_id, '_dokan_email_pending_verification', true );

        if ( !$pending_verification ) {
            return $redirect;
        }

        wp_logout();

        wc_add_notice( sprintf( __( '%s', 'dokan' ), $notice ) );
        do_action( 'woocommerce_set_cart_cookies', true );

        return $this->base_url;
    }

    /**
     * Validate Email from link
     */
    function validate_email_link() {

        if ( !isset( $_GET['dokan_email_verification'] ) && empty( $_GET['dokan_email_verification'] ) ) {
            return;
        }

        if ( !isset( $_GET['id'] ) && empty( $_GET['id'] ) ) {
            return;
        }

        $user_id = intval( $_GET['id'] );
        $activation_key = $_GET['dokan_email_verification'];

        if ( get_user_meta( $user_id, '_dokan_email_verification_key', true ) != $activation_key ) {
            return;
        }

        delete_user_meta( $user_id, '_dokan_email_pending_verification' );
        delete_user_meta( $user_id, '_dokan_email_verification_key' );

        // $notice = dokan_get_option( 'activation_notice', 'dokan_email_verification' );

        // wc_add_notice( sprintf( __( '%s', 'dokan' ), $notice ) );
        do_action( 'woocommerce_set_cart_cookies', true );

        $user = get_user_by( 'id', $user_id );

        if ( $user ) {
            clean_user_cache( $user_id );
            wp_clear_auth_cookie();
            wp_set_current_user( $user_id, $user->user_login );

            if ( is_ssl() == true ) {
                wp_set_auth_cookie( $user_id, true, true );
            } else {
                wp_set_auth_cookie( $user_id, true, false );
            }

            update_user_caches( $user );
        }

        $seller_wizard = new Dokan_Seller_Setup_Wizard();
        $seller_wizard->setup_wizard();
    }

    /**
     * Add verification link in welcome email
     *
     * @param type $email
     *
     * @return void
     */
    function add_activation_link( $email ) {

        if ( $email->id != 'customer_new_account' ) {
            return;
        }

        $user = get_user_by( 'email', $email->user_email );

        $verification_key = get_user_meta( $user->ID, '_dokan_email_verification_key', true );

        if ( empty( $verification_key ) ) {
            return;
        }

        if ( in_array( 'seller', $user->roles ) ) {
            $verification_link = add_query_arg( array( 'dokan_email_verification' => $verification_key, 'id' => $user->ID, 'page' => 'dokan-seller-setup' ), $this->base_url );
        } else {
            $verification_link = add_query_arg( array( 'dokan_email_verification' => $verification_key, 'id' => $user->ID ), $this->base_url );
        }

        $message = sprintf( __( "<p><b>To Verify your Email <a href='%s'>Click Here</a></b></p>", 'dokan' ), $verification_link );

        echo apply_filters( 'dokan_email_verification_email_text' , $message, $verification_link );
    }

    /**
     * Filter admin menu settings section
     *
     * @param type $sections
     *
     * @return array
     */
    public function dokan_email_verification_settings( $sections ) {
        $sections[] = array(
            'id'    => 'dokan_email_verification',
            'title' => __( 'Email Verification', 'dokan' ),
            'icon'  => 'dashicons-shield'
        );
        return $sections;
    }

    /**
     * Render settings fields for admin settings section
     *
     * @param array $settings_fields
     *
     * @return array
     */
    public function dokan_email_settings_fields( $settings_fields ) {

        $settings_fields['dokan_email_verification'] = array(
            'enabled' => array(
                'name'  => 'enabled',
                'label' => __( 'Enable Email Verification', 'dokan' ),
                'type'  => "checkbox",
                'desc'  => __( 'Enabling this will add Email verification after registration form to allow users to verify their emails', 'dokan' ),
            ),
            'registration_notice' => array(
                'name'  => 'registration_notice',
                'label' => __( 'Registration Notice', 'dokan' ),
                'type'  => "text",
                'desc'  => __( 'This notice will be shown after a user has registered with pending email verification.', 'dokan' ),
                'default' => __( 'Please Check your Email and complete Email Verification to Login.', 'dokan' ),
            ),
            'login_notice' => array(
                'name'  => 'login_notice',
                'label' => __( 'Login Notice', 'dokan' ),
                'type'  => "text",
                'desc'  => __( 'This notice will be shown when a user tries to login without email verification.', 'dokan' ),
                'default' => __( 'Please Check your Email and complete Email Verification to Login.', 'dokan' ),
            ),
            // 'activation_notice' => array(
            //     'name'  => 'activation_notice',
            //     'label' => __( 'Activation Notice', 'dokan' ),
            //     'type'  => "text",
            //     'desc'  => __( 'This notice will be shown when a user succesfully completes email verification.', 'dokan' ),
            //     'default' => __( 'Email verification is complete, you will be loggedin automatically in a few seconds.', 'dokan' ),
            // ),


        );

        return $settings_fields;
    }

    /**
     * Get Post Type array
     *
     * @param  string $post_type
     *
     * @return array
     */
    public function get_post_type( $post_type ) {
        $pages_array = array( '-1' => __( '- select -', 'dokan' ) );
        $pages = get_posts( array('post_type' => $post_type, 'numberposts' => -1) );

        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_array[$page->ID] = $page->post_title;
            }
        }

        return $pages_array;
    }

}
