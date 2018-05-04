<?php

/**
 * Dokan Pro Template Settings class
 *
 * @since 2.4
 *
 * @package dokan
 */
class Dokan_Pro_Settings extends Dokan_Template_Settings {

    /**
     * Load autometically when class initiate
     *
     * @since 2.4
     *
     * @uses actions hook
     * @uses filter hook
     *
     * @return void
     */
    public function __construct() {
        $this->currentuser = dokan_get_current_user_id();
        $this->profile_info = dokan_get_store_info( dokan_get_current_user_id() );

        add_filter( 'dokan_get_dashboard_settings_nav', array( $this, 'load_settings_menu' ), 10 );
        add_filter( 'dokan_dashboard_settings_heading_title', array( $this, 'load_settings_header' ), 10, 2 );
        add_filter( 'dokan_dashboard_settings_helper_text', array( $this, 'load_settings_helper_text' ), 10, 2 );

        add_action( 'dokan_ajax_settings_response', array( $this, 'add_progressbar_in_settings_save_response' ), 10 );
        add_action( 'dokan_settings_load_ajax_response', array( $this, 'render_pro_settings_load_progressbar' ), 25 );
        add_action( 'dokan_settings_render_profile_progressbar', array( $this, 'load_settings_progressbar' ), 10, 2 );
        add_action( 'dokan_settings_content_area_header', array( $this, 'render_shipping_status_message' ), 25 );
        add_action( 'dokan_render_settings_content', array( $this, 'load_settings_content' ), 10 );
        add_action( 'dokan_settings_form_bottom', array( $this, 'add_discount_option' ), 10, 2 );
        add_action( 'dokan_store_profile_saved', array( $this, 'save_store_discount_data' ), 10, 2 );
    }

    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {

        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Settings();
        }

        return $instance;
    }


    /**
     * Load Settings Menu for Pro
     *
     * @since 2.4
     *
     * @param  array $sub_settins
     *
     * @return array
     */
    public function load_settings_menu( $sub_settins ) {

        $dokan_shipping_option = get_option( 'woocommerce_dokan_product_shipping_settings' );
        $enable_shipping       = ( isset( $dokan_shipping_option['enabled'] ) ) ? $dokan_shipping_option['enabled'] : 'yes';

        if ( $enable_shipping == 'yes' ) {
            $sub_settins['shipping'] = array(
                'title'      => __( 'Shipping', 'dokan' ),
                'icon'       => '<i class="fa fa-truck"></i>',
                'url'        => dokan_get_navigation_url( 'settings/shipping' ),
                'pos'        => 70,
                'permission' => 'dokan_view_store_shipping_menu'
            );
        }

        $sub_settins['social'] = array(
            'title'      => __( 'Social Profile', 'dokan' ),
            'icon'       => '<i class="fa fa-share-alt-square"></i>',
            'url'        => dokan_get_navigation_url( 'settings/social' ),
            'pos'        => 90,
            'permission' => 'dokan_view_store_social_menu'
        );

        if ( dokan_get_option( 'store_seo', 'dokan_general', 'on' ) === 'on' ) {
            $sub_settins['seo'] = array(
                'title'      => __( 'Store SEO', 'dokan' ),
                'icon'       => '<i class="fa fa-globe"></i>',
                'url'        => dokan_get_navigation_url( 'settings/seo' ),
                'pos'        => 110,
                'permission' => 'dokan_view_store_seo_menu'
            );
        }

        return $sub_settins;
    }

    /**
     * Load Settings Template
     *
     * @since 2.4
     *
     * @param  string $template
     * @param  array $query_vars
     *
     * @return void
     */
    public function load_settings_template( $template, $query_vars ) {

        if ( $query_vars == 'social' ) {
            dokan_get_template_part( 'settings/store' );
            return;
        }

        if ( $query_vars == 'shipping' ) {
            dokan_get_template_part( 'settings/store' );
            return;
        }

        if ( $query_vars == 'seo' ) {
            dokan_get_template_part( 'settings/store' );
            return;
        }
    }

    /**
     * Load Settings Header
     *
     * @since 2.4
     *
     * @param  string $header
     * @param  array $query_vars
     *
     * @return string
     */
    public function load_settings_header( $header, $query_vars ) {
        if ( $query_vars == 'social' ) {
            $header = __( 'Social Profiles', 'dokan' );
        }

        if ( $query_vars == 'shipping' ) {
            $header = __( 'Shipping Settings', 'dokan' );
        }

        if ( $query_vars == 'seo' ) {
            $header = __( 'Store SEO', 'dokan' );
        }

        return $header;
    }

    /**
     * Load Settings Progressbar
     *
     * @since 2.4
     *
     * @param  $array $query_vars
     *
     * @return void
     */
    public function render_pro_settings_load_progressbar() {
        global $wp;

        if ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'store' ) {
            echo dokan_get_profile_progressbar();
        }

        if ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'payment' ) {
            echo dokan_get_profile_progressbar();
        }

        if ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'social' ) {
            echo dokan_get_profile_progressbar();
        }

    }

    /**
     * Add progressbar in settings save feedback message
     *
     * @since 2.4
     *
     * @param array $message
     *
     * @return array
     */
    public function add_progressbar_in_settings_save_response( $message ) {
        $progress_bar = dokan_get_profile_progressbar();
        $message['progress'] = $progress_bar;

        return $message;
    }

    /**
     * Load Settings page helper
     *
     * @since 2.4
     *
     * @param  string $help_text
     * @param  array $query_vars
     *
     * @return string
     */
    public function load_settings_helper_text( $help_text, $query_vars ) {

        if ( $query_vars == 'social' ) {
            $help_text = __( 'Social profiles help you to gain more trust. Consider adding your social profile links for better user interaction.', 'dokan' );
        }

        if ( $query_vars == 'shipping' ) {

            $help_text = sprintf ( '<p>%s</p><p>%s</p>',
                __( 'This page contains your store-wide shipping settings, costs, shipping and refund policy.', 'dokan' ),
                __( 'You can enable/disable shipping for your products. Also you can override these shipping costs while creating or editing a product.', 'dokan' )
            );
        }

        return $help_text;
    }

    /**
     * Load Settings Content
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return void
     */
    public function load_settings_content( $query_vars ) {

        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'social' ) {
             if ( ! current_user_can( 'dokan_view_store_social_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan-lite' ) ) );
            } else {
                $this->load_social_content();
            }
        }

        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'shipping' ) {
             if ( ! current_user_can( 'dokan_view_store_shipping_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan-lite' ) ) );
            } else {
                $this->load_shipping_content();
            }
        }

        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'seo' ) {
             if ( ! current_user_can( 'dokan_view_store_seo_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan-lite' ) ) );
            } else {
                $this->load_seo_content();
            }
        }
    }

    /**
     * Load Social Page Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_social_content() {
        $social_fields = dokan_get_social_profile_fields();

        dokan_get_template_part( 'settings/social', '', array(
            'pro'           => true,
            'social_fields' => $social_fields,
            'current_user'  => $this->currentuser,
            'profile_info'  => $this->profile_info,
        ) );
    }

    /**
     * Load Shipping Page Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_shipping_content() {
        dokan_get_template_part( 'settings/shipping', '', array( 'pro' => true ) );
    }

    /**
     * Render Shipping status message
     *
     * @since 2.4
     *
     * @return void
     */
    public function render_shipping_status_message() {
        if ( isset( $_GET['message'] ) && $_GET['message'] == 'shipping_saved' ) {
            dokan_get_template_part( 'global/dokan-message', '', array(
                'message' => __( 'Shipping options saved successfully', 'dokan' )
            ) );
        }
    }

    /**
     * Load SEO Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_seo_content() {
        dokan_get_template_part( 'settings/seo', '', array( 'pro' => true ) );
    }

    /**
    * Render discount options
    *
    * @since 2.6
    *
    * @return void
    **/
    public function add_discount_option( $current_user, $profile_info ) {
        $is_enable_op_discount = dokan_get_option( 'discount_edit', 'dokan_selling' );
        $is_enable_op_discount = $is_enable_op_discount ? $is_enable_op_discount : array();
        $is_enable_order_discount = isset( $profile_info['show_min_order_discount'] ) ? $profile_info['show_min_order_discount'] : 'no';
        $setting_minimum_order_amount = isset( $profile_info['setting_minimum_order_amount'] ) ? $profile_info['setting_minimum_order_amount'] : '';
        $setting_order_percentage = isset( $profile_info['setting_order_percentage'] ) ? $profile_info['setting_order_percentage'] : '';

        dokan_get_template_part( 'settings/discount', '', array(
            'pro'                          => true,
            'is_enable_op_discount'        => $is_enable_op_discount,
            'is_enable_order_discount'     => $is_enable_order_discount,
            'setting_minimum_order_amount' => $setting_minimum_order_amount,
            'setting_order_percentage'     => $setting_order_percentage
        ) );
    }

    /**
    * Save doscount settings data
    *
    * @since 2.6
    *
    * @return void
    **/
    public function save_store_discount_data( $store_id, $dokan_settings ) {
        if ( ! $store_id ) {
            return;
        }

        $data = array(
            'show_min_order_discount'      => isset( $_POST['setting_show_minimum_order_discount_option'] ) ? 'yes' : 'no',
            'setting_minimum_order_amount' => isset( $_POST['setting_minimum_order_amount'] ) ? sanitize_text_field( $_POST['setting_minimum_order_amount'] ) : '',
            'setting_order_percentage'     => isset( $_POST['setting_order_percentage'] ) ? sanitize_text_field( $_POST['setting_order_percentage'] ) : '',
        );

        $settings_data = wp_parse_args( $data, $dokan_settings );

        update_user_meta( $store_id, 'dokan_profile_settings', $settings_data );
    }

}