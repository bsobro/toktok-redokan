<?php

/**
 * A class that handles the ajax calls made by the submission form.
 * 
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Form_Ajax
{
	/**
	 * Class constructor. Loads the file of WPFEPP_Form class.
	 **/
	public function __construct( $version ){
		$this->version = $version;
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-form.php';
	}

	/**
	 * Adds ajax actions using wp_ajax_* hooks. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_action( 'wp_ajax_wpfepp_handle_submission_ajax', array($this, 'handle_submission_ajax') );
		add_action( 'wp_ajax_wpfepp_get_thumbnail', array($this, 'get_thumbnail') );
		add_action( 'wp_ajax_wpfepp_get_parser_thumbnail', array($this, 'get_parser_thumbnail') );
	}

	/**
	 * Ajax function that processes data submitted by users and prints out the appropriate response as a json encoded string.
	 *
	 * @param array The $_POST array.
	 * @return string A json encoded string.
	 **/
	public function handle_submission_ajax(){
		$form_id 	= $_POST['form_id'];
		$form 		= new WPFEPP_Form($this->version, $form_id, false);
		
		if( $_POST['req_type'] == 'submit' ){
			$result 	= $form->handle_submission($_POST);
		}
		elseif( $_POST['req_type'] == 'save' ){
			$result 	= $form->save_draft($_POST);
		}
		die(json_encode($result));
	}

	/**
	 * Ajax function that prints out the HTML of a thumbnail. Used when the user selects a featured image.
	 *
	 * @param array The $_POST array.
	 * @return string HTML source of a thumbnail.
	 **/
	public function get_thumbnail(){
		$image_id = $_POST['id'];
		ob_start();
		echo wp_get_attachment_image( $image_id, array(200,200) );
		$image = ob_get_clean();
		$return_val = array('success'=>true, 'image'=>$image);
		die(json_encode($return_val));
	}
	
	/**
	 * Ajax image parser function
	 *
	 * @param array The $_POST array.
	 * @return JSON object/array with urls of parsered images.
	**/
	public function get_parser_thumbnail(){
		if( empty( $_POST['ext_url']) ) {
			$return_val = array( 'success'=>true, 'pictures'=>'no_url' );
		} else {
			$host = parse_url( $_POST['ext_url'], PHP_URL_HOST );
			$data_attr = array( 'data-src', 'data-zoom-url', 'data-old-hires', 'data-original', 'src' );
			$args = array( 
				'timeout' => 30,
				'httpversion' => '1.1',
				'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36'
				);
				
			$response = wp_safe_remote_get( $_POST['ext_url'], $args );
			
			if( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == '200' ) {	
				$body_response = wp_remote_retrieve_body( $response );
				$doc = new DOMDocument();
				libxml_use_internal_errors(true);
				$doc->loadHTML( $body_response );
				libxml_clear_errors();
				$tags = $doc->getElementsByTagName( 'img' );
				$images = array();
				$i = 0;
				foreach ( $tags as $tag ) {
					for( $k = 0; $k < count( $data_attr ); $k++ ) {
						$src = $tag->getAttribute( $data_attr[$k] );
						if( !empty( $src ) )
							break;
					}
					if( 0 === strpos($src, '//') && false === strpos($src, '://') )
						$src = 'https:' . $src;
					if( false === strpos($src, 'https:') && false === strpos($src, 'http:') && false === strpos($src, $host) )
						$src = 'https://'. $host .'/'. $src;
					if( preg_match('/\.png|\.jpg|\.jpeg/i', $src) && !preg_match('/ebaystatic|sprites/i', $src) ) {
						if(preg_match('/(ebayimg).+(s-l300)/', $src)) $src = str_replace('s-l300', 's-l500', $src); // substitutes ebay image url with a width of 300px to 500px
						// preg_replace('/^(.+?)(\?.*?)?(#.*)?$/', '$1$3', $src); // deletes GET parameter
						$images[] = $src; 
					}
					$i++;
					if( $i == 99 )
						break;
				}
				$return_val = array( 'success'=>true, 'pictures'=>$images );
			} else {
				$return_val = is_wp_error( $response ) ? array( 'errors'=> $response->get_error_message() ) : $response['response'];
			}
		}
		die( json_encode( $return_val ) );
	}
}

?>