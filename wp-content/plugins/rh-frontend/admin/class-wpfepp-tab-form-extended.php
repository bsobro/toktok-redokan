<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

/**
 * Manages the form extended tab of the forms page.
 *
 * @package WPFEPP
 * @since 2.3.0
 **/
class WPFEPP_Tab_Form_Extended extends WPFEPP_Tab
{
	/**
	 * An instance of the form manager class, responsible for instantiating this tab.
	 *
	 * @var WPFEPP_Form_Manager
	 **/
	private $form_manager;

	/**
	 * Class constructor. Initializes class variables and calls parent constructor.
	 *
	 * @var string $version Plugin version.
	 * @var string $slug Tab slug.
	 * @var string $name Tab name.
	 * @var string $form_manager An instance of the form manager class.
	 **/
	function __construct($version, $slug, $name, $form_manager) {
		$this->form_manager = $form_manager;
		parent::__construct($version, $slug, $name);
		
	}

	/**
	 * Registers the actions of this class with WordPress. This function is called by add_actions of WPFEPP_Tab_Collection, which in turn is called by add_actions of WPFEPP_Form_Manager.
	 **/
	public function add_actions(){
		add_action( 'admin_init', array( $this, 'save_extended' ) );
		add_action( 'wpfepp_extended_form_settings', array( $this, 'admin_enqueue_location_scripts' ), 0 );
	}

	/**
	 * When users hit the submit button this function handles the request and redirects them back to the page.
	 **/
	public function save_extended(){
		if(!$this->form_manager->is_page())
			return;

		$result = 0;

		if( $_GET['action'] == 'edit' && isset($_POST['update-form-extended']) && isset($_POST['form_extended']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpfepp-update-form-extended') ){
			// get Google Map coordinates to save them to database as part of $extended array
			$_POST['form_extended']['start_geo_lat']['val'] = isset( $_POST['wpfepp_start_geo_lat'] ) ? $_POST['wpfepp_start_geo_lat'] : '';
			$_POST['form_extended']['start_geo_lat']['type'] = 'text';
			$_POST['form_extended']['start_geo_long']['val'] = isset( $_POST['wpfepp_start_geo_long'] ) ? $_POST['wpfepp_start_geo_long'] : '';
			$_POST['form_extended']['start_geo_long']['type'] = 'text';
			
			$form_extended 	= $this->validator->validate($_POST['form_extended']);
			$result = $this->db->update_form_extended($_GET['form'], $form_extended);
			$sendback = esc_url_raw( add_query_arg( array( 'updated' => $result ) ) );
			wp_redirect( $sendback );
		}
	}

/**
 * Get current extended form settings.
 *
 * @return An array of saved extended form settings. 
 */
	private function get_extended_settings() {
		
		if ( isset( $_GET['form'] ) && $_GET['form'] !=='' ) {
			$form_id = $_GET['form'];
			$form = $this->db->get( $form_id );
			$form_extended 	= $form['extended'];
			return $form_extended;		
		}
	}
	
	/**
	 * Outputs the contents of the tab with the help of WordPress' settings API.
	 **/
	public function display() {
		$form_extended = $this->get_extended_settings();

		$page = 'wpfepp_form_extended_tab';
		$section1 = 'wpfepp_form_googlemap_section';
		$callback = array( $this->renderer, 'render' );
		$args = array( 'group' => 'form_extended', 'curr' => $form_extended );

		add_settings_section( 'wpfepp_form_limitnumber_section', __("Limit post submit for form", "wpfepp-plugin"), null, $page);

	    add_settings_field(
	        'limit_number', __('Limit number', 'wpfepp-plugin'), $callback, $page, 'wpfepp_form_limitnumber_section',
		    array_merge(
				array(
					'desc' 	=> __( 'If you set this field, user will have ability to post only this number of posts from this form. 0 is unlimited', 'wpfepp-plugin' ),
					'id' 	=> 'limit_number',
					'type' 	=> 'number',
				), $args )
	    );

	    add_settings_field(
	        'limit_number_message', __('Add message which user will see after reach the limits', 'wpfepp-plugin'), $callback, $page, 'wpfepp_form_limitnumber_section',
		    array_merge(
				array(
					'desc' 	=> __( 'Shortcodes are also allowed', 'wpfepp-plugin' ),
					'id' 	=> 'limit_number_message',
					'type' 	=> 'textarea',
				), $args )
	    );

	    add_settings_field(
	        'limit_number_redirect', __('Redirect after reach limit', 'wpfepp-plugin'), $callback, $page, 'wpfepp_form_limitnumber_section',
		    array_merge(
				array(
					'desc' 	=> __('Use this field to add link where you want to redirect user who reached limit. Previous field will not work if you set redirect link', 'wpfepp-plugin'),
					'id' 	=> 'limit_number_redirect',
					'type' 	=> 'text',
					'size' => 50
				), $args )
	    );	    

	    add_settings_field(
	        'pre_limit_message', __('Add message which user will see before reach the limit', 'wpfepp-plugin'), $callback, $page, 'wpfepp_form_limitnumber_section',
		    array_merge(
				array(
					'desc' 	=> __( '%%count%% will be replaced by number of available limit. Example: You have %%count%% available submissions', 'wpfepp-plugin' ),
					'id' 	=> 'pre_limit_message',
					'type' 	=> 'textarea',
				), $args )
	    );	    	    		

		add_settings_section( $section1, __("Google Map settings", "wpfepp-plugin"), null, $page);

		add_settings_field(
		    'wpfepp_enable_city_suggest', __('Location Icon finder', 'wpfepp-plugin'), $callback, $page, $section1,
		    array_merge(
				array(
					'desc' 	=> __('When checked the user will be able to detect its location automatically. Function works only with HTTPS.', 'wpfepp-plugin'),
					'id' 	=> 'enable_city_suggest',
					'type' 	=> 'bool'
				), $args )
		);
	    add_settings_field(
	        'wpfepp_enable_map', __('Display Map', 'wpfepp-plugin'), $callback, $page, $section1,
		    array_merge(
				array(
					'desc' 	=> __('When checked there will be a small Google Map positioned beneath the location field.', 'wpfepp-plugin'),
					'id' 	=> 'enable_map',
					'type' 	=> 'bool'
				), $args )
	    );
	    add_settings_field(
	        'wpfepp_map_placeholder', __('Placeholder Address', 'wpfepp-plugin'), $callback, $page, $section1,
		    array_merge(
				array(
					'desc' 	=> __('Set a placeholder to the address field.', 'wpfepp-plugin'),
					'id' 	=> 'adress_placeholder',
					'type' 	=> 'text',
					'size' => 50
				), $args )
	    );
	    add_settings_field(
	        'wpfepp_map_start_location', __('Default Location', 'wpfepp-plugin'), $callback, $page, $section1,
		    array_merge(
				array(
					'desc' 	=> __('The start location if the map is enabled', 'wpfepp-plugin'),
					'id' 	=> 'map_start_location',
					'type' 	=> 'text',
					'size' => 50
				), $args )
	    );
	    add_settings_field(
	        'wpfepp_map_google_key', __('Google Map API key', 'wpfepp-plugin'), $callback, $page, $section1,
		    array_merge(
				array(
					'desc' 	=> __('To show a map in the form you need to obtain your Goole API key. <a href="//docs.gravitygeolocation.com/article/101-create-google-map-api-key" target="_blank">Learn how.</a>.', 'wpfepp-plugin'),
					'id' 	=> 'map_google_key',
					'type' 	=> 'text',
					'size' => 50
				), $args )
	    );
	    add_settings_field(
	        'wpfepp_map_google_country', __('Country Code', 'wpfepp-plugin'), $callback, $page, $section1,
		    array_merge(
				array(
					'desc' 	=> __('Enter the default country code. <a href="https://wikipedia.org/wiki/ISO_3166-1" target="blank">List of country codes</a>.', 'wpfepp-plugin'),
					'id' 	=> 'map_google_country',
					'type' 	=> 'text',
					'size' => 10
				), $args )
	    );
	    add_settings_field(
	        'wpfepp_map_google_lang', __('Language Code', 'wpfepp-plugin'), $callback, $page, $section1,
		    array_merge(
				array(
					'desc' 	=> __( 'Set the language to be used with Google Places address auto-complete. <a href="https://wikipedia.org/wiki/List_of_ISO_639-1_codes" target="blank">List of language codes</a>.', 'wpfepp-plugin' ),
					'id' 	=> 'map_google_lang',
					'type' 	=> 'text',
					'size' => 10
				), $args )
	    );

	    add_settings_section( 'wpfepp_paid_help_section', __('Payment Settings', 'wpfepp-plugin'), array($this, 'paidhelper_callback'), $page );
		
		do_action( 'wpfepp_extended_form_settings', $page, $callback, $args );

	    ?>
			<form method="POST">
				<?php do_settings_sections( $page ); ?>
				<?php wp_nonce_field( 'wpfepp-update-form-extended', '_wpnonce', false, true ); ?>
				<?php submit_button(__('Save Settings', 'wpfepp-plugin'), 'primary', 'update-form-extended'); ?>
			</form>
			
	    <?php 
	}
	
	/**
	 * Admin location scripts.
	 *
	 * Enqueue admin styles and javascripts.
	 *
	 * @since 1.0.2
	 */
 	public function admin_enqueue_location_scripts() {
		$form_extended = $this->get_extended_settings();
		$default_extended = $this->form_manager->get_defaults();
		wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?v=3.exp&amp;'. $form_extended['map_google_key'] .'&amp;libraries=places' );
		wp_enqueue_script( 'mapify', plugins_url( '/static/js/mapify.js', dirname(__FILE__) ), array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'admin-location', plugins_url( '/static/js/admin-location.js', dirname(__FILE__) ), array( 'jquery', 'mapify' ), $this->version, true );	
		wp_localize_script( 'admin-location', 'wpfeppl', array(
			'start_point'		=> $form_extended['map_start_location'],
			'start_geo_lat'		=> isset( $form_extended['start_geo_lat'] ) ? $form_extended['start_geo_lat'] : $default_extended['extended']['start_geo_lat'],
			'start_geo_long'	=> isset( $form_extended['start_geo_long'] ) ? $form_extended['start_geo_long'] : $default_extended['extended']['start_geo_long'],
			'enable_city_suggest' => $form_extended['enable_city_suggest'],
			'enable_map' 		=> $form_extended['enable_map'],
			'l10n'              => array(
				'locked'    => __( 'Lock Pin Location', 'wpfepp-plugin' ),
				'unlocked'  => __( 'Unlock Pin Location', 'wpfepp-plugin' )
			)
		) );
	}

	public function paidhelper_callback($args){
		$form_id = $_GET['form'];
		?>
			<p class="description"><?php printf( __('To use paid sumbission for posting, you must have installed plugin WooCommerce. You must enable payment option in RH Frontend PRO - Settings - Payment. <br />After setup payment packages, use shortode<br /><br /><code>[wpfepp_submission_form form="%s" paid="1"]</code><br /><br />to add payment option to form.', 'wpfepp-plugin'), $form_id); ?></p>
		<?php	
	}	
	
}