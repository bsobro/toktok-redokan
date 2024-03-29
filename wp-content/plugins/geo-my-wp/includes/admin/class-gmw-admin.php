<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * GMW_Admin class.
 */
class GMW_Admin {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		// get options
		$this->settings = get_option( 'gmw_options' );
			
		// admin notice to import location to the new database table
		if ( get_option( 'gmw_old_locations_tables_exist' ) !== FALSE && get_option( 'gmw_old_locations_tables_updated' ) === FALSE ) {
			add_action( 'admin_notices', array( $this, 'update_database_notice' ) );
		}

		// do some actions
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
		add_action( 'admin_init', array( $this, 'init_addons' ) );

		// "GMW Form" button in edit post page
		if ( self::add_form_button_pages() ) {
			add_action( 'media_buttons', array( $this, 'add_form_button' ), 25 );
			add_action( 'admin_footer', array( $this, 'form_insert_popup' ) );
		}

		do_action( 'gmw_pre_admin_include_pages' );

		// admin functions
		include( 'gmw-admin-functions.php' );
		include( 'class-gmw-tracking.php' );
		include( 'updater/class-gmw-license.php' ); 
		include( 'class-gmw-form-settings-helper.php' );

		// admin pages
		include( 'pages/class-gmw-extensions.php' );
		include( 'pages/class-gmw-settings.php' );
		include( 'pages/class-gmw-forms-page.php' );
		include( 'pages/class-gmw-form-editor.php' );
		include( 'pages/tools/class-gmw-tools.php' );
		include( 'pages/import-export/class-gmw-import-export-page.php' );
				
		//set pages
		$this->addons_page   = new GMW_Extensions();
		$this->settings_page = new GMW_Settings();
		$this->forms_page    = new GMW_Forms_Page();

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'gmw-forms' && isset( $_GET['gmw_action'] ) && $_GET['gmw_action'] == 'edit_form' && ! empty( $_GET['prefix'] ) && class_exists( 'GMW_'.$_GET['prefix'].'_Form_Editor') ) {
			$class_name = 'GMW_'.$_GET['prefix'].'_Form_Editor';
		} else {
			$class_name = 'GMW_Form_Editor';
		}

		$this->edit_form_page 	  = new $class_name();
		$this->tools_page 		  = new GMW_Tools();
		$this->import_export_page = new GMW_Import_Export_Page();
		//$this->shortcodes_page 	= new GMW_Shortcodes_page();

		// Setup/welcome
		if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'gmw-welcome' ) {
			include_once( 'class-gmw-welcome-page.php' );
		}
		
		add_filter( 'plugin_action_links_' . GMW_BASENAME, array( $this, 'gmw_action_links' ), 10, 2 );
	}

	public function update_database_notice() {
		?>
	    <div class="error">
	    	<p><?php echo sprintf( __( 'GEO my WP needs to import exsiting locations into its new database table. <a href="%s" class="button-primary"> Import locations</a>' ), admin_url( 'admin.php?page=gmw-import-export&tab=gmw_v_3' ) ) ?></p>
	    </div>
		<?php
	}

	/**
	 * Action link in plugins page
	 * 
	 * @param $links
	 * @param $file
	 */
	public function gmw_action_links( $links, $file ) {
		
		$links['settings'] = '<a href="' . admin_url( 'admin.php?page=gmw-settings' ).'">' . __( 'Settings' , 'geo-my-wp' ) . '</a>';
		$links['extensions'] = '<a href="' . admin_url( 'admin.php?page=gmw-extensions' ).'">' . __( 'Extensions' , 'geo-my-wp' ) . '</a>';
		
		return $links;
	}
	
	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
	
		//GEO my WP menu items
		add_menu_page( 'GEO my WP', 'GEO my WP', 'manage_options', 'gmw-extensions', array( $this->addons_page, 'output' ), 'dashicons-location-alt', 66 );
		
		// sub menu pages
		$menu_items = array();

		$menu_items[] = array(
			'parent_slug' 		=> 'gmw-extensions',
			'page_title' 		=> __( 'GEO my WP Extensions', 'geo-my-wp' ),
			'menu_title' 		=> __( 'Extensions', 'geo-my-wp' ),
			'capability' 		=> 'manage_options',
			'menu_slug' 		=> 'gmw-extensions',
			'callback_function' => array( $this->addons_page, 'output' ),
			'priority'			=> 1
		);

		$menu_items[] = array(
			'parent_slug' 		=> 'gmw-extensions',
			'page_title' 		=> __( 'GEO my WP Settings', 'geo-my-wp' ),
			'menu_title' 		=> __( 'Settings', 'geo-my-wp' ),
			'capability' 		=> 'manage_options',
			'menu_slug' 		=> 'gmw-settings',
			'callback_function' => array( $this->settings_page, 'output' ),
			'priority'			=> 5
		);

		$forms_output = ( ! empty( $_GET['gmw_action'] ) && $_GET['gmw_action'] == 'edit_form' ) ? $this->edit_form_page : $this->forms_page;
		
		$menu_items[] = array(
			'parent_slug' 		=> 'gmw-extensions',
			'page_title' 		=> __( 'GEO my WP Forms', 'geo-my-wp' ),
			'menu_title' 		=> __( 'Forms', 'geo-my-wp' ),
			'capability' 		=> 'manage_options',
			'menu_slug' 		=> 'gmw-forms',
			'callback_function' => array( $forms_output, 'output' ),
			'priority'			=> 8
		);

		$menu_items[] = array(
			'parent_slug' 		=> 'gmw-extensions',
			'page_title' 		=> __( 'GEO my WP Import / Export', 'geo-my-wp' ),
			'menu_title' 		=> __( 'Import / Export', 'geo-my-wp' ),
			'capability' 		=> 'manage_options',
			'menu_slug' 		=> 'gmw-import-export',
			'callback_function' => array( $this->import_export_page, 'output' ),
			'priority'			=> 10
		);

		$menu_items[] = array(
			'parent_slug' 		=> 'gmw-extensions',
			'page_title' 		=> __( 'GEO my WP Tools', 'geo-my-wp' ),
			'menu_title' 		=> __( 'Tools', 'geo-my-wp' ),
			'capability' 		=> 'manage_options',
			'menu_slug' 		=> 'gmw-tools',
			'callback_function' => array( $this->tools_page, 'output' ),
			'priority'			=> 15
		);
		
		/** 
		 * 
		 * Hook your add-on's menu item and page
		 *
		 * To do so you need to append an array of menu items as the example below:
		 *
		 * $menu_items[] = array(
		 * 	  'parent_slug' 	  => 'gmw-extensions', // the main menu to add your sub-menu item to. Should always be gmw-extensions
		 *	  'page_title' 		  => __( 'GEO my WP Tools', 'geo-my-wp' ),
		 *	  'menu_title' 		  => __( 'Tools', 'geo-my-wp' ),
		 *	  'capability' 		  => 'manage_options',
		 *	  'menu_slug' 		  => 'gmw-tools',
		 *	  'callback_function' => array( 'tools_page', 'output' ), // this can be either a string when using a function or array of class and the function to execute.
		 *	  'priority'		  => 25 
		 * );
		 * 
		 */
		$menu_items = apply_filters( 'gmw_admin_menu_items', $menu_items );

		// order menu items by priority
		usort( $menu_items, 'gmw_sort_by_priority' );
		
		// gmw admin pages
		$gmw_pages = array();

		// loop and create menu items and pages
		foreach ( $menu_items as $key => $item ) {
		
			add_submenu_page( 
				! empty( $item['parent_slug'] ) ? $item['parent_slug'] : 'gmw-extensions',
				! empty( $item['page_title'] )  ? $item['page_title']  : '',
				! empty( $item['menu_title'] )  ? $item['menu_title']  : '',
				! empty( $item['capability'] )  ? $item['capability']  : 'manage_options',
				! empty( $item['menu_slug'] )   ? $item['menu_slug']   : '',
				$item['callback_function']
			);

			$gmw_pages[] = $item['menu_slug'];
		}	


		// apply credit and enqueue scripts and styles in GEO my WP admin pages only
		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $gmw_pages ) ) {

			add_filter( 'admin_footer_text', array( $this, 'gmw_credit_footer'), 10 );
		}
	}
	
	/**
	 * Initiate GEO my WP's add-ons
	 *
	 */
	public function init_addons() {

		/******* Compability with previous versions of add-ons. To be removed in the future. *********/
		
		$deprecated_addons = array();
		
		// hook your add-on here ( deprecated )
		$deprecated_addons = apply_filters( 'gmw_admin_addons_page', $deprecated_addons );

		foreach ( $deprecated_addons as $key => $addon ) {	

			//if ( IS_ADMIN && ( empty( $gmw_addons[$key] ) || ! get_transient( 'gmw_extensions_data' ) ) ) {
				//sdf();
				$new_addon 		   	  	  = array();
				$new_addon['slug'] 	  	  = $key;
				$new_addon['name'] 	  	  = $addon['title'];
				$new_addon['prefix']  	  = ! empty( $addon['prefix'] ) ? $addon['prefix'] : '1.0';
				$new_addon['version'] 	  = ! empty( $addon['version'] ) ? $addon['version'] : '1.0';
				$new_addon['author']      = ! empty( $addon['author'] ) ? $addon['author'] : 'Eyal Fitoussi';
				$new_addon['description'] = ! empty( $addon['desc'] ) ? $addon['desc'] : '';
				$new_addon['is_core'] 	  = false;
				$new_addon['object_type'] = false;
				$new_addon['full_path']   = ! empty( $addon['file'] ) ? $addon['file'] : '';
				$new_addon['basename']    = plugin_basename( $new_addon['full_path'] );
				$new_addon['plugin_dir']  = untrailingslashit( plugin_dir_path( $new_addon['full_path'] ) );
				$new_addon['plugin_url']  = untrailingslashit( plugins_url( basename( plugin_dir_path( $new_addon['full_path'] ) ), dirname( $new_addon['full_path'] ) ) );
				$new_addon['required']	  = false;
				$new_addon['min_version'] = ! empty( GMW()->required_versions[$new_addon['slug']] ) ? GMW()->required_versions[$new_addon['slug']] : '1.0';
				$new_addon['gmw_min_version'] = ! empty( $addon['gmw_version'] ) ? $addon['gmw_version'] : GMW_VERSION;
				$new_addon['item_name']   	  = ! empty( $addon['item'] ) ? $addon['item'] : false;
				$new_addon['item_id']         = ! empty( $addon['item_id'] ) ? $addon['item_id'] : null;
				$new_addon['api_url']         = ! empty( $addon['api_url'] ) ? $addon['api_url'] : null;
				$new_addon['license_name']    = ! empty( $addon['name'] ) ? $addon['name'] : false;
				$new_addon['trigger_license'] = ! empty( $addon['trigger_license'] ) ? $addon['trigger_license'] : false;

				//check for min requirements of add-ons with current version of GEO my WP
				if ( ! empty( $new_addon['gmw_min_version'] ) && version_compare( GMW_VERSION, $new_addon['gmw_min_version'], '<' ) ) {

					$new_addon['status'] = 'disabled';
					$new_addon['status_details']['error'] = 'gmw_version_mismatch';
					$new_addon['status_details']['required_version'] = $new_addon['gmw_min_version'];
					$new_addon['status_details']['notice'] = sprintf( 
						__( '%s extension version %s requires GEO my WP plugin version %s or higher.', 'geo-my-wp' ), 
						$new_addon['name'], 
						$new_addon['version'], 
						$new_addon['gmw_min_version']
					);
					
				} elseif ( ! empty( $new_addon['min_version'] ) && version_compare( $new_addon['version'], $new_addon['min_version'], '<' ) ) {

					$new_addon['status'] = 'disabled';
					$new_addon['status_details']['error'] = 'addon_version_mismatch';
					$new_addon['status_details']['required_version'] = $new_addon['min_version'];
					$new_addon['status_details']['notice'] = sprintf( 
						__( '%s extension requires an update to version %s.', 'geo-my-wp' ), 
						$new_addon['name'],
						$new_addon['min_version']
					);
				
				//otherwise mark add-on as activated
				} else {

					$new_addon['status'] = 'active';
				}				

				// trigger license key
				if ( class_exists( 'GMW_License' ) && ! empty( $new_addon['full_path'] ) ) {

					$gmw_license = new GMW_License( 
						$new_addon['full_path'], 
						$new_addon['item_name'], 
						$new_addon['slug'], 
						$new_addon['version'], 
						$new_addon['author'], 
						$new_addon['api_url'], 
						$new_addon['item_id']
					);
				}

				GMW()->addons[$new_addon['slug']] = $new_addon;

				GMW()->registered_addons[] = $new_addon['slug'];

				if ( ! empty( $new_addon['object_type'] ) ) {
					GMW()->object_types[] = $new_addon['object_type'];
				}
			//}
		}
		
		/********* end ******************************************************/
	}
	
	/**
	 * add_form_button_pages
	 *
	 * Verify allowed pages for 
	 */
	public static function add_form_button_pages() {
		
		//alowed pages can be modified
		$pages = apply_filters( 'gmw_add_form_button_admin_pages', array( 'post.php', 'page.php', 'page-new.php', 'post-new.php' ) );

		return ( is_array( $pages ) && in_array( basename( $_SERVER['PHP_SELF'] ), $pages ) ) ? 1 : 0;
	}
	
	/**
	 * Action target that adds the "Insert Form" button to the post/page edit screen
	 *
	 * This script inspired by the the work of the developers of Gravity Forms plugin
	 */
    public static function add_form_button(){	
    	?>
        <style>
    		.gmw_media_icon:before {
    			content: "\f230" !important;
				color: rgb(103, 199, 134) !important;
			}
    		.gmw_media_icon {
            	vertical-align: text-top;
            	width: 18px;
            }
            .wp-core-ui a.gmw_media_link{
             	padding-left: 0.4em;
            }
        </style>

        <a 
        	href="#TB_inline?width=480&inlineId=select_gmw_form" 
        	class="thickbox button gmw_media_link" 
        	id="add_gmw_form" 
        	title="<?php _e( 'GEO my WP Form Shortcode', 'geo-my-wp' ); ?>"
        >
        	<span class="dashicons-location-alt dashicons"></span>
        	<?php _e( 'GMW Form', 'geo-my-wp' ); ?>
       	</a>
       	<?php
    }
    
    /**
     * form_insert_popup
     *
     * Popup form to inset GEO my WP form shortcode into content area
     * 
     * @return [type] [description]
     */
    public static function form_insert_popup() {
    	?>
        <script>
            function gmwInsertForm() {
                                	
            	var form_id = jQuery( '#gmw_form_id' ).val();

                if ( form_id == "" ){
                    
                    alert( '<?php _e( 'Please select a form', 'geo-my-wp' ) ?>' );
                    
                    return;
                }
                
            	var form_name = jQuery("#gmw_form_id option[value='" + form_id + "']").text().replace(/[\[\]]/g, '');
            	
            	window.send_to_editor("[gmw "+ jQuery( '.gmw_form_type:checked' ).val() + "=\"" + form_id + "\" name=\"" + form_name + "\"]");
            }
        </script>

        <div id="select_gmw_form" style="display:none;">
            <div class="gmw-form-shortcode-thickbox-wrap">
                <div>
                    <div>
                        <h3><?php _e( 'Insert A Form Shortcode', 'geo-my-wp' ); ?></h3>
                        <p><?php _e( 'Select the type of shortcode you wish to add:', 'geo-my-wp' ); ?></p>
                    </div>
                    
                    <div class="checkboxes">
                    	<label>
                        	<input 
                        		type="radio" class="gmw_form_type" checked="checked" name="gmw_form_type" value="form" 
                        		onclick="if ( jQuery( '#gmw-forms-dropdown-wrapper' ).is( ':hidden' ) ) jQuery( '#gmw-forms-dropdown-wrapper' ).slideToggle();" 
                        	/> 
                        	<?php _e( 'Complete Form', 'geo-my-wp' ); ?>	
                        </label>

                        <label>
                        	<input type="radio" class="gmw_form_type" name="gmw_form_type"  value="search_form" 
                        		onclick="if ( jQuery( '#gmw-forms-dropdown-wrapper' ).is( ':hidden' ) ) jQuery('#gmw-forms-dropdown-wrapper' ).slideToggle();" 
                        	/> 
                        	<?php _e( 'Search Form Only', 'geo-my-wp' ); ?>	
                        </label>

                        <label>
	                        <input type="radio" class="gmw_form_type" name="gmw_form_type"  value="map" 
	                        	onclick="if ( jQuery( '#gmw-forms-dropdown-wrapper' ).is( ':hidden' ) ) jQuery('#gmw-forms-dropdown-wrapper').slideToggle();" 
	                        /> 
                        	<?php _e( 'Map Only', 'geo-my-wp'); ?>	
                        </label>

                        <label>
	                        <input type="radio" class="gmw_form_type" name="gmw_form_type" value="search_results" 
	                        	onclick="if ( jQuery( '#gmw-forms-dropdown-wrappe' ).is( ':visible' ) ) jQuery('#gmw-forms-dropdown-wrapper').slideToggle();" 
	                        /> 
                        	<?php _e( 'Search Results Only', 'geo-my-wp' ); ?>	
                       	</label>

                    </div>

                    <div id="gmw-forms-dropdown-wrapper">
                        <select id="gmw_form_id">
                            <option value="">
                            	<?php _e( 'Select a Form', 'geo-my-wp' ); ?>	
                            </option>
                            <?php
                                $forms = gmw_get_forms();
                                
                                foreach( $forms as $form ) {
                                	
                                	$form['title'] = ! empty( $form['title'] ) ? $form['title'] : 'form_id_'.$form['ID'];
                                    ?>
                                    <option value="<?php echo absint( $form['ID'] ); ?>">
                                    	<?php echo esc_html( $form['title'] ); ?>	
                                    </option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                   
                    <div>
                        <input 
                        	type="button" 
                        	class="button-primary" 
                        	value="<?php _e( 'Insert Shortcode', 'geo-my-wp' ); ?>" 
                        	onclick="gmwInsertForm();"
                        />
                    	<a class="button" href="#" onclick="tb_remove(); return false;">
                    		<?php _e( 'Cancel', 'geo-my-wp' ); ?>
                    	</a>
                    </div>
                </div>
            </div>
        </div>
    	<?php
    }
	
	/**
	 * GMW credit footer
	 * 
	 * @param unknown_type $content
	 * @return string
	 */
	static public function gmw_credit_footer( $content ) {
		return preg_replace( '/[.,]/', '', $content ) . ' ' . sprintf( __( 'and Geolocating with <a %s>GEO my WP</a>. Your <a %s>feedback</a> on GEO my WP is greatly appriciated.', 'geo-my-wp' ), "href=\"http://geomywp.com\" target=\"_blank\" title=\"GEO my WP website\"", "<a href=\"https://wordpress.org/support/view/plugin-reviews/geo-my-wp?filter=5\" target=\"_blank\" title=\"Rate GEO my WP\"" );	
	}
}
new GMW_Admin();
?>
