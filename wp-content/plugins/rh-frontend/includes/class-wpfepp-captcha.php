<?php

/**
* 
*/
class WPFEPP_Captcha
{
	private $version;

	function __construct($version) {
		$this->version 		= $version;
		$this->options 		= get_option('wpfepp_recaptcha_settings', array('site_key' => '', 'secret' => ''));
	}

	public function keys_available(){
		return ($this->options['site_key'] && $this->options['secret']);
	}

	public function render(){

		if(!$this->keys_available()){
			if(current_user_can('manage_options'))
				echo __('You need to specify your ReCaptcha keys in the plugin settings for this captcha to work!', 'wpfepp-plugin');
			return;			
		}

		?>
			<div class="g-recaptcha" data-sitekey="<?php echo $this->options['site_key']; ?>" data-theme="<?php echo $this->options['theme']; ?>"></div>
			<?php wp_enqueue_script('wpfepp-recaptcha'); ?>

		<?php
	}

	public function check_response($response_string){

		if(!$this->keys_available())
			return;

		$url 		= 'https://www.google.com/recaptcha/api/siteverify?secret=' . $this->options['secret'] . '&response=' . $response_string . '&remoteip=' . $this->get_user_ip();
		$response 	= wp_remote_get( $url, array('timeout' => 10) );
		$body 		= wp_remote_retrieve_body($response);
		$body 		= json_decode($body);
		return $body->success;
	}

	private function get_user_ip(){
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}

?>