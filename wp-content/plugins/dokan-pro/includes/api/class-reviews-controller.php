<?php

/**
* Reviews API controller
*
* @since 2.8.0
*
* @package dokan
*/
class Dokan_REST_Reviews_Controller extends Dokan_REST_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'reviews';



    /**
     * Register all routes related with coupons
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_reviews' ),
                'permission_callback' => array( $this, 'get_reviews_permission_check' ),
                'args'                => $this->get_collection_params(),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/summary', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_reviews_summary' ),
                'permission_callback' => array( $this, 'check_reviews_summary_permission' ),
                'args'                => $this->get_collection_params(),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)', array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_review_status' ),
                'permission_callback' => array( $this, 'manage_reviews_permission_check' ),
                'args'                =>  array(
                    'status' => array(
                        'description' => __( 'Review Status', 'dokan' ),
                        'required'    => true,
                        'type'        => 'string',
                    ),
                ),
            ),
        ) );
    }

    /**
     * Get reviews permissions
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_reviews_permission_check() {
        return current_user_can( 'dokan_view_review_menu' );
    }

    /**
     * Get reviews permissions
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function manage_reviews_permission_check() {
        return current_user_can( 'dokan_manage_reviews' );
    }

    /**
     * Get reviews permissions
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_reviews_summary_permission() {
        return current_user_can( 'dokan_view_review_reports' );
    }

    /**
     * Get all reviews
     *
     * @since 2.8.0
     *
     * @return object
     */
    public function get_reviews( $request ) {
        $store_id = dokan_get_current_user_id();
        $review_class = new Dokan_Pro_Reviews();

        if ( empty( $store_id ) ) {
            return new WP_Error( 'no_store_found', __( 'No seller found', 'dokan' ), array( 'status' => 404 ) );
        }

        $limit = $request['per_page'];
        $offset = ( $request['page'] - 1 ) * $request['per_page'];

        $status = $this->get_status( $request );
        $comments = $review_class->comment_query( $store_id, 'product', $limit, $status, $offset );

        $count = $this->get_total_count( $status );

        $data = array();
        foreach ( $comments as $comment ) {
            $data[] = $this->prepare_item_for_response( $comment, $request );
        }

        $response = rest_ensure_response( $data );
        $response = $this->format_collection_response( $response, $request, $count );
        return $response;
    }

    /**
     * Manaage reviews
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function update_review_status( $request ) {
        $store_id = dokan_get_current_user_id();
        $review_class = new Dokan_Pro_Reviews();

        if ( empty( $store_id ) ) {
            return new WP_Error( 'no_store_found', __( 'No seller found', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( empty( $request['id'] ) ) {
            return new WP_Error( 'no_reivew_found', __( 'No review id found', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( empty( $request['status'] ) ) {
            return new WP_Error( 'no_reivew_status_found', __( 'No review status found for updating review', 'dokan' ), array( 'status' => 404 ) );
        }

        $status = $this->get_status( $request );

        $comment_id  = $request['id'];

        if ( isset( $comment_id ) ) {
            wp_set_comment_status( $comment_id, $status );
        }

        $comment = get_comment( $comment_id );
        $data = $this->prepare_item_for_response( $comment, $request );

        return rest_ensure_response( $data );
    }

    /**
     * Get review status
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_status( $request ) {
        $status = isset( $request['status'] ) ? $request['status'] : '';

        if ( $status == 'hold' ) {
            return '0';
        } else if ( $status == 'spam' ) {
            return 'spam';
        } else if ( $status == 'trash' ) {
            return 'trash';
        } else {
            return '1';
        }
    }

    /**
     * Get total count of comment
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_total_count( $status ) {
        global $wpdb;
        $user_id = dokan_get_current_user_id();

        $total = $wpdb->get_var(
            "SELECT COUNT(*)
            FROM $wpdb->comments, $wpdb->posts
            WHERE   $wpdb->posts.post_author='$user_id' AND
            $wpdb->posts.post_status='publish' AND
            $wpdb->comments.comment_post_ID=$wpdb->posts.ID AND
            $wpdb->comments.comment_approved='$status' AND
            $wpdb->posts.post_type='product'"
        );

        return $total;
    }

    /**
     * Get review summary
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_reviews_summary( $request ) {
        $seller_id = dokan_get_current_user_id();

        $data = array(
            'comment_counts' => dokan_count_comments( 'product', $seller_id ),
            'reviews_url'    => dokan_get_navigation_url( 'reviews' ),
        );

        return rest_ensure_response( $data );
    }

    /**
     * Prepare a single product review output for response.
     *
     * @param WP_Comment $review Product review object.
     * @param WP_REST_Request $request Request object.
     *
     * @return WP_REST_Response $response Response data.
     */
    public function prepare_item_for_response( $review, $request ) {
        $data = array(
            'id'               => (int) $review->comment_ID,
            'date_created'     => wc_rest_prepare_date_response( $review->comment_date ),
            'review'           => $review->comment_content,
            'rating'           => (int) get_comment_meta( $review->comment_ID, 'rating', true ),
            'name'             => $review->comment_author,
            'email'            => $review->comment_author_email,
            'verified'         => wc_review_is_from_verified_owner( $review->comment_ID ),
        );

        $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
        $data    = $this->add_additional_fields_to_object( $data, $request );
        $data    = $this->filter_response_by_context( $data, $context );

        return apply_filters( 'woocommerce_rest_prepare_product_review', $data, $review, $request );
    }

    /**
     * Get the Product Review's schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'product_review',
            'type'       => 'object',
            'properties' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'review' => array(
                    'description' => __( 'The content of the review.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'date_created' => array(
                    'description' => __( "The date the review was created, in the site's timezone.", 'woocommerce' ),
                    'type'        => 'date-time',
                    'context'     => array( 'view', 'edit' ),
                ),
                'date_created_gmt' => array(
                    'description' => __( "The date the review was created, as GMT.", 'woocommerce' ),
                    'type'        => 'date-time',
                    'context'     => array( 'view', 'edit' ),
                ),
                'rating' => array(
                    'description' => __( 'Review rating (0 to 5).', 'woocommerce' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                ),
                'name' => array(
                    'description' => __( 'Reviewer name.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'email' => array(
                    'description' => __( 'Reviewer email.', 'woocommerce' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'verified' => array(
                    'description' => __( 'Shows if the reviewer bought the product or not.', 'woocommerce' ),
                    'type'        => 'boolean',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
            ),
        );

        return $this->add_additional_fields_schema( $schema );
    }

}