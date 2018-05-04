<?php

/**
 * This class is responsible for creating the form management page in the backend area.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Form_Manager {
	/**
	 * Plugin version. This is used in wp_enqueue_style and wp_enqueue_script to make sure that the end user doesn't get outdated scripts and styles because of browser caching.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;
	
	/**
	 * An instance of the WPFEPP_DB_Table class for interacting with the database table.
	 *
	 * @access private
	 * @var WPFEPP_DB_Table
	 **/
	private $db;
	
	/**
	 * The hook of our admin page. It is used to make sure that the stylesheets and scripts are only enqueued where they are relevant.
	 *
	 * @access private
	 * @var string
	 **/
	private $page_hook;
	
	/**
	 * The page slug.
	 *
	 * @access private
	 * @var string
	 **/
	private $page;

	/**
	 * Class constructor. Includes essential files and initializes the class attributes.
	 **/
	public function __construct( $version ) {
		$this->load_dependencies();

		$this->version = $version;
		$this->page = 'wpfepp_form_manager';
		$this->db = WPFEPP_DB_Table::get_instance();
		$this->tabs = new WPFEPP_Tab_Collection();
		$fields_tab = new WPFEPP_Tab_Form_Fields( $this->version, 'fields', __( "Fields", "wpfepp-plugin" ), $this );
		$settings_tab = new WPFEPP_Tab_Form_Settings( $this->version, 'settings', __( "Settings", "wpfepp-plugin" ), $this );
		$emails_tab = new WPFEPP_Tab_Form_Emails( $this->version, 'emails', __( "Emails", "wpfepp-plugin" ), $this );
		$extended_tab = new WPFEPP_Tab_Form_Extended( $this->version, 'extended', __( "Extended", "wpfepp-plugin" ), $this );

		$this->tabs->add( $fields_tab );
		$this->tabs->add( $settings_tab );
		$this->tabs->add( $emails_tab );
		$this->tabs->add( $extended_tab );
	}

	/**
	 * Loads the source files of other classes required for the proper functioning of form manager.
	 **/
	private function load_dependencies() {
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-db-table.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/global-functions.php';
		require_once 'class-wpfepp-list-table.php';
		require_once 'class-wpfepp-tab-collection.php';
		require_once 'class-wpfepp-tab-form-fields.php';
		require_once 'class-wpfepp-tab-form-settings.php';
		require_once 'class-wpfepp-tab-form-emails.php';
		require_once 'class-wpfepp-tab-form-extended.php';
	}

	/**
	 * Sets the default values for new forms. Since it uses some WordPress functions that are not available at the time of instantiation, it has to be called in add_actions and upgrade_forms functions.
	 **/
	private function set_defaults() {
		$this->post_types = get_post_types( array( 'show_ui' => true ), 'names', 'and' );
		$this->defaults = array();
		$this->defaults['fields'] = $this->generate_default_form_fields();
		$this->defaults['custom_field'] = array('type' => 'custom_field', 'enabled' => true, 'required' => false, 'width' => '', 'custom_prefix' => '', 'custom_postfix' => '', 'strip_tags' => 'unsafe', 'nofollow' => false, 'element' => 'input','choices' => '', 'fallback_value' => '', 'prefix_text' => '');
		$this->defaults['settings'] = array('no_restrictions' => wpfepp_prepare_default_role_settings(), 'instantly_publish' => wpfepp_prepare_default_role_settings(), 'width' => '', 'redirect_url' => '', 'button_color' => 'blue', 'enable_drafts' => true, 'user_emails' => true, 'admin_emails' => true, 'admin_email_up' => false, 'copyscape_enabled' => false, 'captcha_enabled' => false);
		$this->defaults['emails'] = array(
			'user_email_subject' => __( "Thank you for your contribution", "wpfepp-plugin" ),
			'user_email_content' => sprintf( __( "Hi %s,\n\nThank you for submitting the article '%s' at our website %s. It has been added to the queue and will be published shortly.\n\nRegards,\n%s\n%s", "wpfepp-plugin" ), '%%AUTHOR_NAME%%', '%%POST_TITLE%%', '%%SITE_NAME%%', '%%ADMIN_NAME%%', '%%SITE_URL%%' ),
			'admin_email_subject' => sprintf( __( "A new article has been submitted on your website %s", "wpfepp-plugin"), '%%SITE_NAME%%' ),
			'admin_email_subject_up' => sprintf( __( "An article has been updated on your website %s", "wpfepp-plugin" ), '%%SITE_NAME%%' ),
			'admin_email_content' => sprintf( __( "Hi %s,\n\nA new article has been added to your website. You can view and edit all your articles here:\n\n%s\n\nRegards,\nYour web server", "wpfepp-plugin" ), '%%ADMIN_NAME%%', '%%EDIT_LINK%%' ),
			'admin_email_content_up' => sprintf( __( "Hi %s,\n\nAn article has been edited and is awaiting moderation on your website. You can view and edit all your articles here:\n\n%s\n\nRegards,\nYour web server", "wpfepp-plugin" ), '%%ADMIN_NAME%%', '%%EDIT_LINK%%' )
		);
		$this->defaults['extended'] = array('limit_number'=>'', 'limit_number_message'=>'', 'limit_number_redirect'=>'', 'pre_limit_message'=>'', 'map_google_key' => '', 'map_google_lang' => 'en','map_google_country' => 'US', 'map_start_location' => '', 'adress_placeholder' => '', 'start_geo_lat' => '51.483137', 'start_geo_long' => '-0.007', 'enable_city_suggest' => false,'enable_map' => false);
	}

	public function get_defaults() {
		return $this->defaults;
	}

	/**
	 * Adds the actions of this class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions() {
		$this->set_defaults();
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_ajax_wpfepp_create_form_ajax', array( $this, 'create_form_ajax' ) );
		add_action( 'admin_init', array( $this, 'delete_forms' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		$this->tabs->add_actions();
	}

	/**
	 * Adds our form manager to the admin menu. This method is registered with WordPress by the add_actions() function above.
	 **/
	public function add_menu_item() {
		$this->page_hook = add_menu_page(
			__( "RH Frontend Publishing Pro", "wpfepp-plugin" ),
			__( "RH Frontend", "wpfepp-plugin" ),
		    'manage_options',
		    $this->page,
		    array( $this, 'render_form_manager' ),
		    plugins_url( "static/img/icon.png" , dirname(__FILE__) )
		);
		add_submenu_page( $this->page, __( "Frontend Publishing Forms", "wpfepp-plugin" ), __( "Forms", "wpfepp-plugin" ), 'manage_options', $this->page );
	}

	/**
	 * Enqueues stylesheets and scripts in the admin area.
	 *
	 * @param string $hook The hook of the current page. The stylesheets and scripts are only added when this matches the $page_hook attribute.
	 **/
	public function enqueue( $hook ) {
		if( $this->page_hook != $hook )
			return;
		wp_enqueue_style( 'wpfepp-admin-stylesheet', plugins_url( "static/css/admin.css" , dirname(__FILE__) ), false, $this->version, "all" );
		wp_enqueue_script( 'wpfepp-form-manager-script', plugins_url( "static/js/form-manager.js" , dirname(__FILE__) ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), $this->version );
		wp_localize_script( 'wpfepp-form-manager-script', 'objectL10n', array( 'areyousure' => esc_html__( "Are you sure?", "wpfepp-plugin" ) ) );
	}

	/**
	 * Callback function for add_menu_page(). Outputs the HTML for our form manager.
	 **/
	public function render_form_manager() {
		if( isset( $_GET['action'] ) && $_GET['action']=='edit' && isset( $_GET['form'] ) && $this->db->form_exists( $_GET['form'] ) )
			include( 'partials/settings.php' );
		else
			include( 'partials/forms.php' );
	}

	/**
	 * Generates an array of default form fields. This method is used when a new form is created by the user.
	 * 
	 * @access private
	 *
	 * @param string $post_type The post type for which to generate default form fields.
	 * @return array An array of default form fields. Each element of this array contains the setting of a particular form field.
	 **/
	private function generate_default_form_fields(){
		$fields = array();
		foreach ($this->post_types as $post_type) {
			$default_fields = array(
				'title' => array( 'type' => 'title', 'widget_label' => __('Title', 'wpfepp-plugin'), 'label' => __('Title', 'wpfepp-plugin'), 'enabled' => true, 'required'=> true, 'width' => '', "min_words" => 2, "max_words" => 50, 'strip_tags' => 'all', 'fallback_value' => '', 'prefix_text' => '' ),
				'content' => array( 'type' => 'content', 'widget_label' => __( "Content", "wpfepp-plugin" ), 'label' => __( "Content", "wpfepp-plugin" ), 'enabled' => true, 'required'=> true, 'width' => '', 'min_words' => 10, 'max_words' => 1000, 'max_links' => 2, 'element' => 'richtext', 'strip_tags' => 'unsafe', 'nofollow' => false, 'media_button' => true, 'fallback_value' => '', 'prefix_text' => '' ),
				'excerpt' => array( 'type' => 'excerpt', 'widget_label' => __('Excerpt', 'wpfepp-plugin'), 'label' => __('Excerpt', 'wpfepp-plugin'), 'enabled' => false, 'required'=> false, 'width' => '', "min_words" => 10, "max_words" => 50, 'strip_tags' => 'all', 'fallback_value' => '', 'prefix_text' => '' ),
				'thumbnail' => array( 'type' => 'thumbnail', 'widget_label' => __('Thumbnail', 'wpfepp-plugin'), 'label' => __('Thumbnail', 'wpfepp-plugin'), 'enabled' => true, 'required'=> false, 'width' => '', 'parser' => false, 'parser_width' => '500', 'fallback_value' => '', 'prefix_text' => '' ),
				'formatting' => array( 'type' => 'post_formats', 'widget_label' => __('Formats', 'wpfepp-plugin'), 'label' => __('Formats', 'wpfepp-plugin'), 'enabled' => false, 'required'=> false, 'width' => '', 'fallback_value' => '', 'prefix_text' => '' ));
			if($post_type == 'product') {
				$default_fields_wc = array(
					'sku' => array( 'type' => 'sku', 'widget_label' => __('SKU', 'wpfepp-plugin'), 'label' => __('SKU', 'wpfepp-plugin'), 'enabled' => true, 'required'=> false, 'width' => '', "min_symbols" => 3, "max_symbols" => 12, 'strip_tags' => 'all', 'fallback_value' => '', 'prefix_text' => '' ),
					'price' => array( 'type' => 'price', 'widget_label' => __('Regular Price', 'wpfepp-plugin'), 'label' => __('Regular Price', 'wpfepp-plugin'), 'enabled' => true, 'required'=> true, 'width' => '', "min_symbols" => 2, "max_symbols" => 7, 'strip_tags' => 'all', 'fallback_value' => '', 'prefix_text' => '' ),
					'sale_price' => array( 'type' => 'sale_price', 'widget_label' => __('Sale Price', 'wpfepp-plugin'), 'label' => __('Sale Price', 'wpfepp-plugin'), 'enabled' => true, 'required'=> false, 'width' => '', "min_symbols" => 2, "max_symbols" => 7, 'strip_tags' => 'all', 'fallback_value' => '', 'prefix_text' => '' ),
					'product_options' => array('type' => 'product_options', 'widget_label' => __('Product Options', 'wpfepp-plugin'), 'enabled' => false, 'required'=> false, 'prod_gallery' => false, 'virtual_prod' => false, 'extern_prod' => false, 'down_product' => false, 'down_type' => false, 'prefix_text' => ''));
				$default_fields = array_merge($default_fields, $default_fields_wc);
			}
			
			$taxonomy_objects = get_object_taxonomies( $post_type, 'objects' );

			foreach ($taxonomy_objects as $taxonomy) {
				$excluded_taxonomy = array('post_format', 'product_type', 'product_shipping_class', 'product_visibility');
				if(!in_array($taxonomy->name, $excluded_taxonomy)) {
					$default_fields[$taxonomy->name] = array( 'widget_label' => $taxonomy->label, 'label' => $taxonomy->label, 'enabled' => true, 'required'=> false, 'width' => '', 'fallback_value' => '', 'prefix_text' => '' );
					if ( $taxonomy->hierarchical || $taxonomy->rewrite['hierarchical'] ) {
						$default_fields[$taxonomy->name]['type'] = 'hierarchical_taxonomy';
						$default_fields[$taxonomy->name]['multiple'] = true;
						$default_fields[$taxonomy->name]['hide_empty'] = false;
						$default_fields[$taxonomy->name]['exclude'] = '';
						$default_fields[$taxonomy->name]['include'] = '';
					} else {
						$default_fields[$taxonomy->name]['type'] = 'non_hierarchical_taxonomy';
						$default_fields[$taxonomy->name]['min_count'] = 1;
						$default_fields[$taxonomy->name]['max_count'] = 20;
					}
				}
			}
			$fields[$post_type] = $default_fields;
		}
		return $fields;
	}

	/**
	 * Upgrades all the old forms in the database to include the new fields and settings introduced in the version.
	 **/
	public function upgrade_forms(){
		$this->set_defaults();
		
		foreach ($this->post_types as $type) {
			$this->db->upgrade_forms($this->defaults['fields'][$type], $this->defaults['settings'], $this->defaults['emails'], $this->defaults['extended'], $this->defaults['custom_field'], $type);
		}
	}

	/**
	 * An ajax callback function that creates a new form with default fields and settings after checking the nonce.
	 **/
	public function create_form_ajax() {
		$form_name = $_POST['form_name'];
		$post_type = $_POST['post_type'];
		$description = $_POST['form_description'];
		$parent_form_id = $_POST['parent_form'];
		$parent_form = false;
		$nonce = $_POST['_wpnonce'];

		if( ! ( $form_name && $post_type ) ) {
			$response = array( 'success' => false, 'error' => __( "You missed a required item.", "wpfepp-plugin" ) );
			die( json_encode( $response ) );
		}

		if( ! wp_verify_nonce( $nonce, 'wpfepp-create-form' ) ) {
			$response = array( 'success' => false, 'error' => __( "Security check failed!", "wpfepp-plugin" ) );
			die( json_encode( $response ) );
		}

		if( $parent_form_id != -1 ) {
			$parent_form = $this->db->get( $parent_form_id );
			if( $parent_form && $parent_form['post_type'] != $post_type ) {
				$response = array( 'success' => false, 'error' => __( "You can only import items from a form which has the same post type as the one selected for this form.", "wpfepp-plugin" ) );
				die( json_encode( $response ) );
			}
		}

		$fields = ( $parent_form ) ? $parent_form['fields'] : $this->defaults['fields'][$post_type];
		$settings = ( $parent_form ) ? $parent_form['settings'] : $this->defaults['settings'];
		$emails = ( $parent_form ) ? $parent_form['emails'] : $this->defaults['emails'];
		$extended = ( $parent_form ) ? $parent_form['extended'] : $this->defaults['extended'];
		$this->db->add( $form_name, $post_type, $description, $fields, $settings, $emails, $extended );

		ob_start();
		$this->render_form_list_table();
		$table_html = ob_get_clean();
		$response = array( 'success' => true, 'table_html' => $table_html );
		die( json_encode( $response ) );
	}

	/**
	 * Generates HTML for the list table using WPFEPP_List_Table class.
	 * 
	 * @access private
	 **/
	private function render_form_list_table() {
		?>
			<form method="GET">
			<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
				<?php
					$table = new WPFEPP_List_Table( $this->version );
					$table->prepare_items();
					$table->display();
				?>
			</form>
		<?php
	}

	/**
	 * Displays a message on the option page whenever an item is deleted or updated.
	 **/
	public function admin_notices() {
		global $pagenow;
		if( $pagenow != 'admin.php' || !isset( $_GET['page']) || $_GET['page'] != $this->page )
			return;

		if( isset($_GET['deleted']) && $_GET['deleted'] ) {
    	?>
		    <div class="updated">
		        <p><?php _e( "Form(s) deleted successfully!", "wpfepp-plugin" ); ?></p>
		    </div>
	    <?php
		}

		if( isset( $_GET['updated'] ) && $_GET['updated'] ) {
    	?>
		    <div class="updated">
		        <p><?php _e( "The form has been updated successfully!", "wpfepp-plugin" ); ?></p>
		    </div>
	    <?php
		}
	}

	public function is_page() {
		return ( is_admin() && isset( $_GET['page'] ) && $this->page == $_GET['page'] && isset( $_GET['action'] ) && isset( $_GET['form'] ) );
	}

	/**
	 * Deals with the delete bulk action of the list table and saves form fields and settings in the database.
	 **/
	public function delete_forms() {

		if( !$this->is_page() ) 
			return;
		
		$result = 0;

		if( ( $_GET['action'] == 'delete' || ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' ) ) 
			&& is_array( $_GET['form'] ) 
			&& isset( $_GET['_wpnonce'] ) 
			&& wp_verify_nonce( $_GET['_wpnonce'], 'bulk-forms' ) )
		{
			if( !empty( $_GET['form'] ) ) {
				$result = $this->db->delete_multiple( $_GET['form'] );
				$sendback = esc_url_raw( add_query_arg( array( 'page' => $_GET['page'], 'deleted' => $result ), '' ) );
			}
			wp_redirect ( $sendback );
		}
		
		if( $_GET['action'] == 'delete' && is_numeric( $_GET['form'] ) ) {
			$result = $this->db->delete_single( $_GET['form'] );
			$sendback = esc_url_raw( add_query_arg( array( 'page' => $_GET['page'], 'deleted' => $result ), '' ) );
			wp_redirect ( $sendback );
		}
	}

	/**
	 * Simple getter function for the slug of this page.
	 **/
	public function get_page_slug(){
		return $this->page;
	}

	public function display_form_selector(){
		$forms = $this->db->get_forms_for_select();
		?>
		<select name="parent_form">
			<option value="-1"><?php _e('None', 'wpfepp-plugin'); ?></option>
			<?php foreach ($forms as $key => $form): ?>
				<option value="<?php echo $key; ?>"><?php echo $form; ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
}

?>