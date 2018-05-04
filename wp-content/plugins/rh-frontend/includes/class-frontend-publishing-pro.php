<?php

/**
 * The main class of the plugin. Responsible for running initialization functions and pulling the whole plugin together.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class Frontend_Publishing_Pro
{
	/**
	 * Plugin version. This number is passed to all the other classes and is used while enqueuing scripts and styles.
	 *
	 * @access private
	 *
	 * @var string
	 **/
	private $version;

	/**
	 * An instance of WPFEPP_Loader with which actions of all classes will be registered. The loader will then registers these actions with WordPress.
	 * 
	 * @access private
	 *
	 * @var WPFEPP_Loader
	 **/
	private $loader;

	/**
	 * An array of media settings saved by the user.
	 * 
	 * @access private
	 *
	 * @var array
	 **/
	private $media_settings;

	/**
	 * An instance of our WPFEPP_Shortcode_Manager class
	 *
	 * @var WPFEPP_Shortcode_Manager
	 **/
	private $shortcode_manager;

	/**
	 * An instance of our WPFEPP_Form_Manager class
	 *
	 * @var WPFEPP_Form_Manager
	 **/
	private $form_manager;

	/**
	 * An instance of our WPFEPP_Plugin_Settings class
	 *
	 * @var WPFEPP_Plugin_Settings
	 **/
	private $plugin_settings;

	/**
	 * An instance of our WPFEPP_Form_Ajax class
	 *
	 * @var WPFEPP_Form_Ajax
	 **/
	private $form_ajax;

	/**
	 * An instance of our WPFEPP_Post_List class
	 *
	 * @var WPFEPP_Post_List
	 **/
	private $post_list;

	/**
	 * An instance of our WPFEPP_DB_Table class
	 *
	 * @var WPFEPP_DB_Table
	 **/
	private $db;

	/**
	 * Class constructor. Initializes the class variables, loads dependencies and registers actions of each class with the loader.
	 *
	 * @param string $version Plugin version
	 **/
	public function __construct( $version = "1.0" ) {
		$this->load_dependencies();
		$this->version = $version;
		$this->loader = new WPFEPP_Loader();
		$this->shortcode_manager = new WPFEPP_Shortcode_Manager( $version );
		$this->form_manager = new WPFEPP_Form_Manager( $version );
		$this->plugin_settings = new WPFEPP_Plugin_Settings( $version, $this->form_manager->get_page_slug() );
		$this->admin_messages = new WPFEPP_Admin_Messages( $version );
		$this->email_manager = new WPFEPP_Email_Manager( $version );
		$this->form_ajax = new WPFEPP_Form_Ajax( $version );
		$this->post_list = new WPFEPP_Post_List( $version );
		$this->db = WPFEPP_DB_Table::get_instance();
		$this->copyscape = new WPFEPP_CopyScape( $version );
		$this->previews = new WPFEPP_Post_Previews();
		$this->media_settings = get_option( 'wpfepp_media_settings' );
		$this->define_admin_hooks();
	}

	/**
	 * Responsible for including the files of other classes.
	 *
	 * @access private
	 **/
	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-shortcode-manager.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/class-wpfepp-form-manager.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/class-wpfepp-plugin-settings.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'admin/class-wpfepp-admin-messages.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-db-table.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-loader.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-form-ajax.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-post-list.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-email-manager.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-copyscape.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-post-previews.php';
	}

	/**
	 * Registers the actions of each class with the loader.
	 *
	 * @access private
	 **/
	private function define_admin_hooks() {
		// Hook initialization and uninstallation functions.
		$this->loader->add_action('admin_init', $this, 'initialize');
		$this->loader->add_action('wpfepp_uninstall', $this, 'rollback');
		// Hook the main functions of the plugin.
		$this->loader->add_filter('tiny_mce_before_init', $this, 'tinymce_on_change');
		$this->loader->add_filter('pre_get_posts', $this, 'show_users_own_attachments');
		$this->loader->add_filter('upload_mimes', $this, 'change_upload_mimes');
		$this->loader->add_filter('wp_handle_upload_prefilter', $this, 'upload_size');
		$this->loader->add_filter('init', $this, 'grant_upload_capabilities');
		$this->loader->add_filter('init', $this, 'register_resources');
		$this->loader->add_filter('wpseo_whitelist_permalink_vars', $this, 'update_yoast_whitelist');
		// Hook the actions of other plugin classes.
		$this->loader->add_action('wp_loaded', $this->form_manager, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->plugin_settings, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->shortcode_manager, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->email_manager, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->form_ajax, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->post_list, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->admin_messages, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->copyscape, 'add_actions');
		$this->loader->add_action('wp_loaded', $this->previews, 'add_actions');
	}

	/**
	 * Getter function that returns plugin version.
	 **/
	public function get_version() {
		return $this->version;
	}

	/**
	 * Executes the run function of the loader which in turn registers all actions and filters with WordPress.
	 **/
	public function run(){
		$this->loader->run();
	}

	/**
	 * Main initialization function. Creates the database tables and initializes the options on first run.
	 **/
	public function initialize() {
		// If this is not a new version, do nothing
		$old_version = get_option('wpfepp_version');
		if($this->version == $old_version)
			return;
		// Update/Create the forms table
		$this->db->create_table();
		// Update plugin settings
		$this->plugin_settings->update_settings();
		//Update all the existing forms to include the new fields and settings
		$this->form_manager->upgrade_forms();
		// Change the database flag to reflect that all the changes of this version have been completed
		update_option('wpfepp_version', $this->version);
	}
	 
	/**
	 * The function that will run on uninstallation of the plugin to remove data.
	 **/
	public static function rollback() {
		$data_settings = get_option('wpfepp_data_settings');
		if( $data_settings && $data_settings['delete_on_uninstall'] ){
			$db_instance = WPFEPP_DB_Table::get_instance();
			$db_instance->remove_table();
			$db_instance->delete_post_meta(WPFEPP_CopyScape::$meta_key);
			
			delete_option('wpfepp_media_settings');
			delete_option('wpfepp_post_list_settings');
			delete_option('wpfepp_data_settings');
			delete_option('wpfepp_errors');
			delete_option('wpfepp_email_settings');
			delete_option('wpfepp_copyscape_settings');
			delete_option('wpfepp_recaptcha_settings');
			delete_option('wpfepp_payment_settings');
			delete_option('wpfepp_version');
			delete_option('wpfepp_db_table_version');
			delete_option('wpfepp_nag_dismissed');
		}
	}

	public function register_resources(){
		// Register stylesheets.
		wp_register_style( 'wpfepp-style', plugins_url('static/css/style.css', dirname(__FILE__) ), array(), $this->version, 'all' );
		wp_register_style( 'wpfepp-select2-css', plugins_url('static/css/vendor/select2.min.css', dirname(__FILE__) ), array(), '4.0.3', 'all' );
		// Register plugin scripts. All the scripts are included in scripts.js for performance.
		wp_register_script( 'wpfepp-meta-boxes-product', plugins_url( 'static/js/meta-boxes-product.js', dirname(__FILE__) ), array('jquery'), $this->version, true );
		wp_register_script( 'wpfepp-script', plugins_url( 'static/js/scripts.js', dirname(__FILE__) ), array('jquery'), $this->version, true );
		wp_register_script( 'wpfepp-media', plugins_url( 'static/js/media-element.js', dirname(__FILE__) ), array('jquery'), $this->version, true );
		wp_register_script( 'wpfepp-select2', plugins_url( 'static/js/vendor/select2.min.js', dirname(__FILE__) ), array('jquery'), '4.0.3', true );
		wp_register_script( 'wpfepp-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), $this->version, true );

		// Localize resources.
		$errors = get_option('wpfepp_errors');
		wp_localize_script( 'wpfepp-script', 'wpfepp', array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'chooseimg' => esc_html__( 'Choose Image', 'wpfepp-plugin' ),
			'choosefile' => esc_html__( 'Choose File', 'wpfepp-plugin' ),
			'selectimg' => esc_html__( 'Choose an image to make it as the Post Thumbnail.', 'wpfepp-plugin' ),
			'orselectimg' => esc_html__( 'Or upload your own one in the Thumbnail section.', 'wpfepp-plugin' ),
			'noselectimg' => esc_html__( 'There are no suitable images to retrieve.', 'wpfepp-plugin' ),
			'parsing' => esc_html__( '... parsing images ...', 'wpfepp-plugin' )
		));
		wp_localize_script( 'wpfepp-script', 'wpfepp_errors', $errors );
	}

	/**
	 * Adds custom event handlers for blur and onKeyUp events of the frontend tinymce editor.
	 *
	 * @param array $initArray An array containing initialization data for TinyMCE
	 * @return array $initArray A modified array containing initialization data for TinyMCE
	 **/
	public function tinymce_on_change( $initArray )
	{
		if( is_admin() )
			return $initArray;

	    $initArray['setup'] = <<<JS
[function(ed) {
	var tinymceValidState = true;


	if(tinyMCE.majorVersion >= 4){
		ed.on('blur', function(e) {
			tinyMCE.triggerSave();
			is_valid = jQuery('#'+ed.id).valid();
			if(!is_valid)
				tinymceValidState = false;
		});

		ed.on('keyUp', function(e) {
			if(tinymceValidState){
				return;
			}
			tinyMCE.triggerSave();
			jQuery('#'+ed.id).valid();
		});		
	}

	if(tinyMCE.majorVersion <= 3){
		ed.onInit.add(function(ed, evt) {
	        tinymce.dom.Event.add(ed.getDoc(), 'blur', function(e) {
	            tinyMCE.triggerSave();
	            is_valid = jQuery('#'+ed.id).valid();
	            if(!is_valid)
	            	tinymceValidState = false;
	        });
	    });
		ed.onKeyUp.add(function(ed, e) {
			if(tinymceValidState){
				return;
			}
	    	tinyMCE.triggerSave();
	        jQuery('#'+ed.id).valid();
	    });
	}
}][0]
JS;
	    return $initArray;
	}

	/**
	 * Shows the user only his or her attachments in the media uploader
	 **/
	public function show_users_own_attachments( $wp_query_obj ) {
		if( wpfepp_current_user_has($this->media_settings['exempt_roles']) )
			return;

		if (current_user_can('activate_plugins')){
			return;
		}		

	    global $pagenow;
	    $current_user = wp_get_current_user();

	    if( !($current_user instanceof WP_User))
			return;

	    if( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' )
	    	return;

	    $wp_query_obj->set('author', $current_user->ID );

	    return;
	}

	public function change_upload_mimes( $mimes ){

		if( wpfepp_current_user_has($this->media_settings['exempt_roles']) )
			return $mimes;

		if (current_user_can('activate_plugins')){
			return $mimes;
		}		

		$allowed_media_types = $this->media_settings['allowed_media_types'];

		//Image formats
		if( !$allowed_media_types['image'] )
			unset( $mimes['jpg|jpeg|jpe'], $mimes['gif'], $mimes['png'], $mimes['bmp'], $mimes['tif|tiff'], $mimes['ico'] );

		// Video formats
		if( !$allowed_media_types['video'] )
			unset( $mimes['asf|asx'], $mimes['wmv'], $mimes['wmx'], $mimes['wm'], $mimes['avi'], $mimes['divx'], $mimes['flv'], $mimes['mov|qt'], $mimes['mpeg|mpg|mpe'], $mimes['mp4|m4v'], $mimes['ogv'], $mimes['webm'], $mimes['mkv'] );

		// Text formats
		if( !$allowed_media_types['text'] )
			unset( $mimes['txt|asc|c|cc|h'], $mimes['csv'], $mimes['tsv'], $mimes['ics'], $mimes['rtx'], $mimes['css'], $mimes['htm|html'] );

		// Audio formats
		if( !$allowed_media_types['audio'] )
			unset( $mimes['mp3|m4a|m4b'], $mimes['ra|ram'], $mimes['wav'], $mimes['ogg|oga'], $mimes['mid|midi'], $mimes['wma'], $mimes['wax'], $mimes['mka'] );

		// Misc application formats
		if( !$allowed_media_types['misc'] )
			unset( $mimes['rtf'], $mimes['js'], $mimes['pdf'], $mimes['swf'], $mimes['class'], $mimes['tar'], $mimes['zip'], $mimes['gz|gzip'], $mimes['rar'], $mimes['7z'], $mimes['exe'] );

		// MS Office formats
		if( !$allowed_media_types['office'] )
			unset( $mimes['doc'], $mimes['pot|pps|ppt'], $mimes['wri'], $mimes['xla|xls|xlt|xlw'], $mimes['mdb'], $mimes['mpp'], $mimes['docx'], $mimes['docm'], $mimes['dotx'], $mimes['dotm'], $mimes['xlsx'], $mimes['xlsm'], $mimes['xlsb'], $mimes['xltx'], $mimes['xltm'], $mimes['xlam'], $mimes['pptx'], $mimes['pptm'], $mimes['ppsx'], $mimes['ppsm'], $mimes['potx'], $mimes['potm'], $mimes['ppam'], $mimes['sldx'], $mimes['sldm'], $mimes['onetoc|onetoc2|onetmp|onepkg'] );

		// OpenOffice formats
		if( !$allowed_media_types['open_office'] )
			unset( $mimes['odt'], $mimes['odp'], $mimes['ods'], $mimes['odg'], $mimes['odc'], $mimes['odb'], $mimes['odf'] );

		// WordPerfect formats
		if( !$allowed_media_types['wordperfect'] )
			unset( $mimes['wp|wpd'] );

		// iWork formats
		if( !$allowed_media_types['iwork'] )
			unset( $mimes['key'], $mimes['numbers'], $mimes['pages'] );

		return $mimes;
	}

	/* This function prevents the users from uploading files larger than the size specified in the plugin's control panel. */
	public function upload_size($file) {

		if( wpfepp_current_user_has($this->media_settings['exempt_roles']) )
			return $file;

		if (current_user_can('activate_plugins')){
			return $file;
		}

		$max_upload_size = $this->media_settings['max_upload_size'];

	    $size = $file['size'];
	    if($size > $max_upload_size * 1024) {
	       $file['error'] = sprintf(__("Files larger than %s kilobytes are not allowed.", 'wpfepp-plugin'), $max_upload_size);
	    }
	    return $file;
	}

	/* By default subscribers and contributors can't upload media items. This function gives them the ability to do so. */
	public function grant_upload_capabilities(){
		if( $this->media_settings['force_allow_uploads'] ){
			$subscriber_role 	= get_role( 'subscriber' );
			if($subscriber_role)
				$subscriber_role->add_cap('upload_files');
			$contributor_role 	= get_role( 'contributor' );
			if($contributor_role)
				$contributor_role->add_cap('upload_files');
		} else {
			$subscriber_role 	= get_role( 'subscriber' );
			if($subscriber_role)
				$subscriber_role->remove_cap('upload_files');
			$contributor_role 	= get_role( 'contributor' );
			if($contributor_role)
				$contributor_role->remove_cap('upload_files');
		}
	}

	/* Adds vriables to yoast's whitelist */
	public function update_yoast_whitelist($query_vars) {
		$wpfepp_vars = array('wpfepp_action', 'wpfepp_post', '_wpnonce');
		return array_merge($wpfepp_vars, $query_vars);
	}
	
}

?>