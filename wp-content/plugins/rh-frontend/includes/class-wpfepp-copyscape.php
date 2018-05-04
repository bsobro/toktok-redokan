<?php

/**
* 
*/
class WPFEPP_CopyScape
{
	private $version;

	private $options;

	public static $meta_key = 'wpfepp_copyscape_status';

	function __construct($version) {
		$this->version 		= $version;
		$this->options 		= get_option('wpfepp_copyscape_settings', array('username' => '', 'api_key' => ''));
	}

	public function add_actions(){
		$post_types = wpfepp_get_post_types();
		foreach ( $post_types as $key => $type ) {
			if( isset($this->options['column_types'][$type]) && $this->options['column_types'][$type] ){
				add_filter('manage_'. $type .'_posts_columns' , array($this, 'custom_columns'));
				add_filter('manage_'. $type .'_posts_custom_column' , array($this, 'custom_column'), 10, 2);
			}
		}
	}

	public function custom_columns($columns){
		return array_merge( $columns, array(self::$meta_key => 'CopyScape Status') );
	}

	public function custom_column($column, $post_id){
		if($column == self::$meta_key){
			echo ucfirst(get_post_meta($post_id, self::$meta_key, true));
		}
	}

	public function option($id){
		return $this->options[$id];
	}

	public function passed($post_data){
		$username 	= $this->options['username'];
		$key 		= $this->options['api_key'];

		if(!$username || !$key)
			return new WP_Error( 'copyscape_error', __('You need to specify your CopyScape API keys in the plugin settings', 'wpfepp-plugin') );

		$test_content = '';
		$test_content .= ( isset($post_data['title']) && $post_data['title'] ) ? $post_data['title'] : '';
		$test_content .= ( isset($post_data['content']) && $post_data['content'] ) ? $post_data['content'] : '';

		if(!$test_content)
			return true;

		$url =  'http://www.copyscape.com/api/';
		$response = wp_remote_post( $url, array(
				'timeout' => 50,
				'body' => array( 'u' => $username, 'k' => $key, 'o' => 'csearch', 'e' => 'UTF-8', 't' => $test_content )
		    )
		);

		$body = wp_remote_retrieve_body($response);
		$xml = simplexml_load_string($body);

		if(is_wp_error($response))
			return $response;

		if(isset($xml->error))
			return new WP_Error( 'copyscape_error', $xml->error );

		if(intval($xml->count))
			return false;

		return true;
	}
}

?>