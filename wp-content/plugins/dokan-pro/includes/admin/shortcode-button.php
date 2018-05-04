<?php

/**
 * Dokan tinyMce Shortcode Button class
 *
 * @since 2.4.12
 */
class Dokan_shortcodes_button {

    /**
     * Constructor for shortcode class
     */
    public function __construct() {

        add_filter( 'mce_external_plugins',  array( $this, 'enqueue_plugin_scripts' ) );
        add_filter( 'mce_buttons',  array( $this, 'register_buttons_editor' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'localize_shortcodes' ) , 90  );
    }

    /**
     * Generate shortcode array
     *
     * @since 2.4.12
     *
     */
    function localize_shortcodes() {

        $shortcodes = array(
            'dokan-dashboard'            => array(
                'title'   => 'Dokan Dasboard',
                'content' => '[dokan-dashboard]'
            ),
            'dokan-stores'               => array(
                'title'   => 'Stores List',
                'content' => '[dokan-stores]'
            ),
            'dokan-best-selling-product' => array(
                'title'   => 'Best Selling Product',
                'content' => '[dokan-best-selling-product no_of_product="5" seller_id="" ]'
            ),
            'dokan-top-rated-product'    => array(
                'title'   => 'Top Rated Product',
                'content' => '[dokan-top-rated-product]'
            ),
            'dokan-my-orders'            => array(
                'title'   => 'Dokan My Orders',
                'content' => '[dokan-my-orders]'
            ),
            'dokan-customer-migration'   => array(
                'title'   => 'Dokan Customer Migration',
                'content' => '[dokan-customer-migration]'
            )
        );

        $assets_url = DOKAN_PRO_PLUGIN_ASSEST;

        wp_localize_script( 'dokan_slider_admin', 'dokan_shortcodes', apply_filters( 'dokan_button_shortcodes', $shortcodes ) );
        wp_localize_script( 'dokan_slider_admin', 'dokan_assets_url', $assets_url );
    }

    /**
     * * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_shortcodes_button();
        }

        return $instance;
    }

    /**
     * Add button on Post Editor
     *
     * @since 2.4.12
     *
     * @param array $plugin_array
     *
     * @return array
     */
    function enqueue_plugin_scripts( $plugin_array ) {
        //enqueue TinyMCE plugin script with its ID.
        $plugin_array["dokan_button"] =  DOKAN_PRO_PLUGIN_ASSEST . "/js/dokan-tmc-button.js";
        return $plugin_array;
    }

    /**
     * Register tinyMce button
     *
     * @since 2.4.12
     *
     * @param array $buttons
     *
     * @return array
     */
    function register_buttons_editor( $buttons ) {
        //register buttons with their id.
        array_push( $buttons, "dokan_button" );

        return $buttons;
    }

}

Dokan_shortcodes_button::init();
