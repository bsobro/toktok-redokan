<?php

/**
 * Dokan Social login class
 *
 * @since 2.6.6
 *
 * @package dokan-pro
 *
 */

Class Dokan_Social_Login {

    private $base_url;
    private $config;

    /**
     * Load automatically when class instantiated
     *
     * @since 2.4
     *
     * @uses actions|filter hooks
     */
    public function __construct() {
        $this->base_url = dokan_get_page_url( 'myaccount', 'woocommerce' ) . 'dokan-registration/edit';
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
            $instance = new Dokan_Social_Login();
        }

        return $instance;
    }

    /**
     * call actions and hooks
     */
    public function init_hooks() {
        //add settings menu page
        add_filter( 'dokan_settings_sections', array( $this, 'dokan_social_api_settings' ) );
        add_filter( 'dokan_settings_fields', array( $this, 'dokan_social_settings_fields' ) );

        if ( 'on' != dokan_get_option( 'enabled', 'dokan_social_api' ) ) {
           return;
        }
        $this->config   = $this->get_providers_config();

        //Hybrid auth action
        add_action( 'init', array( $this, 'init_session' ) );
        add_action( 'template_redirect', array( $this, 'monitor_autheticate_requests' ) );

        // add social buttons on registration form and login form
        add_action( 'woocommerce_register_form_end', array( $this, 'render_social_logins' ) );
        add_action( 'woocommerce_login_form_end', array( $this, 'render_social_logins' ) );
        // add hybridauth library
        //add custom my account end-point
        add_filter( 'dokan_query_var_filter', array( $this, 'register_support_queryvar' ) );
        add_action( 'dokan_load_custom_template', array( $this, 'load_template_from_plugin' ) );
    }

    /**
     * Initialize session at start
     */
    public function init_session() {
        if ( session_id() == '' ) {
            session_start();
        }
    }

    /**
     * Get configuration values for HybridAuth
     *
     * @return array
     */
    private function get_providers_config() {

        $config    = array( 'providers' => array(
                'base_url'   => $this->base_url,
                "debug_mode" => false,
                "Google"     => array(
                    "enabled" => true,
                    "keys"    => array( "id" => "", "secret" => "" ),
                ),
                "Facebook"   => array(
                    "enabled"        => true,
                    "keys"           => array( "id" => "", "secret" => "" ),
                    "trustForwarded" => false,
                    "scope"          => "email, public_profile, user_friends"
                ),
                "Twitter"    => array(
                    "enabled"      => true,
                    "keys"         => array( "key" => "", "secret" => "" ),
                    "includeEmail" => true,
                ),
                "LinkedIn"   => array(
                    "enabled" => true,
                    "keys"    => array( "id" => "", "secret" => "" ),
                    "fields"  => array(),
                ),
        ) );
        //facebook config from admin
        $fb_id     = dokan_get_option( 'fb_app_id', 'dokan_social_api' );
        $fb_secret = dokan_get_option( 'fb_app_secret', 'dokan_social_api' );
        if ( $fb_id != '' && $fb_secret != '' ) {
            $config['providers']['Facebook']['keys']['id']     = $fb_id;
            $config['providers']['Facebook']['keys']['secret'] = $fb_secret;
        }
        //google config from admin
        $g_id     = dokan_get_option( 'google_app_id', 'dokan_social_api' );
        $g_secret = dokan_get_option( 'google_app_secret', 'dokan_social_api' );
        if ( $g_id != '' && $g_secret != '' ) {
            $config['providers']['Google']['keys']['id']     = $g_id;
            $config['providers']['Google']['keys']['secret'] = $g_secret;
        }
        //linkedin config from admin
        $l_id     = dokan_get_option( 'linkedin_app_id', 'dokan_social_api' );
        $l_secret = dokan_get_option( 'linkedin_app_secret', 'dokan_social_api' );
        if ( $l_id != '' && $l_secret != '' ) {
            $config['providers']['LinkedIn']['keys']['id']     = $l_id;
            $config['providers']['LinkedIn']['keys']['secret'] = $l_secret;
        }
        //Twitter config from admin
        $twitter_id     = dokan_get_option( 'twitter_app_id', 'dokan_social_api' );
        $twitter_secret = dokan_get_option( 'twitter_app_secret', 'dokan_social_api' );
        if ( $twitter_id != '' && $twitter_secret != '' ) {
            $config['providers']['Twitter']['keys']['key']    = $twitter_id;
            $config['providers']['Twitter']['keys']['secret'] = $twitter_secret;
        }

        /**
         * Filter the Config array of Hybridauth
         *
         * @since 1.0.0
         *
         * @param array $config
         */
        $config = apply_filters( 'dokan_social_providers_config', $config );

        return $config;
    }

    /**
     * Monitors Url for Hauth Request and process Hauth for authentication
     *
     * @return void
     */
    public function monitor_autheticate_requests() {

        if ( !class_exists( 'WeDevs_Dokan' ) ) {
            return;
        }

        $config = $this->config;

        if ( isset( $_GET['hauth_start'] ) || isset( $_GET['hauth_done'] ) ) {
            require_once DOKAN_PRO_INC . '/lib/Hybrid/Endpoint.php';

            Hybrid_Endpoint::process();
            exit;
        }

        if ( !isset( $_GET['dokan_reg'] ) ) {
            return;
        }

        $hybridauth = new Hybrid_Auth( $config );
        $provider   = $_GET['dokan_reg'];

        try {
            if ( $provider != '' ) {
                $adapter = $hybridauth->authenticate( $provider );

                if ( $adapter->isUserConnected() ) {
                    $user_profile = $adapter->getUserProfile();
                } else {
                    wc_add_notice( __( 'Something went wrong! please try again', 'dokan' ), 'success' );
                    wp_redirect( $this->base_url );
                }

                $wp_user = get_user_by( 'email', $user_profile->email );

                if ( !$wp_user ) {
                    $this->register_new_user( $user_profile, $provider );
                } else {
                    $this->login_user( $wp_user );
                }
            }
        } catch ( Exception $e ) {
            $this->e_msg = $e->getMessage();
        }
    }

    /**
     * Filter admin menu settings section
     *
     * @param type $sections
     *
     * @return array
     */
    public function dokan_social_api_settings( $sections ) {
        $sections[] = array(
            'id'    => 'dokan_social_api',
            'title' => __( 'Social API', 'dokan' ),
            'icon'  => 'dashicons-networking'
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
    public function dokan_social_settings_fields( $settings_fields ) {

        $settings_fields['dokan_social_api'] = array(
            'enabled' => array(
                'name'  => 'enabled',
                'label' => __( 'Enable Social Login', 'dokan-social-api' ),
                'type'  => "checkbox",
                'desc'  => __( 'Enabling this will add Social Icons under registration form to allow users to login or register using Social Profiles', 'dokan-social-api' ),
            ),
            'facebook_app_label'  => array(
                'name'  => 'fb_app_label',
                'label' => __( 'Facebook App Settings', 'dokan-social-api' ),
                'type'  => "html",
                'desc'  => '<a target="_blank" href="https://developers.facebook.com/apps/">' . __( 'Create an App', 'dokan-social-api' ) . '</a> if you don\'t have one and fill App ID and Secret below. <a href="https://wedevs.com/docs/dokan/settings/dokan-social-login/configuring-facebook/" target="_blank">Get Help</a>',
            ),
            'facebook_app_url'    => array(
                'name'  => 'fb_app_url',
                'label' => __( 'Site Url', 'dokan-social-api' ),
                'type'  => 'html',
                'desc'  => "<input class='regular-text' type='text' disabled value=" . $this->base_url . '?dokan_reg=facebook&hauth.done=Facebook' . '>',
            ),
            'facebook_app_id'     => array(
                'name'  => 'fb_app_id',
                'label' => __( 'App Id', 'dokan-social-api' ),
                'type'  => 'text',
            ),
            'facebook_app_secret' => array(
                'name'  => 'fb_app_secret',
                'label' => __( 'App Secret', 'dokan-social-api' ),
                'type'  => 'text',
            ),
            'twitter_app_label'   => array(
                'name'  => 'twitter_app_label',
                'label' => __( 'Twitter App Settings', 'dokan-social-api' ),
                'type'  => 'html',
                'desc'  => '<a target="_blank" href="https://apps.twitter.com/">' . __( 'Create an App', 'dokan-social-api' ) . '</a> if you don\'t have one and fill Consumer key and Secret below. <a href="https://wedevs.com/docs/dokan/settings/dokan-social-login/configuring-twitter/" target="_blank">Get Help</a>',
            ),
            'twitter_app_url'     => array(
                'name'  => 'twitter_app_url',
                'label' => __( 'Callback URL', 'dokan-social-api' ),
                'type'  => 'html',
                'desc'  => "<input class='regular-text' type='text' disabled value=" . $this->base_url . '?dokan_reg=twitter&hauth.done=Twitter' . '>',
            ),
            'twitter_app_id'      => array(
                'name'  => 'twitter_app_id',
                'label' => __( 'Consumer Key', 'dokan-social-api' ),
                'type'  => 'text',
            ),
            'twitter_app_secret'  => array(
                'name'  => 'twitter_app_secret',
                'label' => __( 'Consumer Secret', 'dokan-social-api' ),
                'type'  => 'text',
            ),
            'google_app_label'    => array(
                'name'  => 'google_app_label',
                'label' => __( 'Google App Settings', 'dokan-social-api' ),
                'type'  => 'html',
                'desc'  => '<a target="_blank" href="https://console.developers.google.com/project">' . __( 'Create an App', 'dokan-social-api' ) . '</a> if you don\'t have one and fill Client ID and Secret below. <a href="https://wedevs.com/docs/dokan/settings/dokan-social-login/configuring-google/" target="_blank">Get Help</a>',
            ),
            'google_app_url'      => array(
                'name'  => 'google_app_url',
                'label' => __( 'Redirect URI', 'dokan-social-api' ),
                'type'  => 'html',
                'desc'  => "<input class='regular-text' type='text' disabled value=" . $this->base_url . '?dokan_reg=google&hauth.done=Google' . '>',
            ),
            'google_app_id'       => array(
                'name'  => 'google_app_id',
                'label' => __( 'Client ID', 'dokan-social-api' ),
                'type'  => 'text',
            ),
            'google_app_secret'   => array(
                'name'  => 'google_app_secret',
                'label' => __( 'Client secret', 'dokan-social-api' ),
                'type'  => 'text',
            ),
            'linkedin_app_label'  => array(
                'name'  => 'linkedin_app_label',
                'label' => __( 'Linkedin App Settings', 'dokan-social-api' ),
                'type'  => 'html',
                'desc'  => '<a target="_blank" href="https://www.linkedin.com/developer/apps">' . __( 'Create an App', 'dokan-social-api' ) . '</a> if you don\'t have one and fill Client ID and Secret below. <a href="https://wedevs.com/docs/dokan/settings/dokan-social-login/configuring-linkedin/" target="_blank">Get Help</a>',
            ),
            'linkedin_app_url'    => array(
                'name'  => 'linkedin_app_url',
                'label' => __( 'Redirect URL', 'dokan-social-api' ),
                'type'  => 'html',
                'desc'  => "<input class='regular-text' type='text' disabled value=" . $this->base_url . '?dokan_reg=linkedin&hauth.done=LinkedIn' . '>',
            ),
            'linkedin_app_id'     => array(
                'name'  => 'linkedin_app_id',
                'label' => __( 'Client ID', 'dokan-social-api' ),
                'type'  => 'text',
            ),
            'linkedin_app_secret' => array(
                'name'  => 'linkedin_app_secret',
                'label' => __( 'Client Secret', 'dokan-social-api' ),
                'type'  => 'text',
            ),
        );

        return $settings_fields;
    }

    /**
     * Register dokan query vars
     *
     * @since 1.0
     *
     * @param array $vars
     *
     * @return array new $vars
     */
    function register_support_queryvar( $vars ) {
        $vars[] = 'social-register';
        $vars[] = 'dokan-registration';

        return $vars;
    }

    /**
     * Register page templates
     *
     * @since 1.0
     *
     * @param array $query_vars
     *
     * @return array $query_vars
     */
    function load_template_from_plugin( $query_vars ) {

        if ( isset( $query_vars['dokan-registration'] ) ) {
            $template = DOKAN_PRO_DIR . '/templates/global/social-register.php';
            include $template;
        }
    }

    /**
     * Render social login icons
     *
     * @return void
     */
    public function render_social_logins() {
        $configured_providers = array();

        //facebook config from admin
        $fb_id     = dokan_get_option( 'fb_app_id', 'dokan_social_api' );
        $fb_secret = dokan_get_option( 'fb_app_secret', 'dokan_social_api' );
        if ( $fb_id != '' && $fb_secret != '' ) {
            $configured_providers [] = 'facebook';
        }
        //google config from admin
        $g_id     = dokan_get_option( 'google_app_id', 'dokan_social_api' );
        $g_secret = dokan_get_option( 'google_app_secret', 'dokan_social_api' );
        if ( $g_id != '' && $g_secret != '' ) {
            $configured_providers [] = 'google';
        }
        //linkedin config from admin
        $l_id     = dokan_get_option( 'linkedin_app_id', 'dokan_social_api' );
        $l_secret = dokan_get_option( 'linkedin_app_secret', 'dokan_social_api' );
        if ( $l_id != '' && $l_secret != '' ) {
            $configured_providers [] = 'linkedin';
        }
        //Twitter config from admin
        $twitter_id     = dokan_get_option( 'twitter_app_id', 'dokan_social_api' );
        $twitter_secret = dokan_get_option( 'twitter_app_secret', 'dokan_social_api' );
        if ( $twitter_id != '' && $twitter_secret != '' ) {
            $configured_providers [] = 'twitter';
        }

        /**
         * Filter the list of Providers connect links to display
         *
         * @since 1.0.0
         *
         * @param array $providers
         */
        $providers = apply_filters( 'dokan_social_provider_list', $configured_providers );

        $data = array(
            'base_url'  => $this->base_url,
            'providers' => $providers,
            'pro'       => true
        );

        dokan_get_template_part( 'global/social-registration', '', $data );
    }

    /**
     * Recursive function to generate a unique username.
     *
     * If the username already exists, will add a numerical suffix which will increase until a unique username is found.
     *
     * @param string $username
     *
     * @return string The unique username.
     */
    function generate_unique_username( $username ) {
        static $i;
        if ( null === $i ) {
            $i = 1;
        } else {
            $i++;
        }
        if ( !username_exists( $username ) ) {
            return $username;
        }
        $new_username = sprintf( '%s_%s', $username, $i );
        if ( !username_exists( $new_username ) ) {
            return $new_username;
        } else {
            return call_user_func( array( $this, 'generate_unique_username' ), $username );
        }
    }

    /**
     * Register a new user
     *
     * @param object $data
     *
     * @param string $provider
     *
     * @return void
     */
    private function register_new_user( $data, $provider ) {

        $userdata = array(
            'user_login' => $this->generate_unique_username( $data->displayName ),
            'user_email' => $data->email,
            'first_name' => $data->firstName,
            'last_name'  => $data->lastName,
            'role'       => 'customer',
        );

        $user_id = @wp_insert_user( $userdata );

        if ( !is_wp_error( $user_id ) ) {
            $this->login_user( get_userdata( $user_id ) );
            wp_redirect( $this->base_url );
            exit();
        }
    }

    /**
     * Log in existing users
     *
     * @param WP_User $wp_user
     *
     * return void
     */
    private function login_user( $wp_user ) {
        clean_user_cache( $wp_user->ID );
        wp_clear_auth_cookie();
        wp_set_current_user( $wp_user->ID );
        
        if ( is_ssl() == true ) {
            wp_set_auth_cookie( $wp_user->ID, true, true );
        } else {
            wp_set_auth_cookie( $wp_user->ID, true, false );
        }
        
        update_user_caches( $wp_user );
    }

}
