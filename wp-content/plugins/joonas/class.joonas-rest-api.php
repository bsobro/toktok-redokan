<?php

class Joonas_REST_API {
    /**
     * Register the routes for the objects of the controller.
     */
    public static function register_endpoints() {
        // endpoints will be registered here
        register_rest_route( 'candyshop/v1', '/candy', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( 'Joonas_REST_API', 'get_candy' ),
        ) );
        register_rest_route( 'candyshop/v1', '/candy', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array( 'Joonas_REST_API', 'create_candy' ),
        ) );
    }

    /**
     * Get all the candies
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public static function get_candy( $request ) {
        $data = get_posts( array(
            'post_type'      => 'candy',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
        ) );

        // @TODO do your magic here
        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Add a new candy
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public static function create_candy( $request ) {
        $params = $request->get_body_params();

        $post_id = wp_insert_post( array(
            'post_title'    => isset( $params['name']    ) ? $params['name'] : 'Untitled Candy',
            'post_content'  => isset( $params['details'] ) ? $params['details'] : '',
            'post_type'     => 'candy',
            'post_status'   => 'publish',
        ) );

        // @TODO do your magic here
        return new WP_REST_Response( $post_id, 200 );
    }
}
