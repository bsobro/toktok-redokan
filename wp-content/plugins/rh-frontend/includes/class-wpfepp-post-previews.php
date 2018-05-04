<?php

class WPFEPP_Post_Previews {
	public static $nonce_name = '_preview_nonce';

	function add_actions(){
		add_filter( 'query_vars', array( $this, 'add_query_var' ) );
		add_filter( 'pre_get_posts', array( $this, 'show_public_preview' ) );
		add_filter( 'wpseo_whitelist_permalink_vars', array( $this, 'add_query_var' ) );
	}

	public function add_query_var( $qv ) {
		$qv[] = self::$nonce_name;

		return $qv;
	}

	public static function make_preview_link( $post_id ) {
		$nonce = wp_create_nonce( 'wpfepp-post-preview-' . $post_id );
		if( get_post_type( $post_id  ) != 'product' ) {
			return sprintf( '%s?p=%s&preview=true&%s=%s', home_url(), $post_id, self::$nonce_name, $nonce );
		} else {
			return sprintf( '%s?post_type=product&p=%s&preview=true&%s=%s', home_url(), $post_id, self::$nonce_name, $nonce );
		}
	}

	public function show_public_preview( $query ) {
		if (
			$query->is_main_query() &&
			$query->is_preview() 	&&
			$query->is_singular() 	&&
			$query->get( self::$nonce_name )
		)

		add_filter( 'posts_results', array( $this, 'set_post_to_publish' ), 10, 2 );

		return $query;
	}

	public function set_post_to_publish( $posts ) {
		// Remove the filter again, otherwise it will be applied to other queries too.
		remove_filter( 'posts_results', array( $this, 'set_post_to_publish' ), 10, 2 );

		if ( empty( $posts ) )
			return;

		$post_id = $posts[0]->ID;

		// If the post has gone live, redirect to it's proper permalink
		if ( 'publish' == get_post_status( $post_id ) ){
			wp_redirect( get_permalink( $post_id ), 301 );
			exit;
		}

		if ( wp_verify_nonce( get_query_var( self::$nonce_name ), 'wpfepp-post-preview-' . $post_id ) )
			$posts[0]->post_status = 'publish';

		return $posts;
	}
}

?>