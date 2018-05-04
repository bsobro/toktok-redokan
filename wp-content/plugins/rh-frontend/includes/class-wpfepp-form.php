<?php

/**
 * WPFEPP Form is the class responsible for generating HTML for forms and handling submissions.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/

class WPFEPP_Form
{
	/**
	 * Plugin version.
	 *
	 * @var string
	 **/
	private $version;
	
	/**
	 * An instance of our table class for making database calls
	 * 
	 * @var WPFEPP_DB_Table
	 **/
	private $db;

	/**
	 * Id of the form from the database table
	 *
	 * @var integer
	 **/
	private $id;

	/**
	 * Name of the form from the database table
	 *
	 * @var string
	 **/
	private $name;

	/**
	 * A short description of this form from the database table
	 *
	 * @var string
	 **/
	private $description;

	/**
	 * The post type for which the current form will work
	 *
	 * @var string
	 **/
	private $post_type;

	/**
	 * An array containing all the form fields and their restrictions. The array is stored in the Database as a serialized string.
	 *
	 * @var string
	 **/
	private $fields;

	/**
	 * An array containing all the form settings. The array is stored in the Database as a serialized string.
	 *
	 * @var string
	 **/
	private $settings;
	
	/**
	 * An array containing all the extended form settings. The array is stored in the Database as a serialized string.
	 *
	 * @var string
	 **/
	private $extended;

	/**
	 * A boolean flag that keeps track of whether the form exists in the database table or not.
	 *
	 * @var boolean
	 **/
	private $valid;
	
	/**
	 * A boolean flag that keeps track of whether the form paid or not.
	 *
	 * @var boolean
	 **/
	private $paid_on;	
	
	/**
	 * Fetches the form data from the database table and initializes all the class attributes.
	 * 
	 * @param int $form_id The row ID of this form from the database table.
	 **/
	public function __construct( $version, $form_id = -1, $paid_form ) {
		$this->load_dependencies();
		$this->version = $version;

		if( $form_id < 0 ) {
			$this->valid = false;
			return;
		}
		
		if( $paid_form == 1 ) {
			$this->paid_on = true;
		} else {
			$this->paid_on = false;
		}

		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-db-table.php';
		$this->id = $form_id;
		$this->db = WPFEPP_DB_Table::get_instance();
		$row = $this->db->get( $form_id );
		$this->captcha = new WPFEPP_Captcha( $this->version );

		if( $row )
			$this->valid = true;
		else
			$this->valid = false;

		if( $this->valid ) {
			$this->name = $row['name'];
			$this->description = $row['description'];
			$this->post_type = $row['post_type'];
			$this->fields = $row['fields'];
			$this->settings = $row['settings'];
			$this->emails 	= $row['emails'];
			$this->extended = $row['extended'];

			//Necessary because we need to check if the post type is public before showing any links to the end user.
			$this->post_type_obj = get_post_type_object( $this->post_type );
		}
	}

	private function load_dependencies(){
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-copyscape.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-captcha.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-post-previews.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-image.php';
	}

	/**
	 * The main function of this class. It is responsible for calling other functions for outputting the form and handling submissions.
	 * 
	 * @param  int $post_id The ID of the post that the form should be populated with on page load. It is used only when the form is created for editing an existing post.
	 **/
	public function display( $post_id = -1 ) {
		//Make sure the form exists in the database. If it does not, display a friendly error message.
		if( !$this->valid ){
			_e( "No form with the specified ID was found", "wpfepp-plugin" );
			return;
		}
		if( !is_user_logged_in() ) {
			$class = apply_filters( 'wpfepp_login_url_class', 'wpfepp-login-url' );
			printf( __( "You need to %s first.", "wpfepp-plugin" ), sprintf( '<a href="%s" class="%s">%s</a>', wp_login_url(), $class, __( "login", "wpfepp-plugin") ) );
			return;
		}
		if( $this->post_type == 'product' and !class_exists( 'Woocommerce' ) and current_user_can( 'install_plugins' ) ) {
			printf( '<div class="wpfepp wpfepp wpfepp-posts"><div class="wpfepp-message error display">%s</div></div>', __( "Woocommerce plugin is deactivated or not installed.", "wpfepp-plugin" ) );
			return;
		}

		$current = false;
		$result = false;

		//If a post id was passed to this function then load its content
		if( $post_id != -1 ) {
			$current = $this->get_post( $post_id );
		}
		//If the form has been submitted, handle the submission and populate the $current variable with either the inserted post or the $_POST array
		if( isset( $_POST['wpfepp-form-'.$this->id.'-submit'] ) ) {
			$result  = $this->handle_submission( $_POST, 'html' );
			$current = ( $result['success'] ) ? ( $this->get_post( $result['post_id'] ) ) : array_map( array( $this, 'stripslashes' ), $_POST );
		}
		if(isset($_POST['wpfepp-form-'.$this->id.'-save'])){
			$result  = $this->save_draft($_POST, 'html');
			$current = ($result['success']) ? ($this->get_post($result['post_id'])) : array_map(array($this, 'stripslashes'), $_POST);
		}

		//Finally print the form
		do_action( 'wpfepp_do_before_print_form' );
		do_action( 'wpfepp_do_before_'. $this->id .'_print_form' );	

		//Here we check if user has access to form if form has limits
		if( !empty($this->extended['limit_number']) && $this->extended['limit_number'] > 0 ){
			$currentuserid = get_current_user_id();
			$user_numb_post_meta = '_rhf_user_submit_counter_form_'.$this->id;
			$author_number_post_package = get_user_meta( $currentuserid, $user_numb_post_meta, true );

			if (is_numeric($author_number_post_package) || $author_number_post_package === 0){
				if ($author_number_post_package <= 0) {	
					$message = (!empty($this->extended['limit_number_message'])) ? $this->extended['limit_number_message'] : __('You reached the limit for submit to this form', 'wpfepp-plugin');
					echo '<div class="wpsm_box blue_type nonefloat_box rh_wpeff_noticebox">';					
						echo do_shortcode($message);
						$redirectlink = (!empty($this->extended['limit_number_redirect'])) ? $this->extended['limit_number_redirect'] : '';	
						if($redirectlink){
							$redirectlink = esc_url($redirectlink);
							echo '<script>jQuery(window).load(function() {
								window.setTimeout(function(){window.location.href="'.$redirectlink.'";},3500);
							});</script>';
							echo '<br /><br />';
							_e('You will be redirected now...', 'wpfepp-plugin');
						}						
					echo '</div>';							
					return;
				}else{			
					$message = (!empty($this->extended['pre_limit_message'])) ? $this->extended['pre_limit_message'] : __('You have %%count%% available submissions', 'wpfepp-plugin');
					$message = str_replace('%%count%%', $author_number_post_package, $message);
					echo '<div class="wpsm_box blue_type nonefloat_box rh_wpeff_noticebox">';					
						echo do_shortcode($message);
					echo '</div>';										
				}
			}else{
				$message = (!empty($this->extended['pre_limit_message'])) ? $this->extended['pre_limit_message'] : __('You have %%count%% available submissions', 'wpfepp-plugin');
				$message = str_replace('%%count%%', $this->extended['limit_number'], $message);
				echo '<div class="wpsm_box blue_type nonefloat_box rh_wpeff_noticebox">';
					echo do_shortcode($message);
				echo '</div>';
			}			
		}				

		$this->print_form( $current, $result );

	}

	/**
	 * A simple wrapper for PHP's own stripslashes() function. It makes sure that the original function is applied only on strings.
	 *
	 * @param  string $str The string on which stripslashes() needs to be called.
	 * @return string The input string with slashed stripped out.
	 **/
	private function stripslashes( $str ){
		if( ! is_string( $str ) )
			return $str;
		return stripslashes( trim( $str ) );
	}

	/**
	 * Prints the form populated with existing values ($current) and displays errors if any exist.
	 *
	 * @param  array $current An array containing the current field values. These values are either fetched from the DB using $this->get_post() or from the $_POST array.
	 * @param  array $result The array obtained from handle_submission(). It contains a success flag, a list of errors/messages and id of the newly generated post.
	 * @return string The input string with slashed stripped out.
	 **/
	private function print_form( $current = false, $result = false ) {
		include( 'partials/form.php' );
	}

	/**
	 * Saves a post as draft.
	 * 
	 * @param  array $post_data containing all the data from the form.
	 * @param  string $error_format Dictates the format of the returned errors. Set to HTML by default.
	 * @return array An array consisting of a boolean flag that tells whether post insertion was successful, all the form errors and in case of successful post insertion, the post id. This structure has been used so that this function can be conviniently used with ajax.
	 **/
	public function save_draft( $post_data, $error_format = 'html' ) {
		$return_val = array( 'success' => false, 'errors' => array() );

		do{
			if( ! $this->settings['enable_drafts'] ) {
				$return_val['errors']['form'][] = __( "Drafts are not allowed!", "wpfepp-plugin" );
				break;
			}

			$captcha_enabled = $this->settings['captcha_enabled'];
			
			if( $captcha_enabled && $this->captcha->keys_available() && $this->post_status( $post_data['post_id']) == 'new' ) {
				$captcha_check = $this->captcha->check_response( $post_data['g-recaptcha-response'] );
				if( ! $captcha_check ) {
					$return_val['errors']['form'][] = __( "Captcha response incorrect", "wpfepp-plugin" );
					break;
				}
			}

			$post_data['post_status'] = 'draft';
			$result = $this->insert_post( $post_data );
			if( is_wp_error( $result ) ) {
				$return_val['errors']['form'][] = $result->get_error_message();
				break;
			}

			$return_val['success'] = true;
			$return_val['post_id'] = $result;
			$preview_link = sprintf( '<br/><a target="_blank" href="%s">%s</a>', WPFEPP_Post_Previews::make_preview_link( $result ), __( "Preview", "wpfepp-plugin" ) );
			$preview_link = ( $this->post_type_obj->public) ? $preview_link : '';
			$return_val['errors']['form'][] = sprintf( __( "The post has been saved successfully! %s", "wpfepp-plugin" ), $preview_link );
		}
		while(0);

		if( $error_format == 'html' )
			$return_val['errors'] = $this->format_errors( $return_val['errors'] );

		return $return_val;
	}

	private function post_status( $post_id ) {
		if( $post_id < 1 )
			return 'new';
		return get_post_status( $post_id );
	}

	/**
	 * Runs a number of checks on user-submitted data and attempts to insert it into the database using helper functions.
	 *
	 * @param  array $post_data containing all the data from the form.
	 * @param  string $error_format Dictates the format of the returned errors. Set to HTML by default.
	 * @return array An array consisting of a boolean flag that tells whether post insertion was successful, all the form errors and in case of successful post insertion, the post id. This structure has been used so that this function can be conviniently used with ajax.
	 **/
	public function handle_submission( $post_data, $error_format = 'html' ){
		$return_val = array( 'success' => false, 'errors' => array() );
		$user_defined_errors = get_option( 'wpfepp_errors' );
		$old_status = $this->post_status( $post_data['post_id'] );

		if( wpfepp_current_user_has( $this->settings['instantly_publish'] ) )
			$post_data['post_status'] = 'publish';
		else
			$post_data['post_status'] = 'pending';

		do {
			if( !$this->valid ) {
				$return_val['errors']['form'][] = __( 'This form no longer exists.', 'wpfepp-plugin' );
				break;
			}

			if( !wp_verify_nonce( $post_data['_wpnonce'], 'wpfepp-form-'.$post_data['form_id'].'-nonce' ) ) {
				$return_val['errors']['form'][] = __( 'You failed the security check.', 'wpfepp-plugin' );
				break;
			}
			if( $post_data['post_id'] != -1 && !$this->current_user_can_edit( $post_data['post_id'] ) ) {
				$return_val['errors']['form'][] = __( 'You do not have permission to modify this post.', 'wpfepp-plugin' );
				break;
			}
			$restriction_errors = $this->check_restrictions( $post_data );
			if( count( $restriction_errors ) ) {
				$return_val['errors'] = $restriction_errors;
				$return_val['errors']['form'][] = $user_defined_errors['form'];
				break;
			}

			$captcha_enabled = $this->settings['captcha_enabled'];
			if($captcha_enabled && $this->captcha->keys_available() && $this->post_status($post_data['post_id']) == 'new'){
				$captcha_check = $this->captcha->check_response($post_data['g-recaptcha-response']);
				if(!$captcha_check){
					$return_val['errors']['form'][] = __('Captcha response incorrect', 'wpfepp-plugin');
					break;
				}
			}

			$copyscape = new WPFEPP_CopyScape($this->version);
			$copyscape_enabled = $this->settings['copyscape_enabled'];
			$copyscape_block 	= $copyscape->option('block');
			$column_msg = '';
			
			if( $copyscape_enabled ) {
				$passed = $copyscape->passed($post_data);
				
				if(is_wp_error($passed)){
					$column_msg = __('ERROR: ', 'wpfepp-plugin') . $passed->get_error_message();
					$passed 	= true;
				}
				else {
					$column_msg 	= ($passed) ? __('passed', 'wpfepp-plugin') : __('failed', 'wpfepp-plugin');
				}

				$this->fields[WPFEPP_CopyScape::$meta_key] = array('type' => 'custom_field');
				$post_data[WPFEPP_CopyScape::$meta_key] = $column_msg;

				if( ! $passed ) {
					if($copyscape_block){
						$return_val['errors']['form'][] 	= $user_defined_errors['copyscape'];
						break;
					}
					else {
						$post_data['post_status'] = 'pending';
					}
				}
			}
			else {
				$this->fields[WPFEPP_CopyScape::$meta_key] = array( 'type' => 'custom_field' );
				$post_data[WPFEPP_CopyScape::$meta_key] = '';
			}

			$result = $this->insert_post($post_data);
			if( is_wp_error( $result ) ) {
				$return_val['errors']['form'][] = $result->get_error_message();
				break;
			}

			$return_val['success'] = true;
			$return_val['post_id'] = $result;
			$return_val['redirect_url'] = ($this->settings['redirect_url']) ? $this->settings['redirect_url'] : false;
			$action = ( $old_status == 'new' || $old_status == 'draft' ) ? 'created' : 'updated';
			$preview_link 	= sprintf( '<a target="_blank" href="%s">%s</a>', WPFEPP_Post_Previews::make_preview_link( $result ), __( "Preview", "wpfepp-plugin" ) );
			$permalink = sprintf( '<a target="_blank" href="%s">%s</a>', get_post_permalink( $result ), __( "View", "wpfepp-plugin" ) );
			$final_link = ( $post_data['post_status'] == 'publish' ) ? $permalink : $preview_link;
			$final_link = ( $this->post_type_obj->public ) ? $final_link : '';

			if( 'created' == $action ) {
				//Here we save limit number to user meta to check if user has access to form later
				if(!empty($post_data['form_id']) && !empty($post_data['form_limit_number']) && $post_data['form_limit_number']>0){
					$currentuserid = get_current_user_id();
					$default_counter = (int)$post_data['form_limit_number'] - 1;	
					$user_numb_post_meta = '_rhf_user_submit_counter_form_'.$post_data['form_id'];
					$author_number_post_package = get_user_meta( $currentuserid, $user_numb_post_meta, true );
					
					if ( !$author_number_post_package) {
						update_user_meta( $currentuserid, $user_numb_post_meta, $default_counter );	
					} else {
						$author_number_post_package = (int)$author_number_post_package - 1;
						update_user_meta( $currentuserid, $user_numb_post_meta, $author_number_post_package );	
					}					
				}
				$display_message = __( "The post has been created successfully. %s %s %s", "wpfepp-plugin" );
			}
			else if( 'updated' == $action ){
				$display_message = __("The post has been updated successfully. %s %s %s", "wpfepp-plugin" );
			}

			$return_val['errors']['form'][] = sprintf(
				$display_message,
				sprintf( '<br/><a class="wpfepp-continue-editing" href="%s">%s</a>', '?wpfepp_action=edit&wpfepp_post='.$post_data['post_id'], __( "Continue Editing", "wpfepp-plugin" ) ),
				$final_link,
				sprintf( '<a class="wpfepp-addnew-editing" href="javascript:window.location.reload()">%s</a>', __( "Add new", "wpfepp-plugin" ) )
			);

			$this->user_defined_actions( array_merge( $post_data, array( 'post_id'=> $result, 'action' => $action ) ) );

		} while (0);

		if($error_format == 'html')
			$return_val['errors'] = $this->format_errors($return_val['errors']);

		return $return_val;
	}

	/**
	 * Checks if the user-submitted data meets the minimum requirements of the form, set by the site administrator in the options panel.
	 * 
	 * This function goes through each field and makes sure that the submitted data corresponding to that field ($post_data[$key]) meets the requirements set by the admin. Note that the key of each field is used as the name attribute in the form.
	 *
	 * @param array $post_data An array containing all the data from the form.
	 * @return array $errors A multidimmensional array of errors. Each array member contains all the errors for a particular field.
	 **/
	private function check_restrictions( $post_data ) {
		$errors = array();
		$user_defined_errors = get_option( 'wpfepp_errors' );
		if( wpfepp_current_user_has( $this->settings['no_restrictions']) )
			return $errors;

		foreach ($this->get_fields() as $key => $field) {

			if( wpfepp_is_field_supported( $field['type'], $this->post_type ) && isset( $field['enabled'] ) && $field['enabled'] && isset( $field['required']) && $field['required'] ) {
				$stripped_value = isset($post_data[$key]) ? $post_data[$key] : "";
				if( is_string( $stripped_value ) )
					$stripped_value = strip_tags( trim( $stripped_value ) );
				if( empty( $stripped_value ) || $stripped_value == -1 || ( is_array( $stripped_value ) && ! count( $stripped_value ) ) )
					$errors[$key][] = $user_defined_errors['required'];
				if( isset( $field['min_words'] ) && is_numeric( $field['min_words'] ) && $this->word_count( $stripped_value ) < $field['min_words'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['min_words']), $field['min_words']);
				if( isset($field['max_words']) && is_numeric($field['max_words']) && $this->word_count($stripped_value) > $field['max_words'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['max_words']), $field['max_words']);
				if( isset($field['min_symbols']) && is_numeric($field['min_symbols']) && $this->symbol_count($stripped_value) < $field['min_symbols'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['min_symbols']), $field['min_symbols']);
				if( isset($field['max_symbols']) && is_numeric($field['max_symbols']) && $this->symbol_count($stripped_value) > $field['max_symbols'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['max_symbols']), $field['max_symbols']);
				if( isset($field['min_count']) && is_numeric($field['min_count']) && $this->segment_count($stripped_value) < $field['min_count'] )
						$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['min_segments']), $field['min_count']);	
				if( isset($field['max_count']) && is_numeric($field['max_count']) && $this->segment_count($stripped_value) > $field['max_count'] )
						$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['max_segments']), $field['max_count']);					
				if( isset($field['max_links']) && is_numeric($field['max_links']) && $this->count_links($post_data[$key]) > $field['max_links'] )
					$errors[$key][] = sprintf(str_replace('{0}', '%s', $user_defined_errors['max_links']), $field['max_links']);
			}
		}
		return $errors;
	}

	/**
	 * Counts links (anchor tags) in a string with the help of a simple regular expression.
	 *
	 * @param string $str An HTML string.
	 * @return integer Number of links in the input HTML string.
	 **/
	private function count_links( $str ){
		return preg_match_all( '/<\s*\ba\b.*?href/', $str, $matches );
	}

	/**
	 * Inserts user submitted data into DB with WordPress' own wp_insert_post(). If insertion is successful, sets terms, saves meta and creates the thumbnail.
	 *
	 * @param array $post_data An array containing all the data from the form. It is actually $_POST.
	 * @return int or WP_Error Either the ID of the new post is returned or a WP_Error object.
	 **/
	private function insert_post( $post_data ){
		$post = array('post_type' => $this->post_type, 'post_status' => $post_data['post_status']);
		$custom_fields = $hierarchical_taxonomies = $non_hierarchical_taxonomies = $custom_prefixes = $custom_postfixes = $custom_postfixes_del = $custom_prefixes_del = array();
		$post_formats = $thumbnail = 0;
		$wc_tax_attrs = wpfepp_get_attribute_taxonomies();
		$form_fields = $this->get_fields();
		
		foreach ( $form_fields as $key => $field ) { //$key - field name; $field - options array (e.g. enabled[1|0], type, label, element, widget_label)
			switch ( $field['type'] ) {
				case 'title':
					if( !empty( $post_data[$key] ) ) $post['post_title'] = $this->sanitize( $post_data[$key], $field );
					break;
				case 'content':
					if( !empty($post_data[$key])) $post['post_content'] = $this->sanitize($post_data[$key], $field);
					break;
				case 'excerpt':
					if( !empty($post_data[$key])) $post['post_excerpt'] = $this->sanitize($post_data[$key], $field);
					break;
				case 'thumbnail':
					if( !empty( $post_data[$key] ) && $post_data[$key] != -1 ) $thumbnail = $post_data[$key];
					break;
				case 'hierarchical_taxonomy':
					if( !empty( $post_data[$key] ) ) {
						$term_ids = $post_data[$key];
						if( is_array( $term_ids ) && count( $term_ids ) ) {
							
							if(!empty($wc_tax_attrs) && in_array($key, $wc_tax_attrs)) {
								$term_sluges = array();
								
								for($i =0; $i < count($term_ids); $i++) {
									$the_term = get_term_by('id', $term_ids[$i], $key);
									if(!is_wp_error($the_term)){
										$term_sluges[] = $the_term->slug;
									}
								}
								$hierarchical_taxonomies[$key] = $term_sluges;
							} else {
								$hierarchical_taxonomies[$key] = $term_ids;
							}
						} elseif( is_string( $term_ids ) ) {
							$hierarchical_taxonomies[$key] = array_map('trim', explode(',', $term_ids));
						}
					}
					break;
				case 'post_formats':
					if(!empty($post_data[$key]) && $post_data[$key]) $post_formats = $post_data[$key];
					break;
				case 'non_hierarchical_taxonomy':
					if(!empty($post_data[$key])) $non_hierarchical_taxonomies[$key] = $post_data[$key];
					break;
				case 'sku':
					$custom_fields['_sku'] = !empty($post_data[$key]) ? $this->sanitize($post_data[$key], $field) : '';
					break;
				case 'price':
					$custom_fields['_regular_price'] = !empty($post_data[$key]) ? $this->sanitize($post_data[$key], $field) : '';
					break;
				case 'sale_price':
					$custom_fields['_sale_price'] = !empty($post_data[$key]) ? $this->sanitize($post_data[$key], $field) : '';
					break;
				case 'product_options':
					if(!empty($post_data[$key])) {
						$custom_fields['_virtual'] = (!empty($post_data[$key .'_virtual']) && $post_data[$key .'_virtual'] == 1) ? 'yes' : 'no';
						$custom_fields['_downloadable'] = (!empty($post_data[$key .'_downloadable']) && $post_data[$key .'_downloadable'] == 1) ? 'yes' : 'no';
						$custom_fields['_download_limit'] = !empty($post_data[$key .'_download_limit']) ? absint($post_data[$key .'_download_limit']) : ''; // 0 or blank = unlimited
						$custom_fields['_download_expiry'] = !empty($post_data[$key .'_download_expiry']) ? absint($post_data[$key .'_download_expiry']) : ''; // 0 or blank = unlimited
						$custom_fields['_download_type'] = !empty($post_data[$key .'_download_type']) ? sanitize_text_field($post_data[$key .'_download_type']) : '';
						$file_names = !empty($post_data[$key .'_file_names']) ? $post_data[$key .'_file_names'] : array();
						$file_urls = !empty($post_data[$key .'_file_urls']) ? wp_unslash(array_map('trim', $post_data[$key .'_file_urls'])) : array();
						$custom_fields['_downloadable_files'] = $this->downloadable_product_files($file_names, $file_urls);
						$custom_fields['_external'] = (!empty($post_data[$key .'_external']) && $post_data[$key .'_external'] == 1) ? 'yes' : 'no';
						$custom_fields['_product_url'] = !empty($post_data[$key .'_product_url']) ? sanitize_text_field($post_data[$key .'_product_url']) : '';
						$custom_fields['_product_image_gallery'] = !empty($post_data['product_image_gallery']) ? sanitize_text_field($post_data['product_image_gallery']) : '';
						$custom_fields['_visibility'] = 'visible'; // Product Catalog visibility: Catalog/search
						
					}
					break;
				case 'custom_field':
					if(isset($field['element']) && $field['element'] == 'map') {
						$custom_fields['_rh_gmw_map_hidden_adress'] = !empty($post_data[$key]) ? $this->sanitize($post_data[$key], $field) : '';
					}
					
					if(!empty($field['custom_prefix']) && !empty($post_data[$key])) {
						$custom_prefix = ($post_data['post_id'] != -1) ? get_post_meta($post_data['post_id'], $key.'_prefix', true) : '';
						if('' == $custom_prefix) {
							$post_data[$key] = $field['custom_prefix'].$post_data[$key];
							$custom_prefixes[] = $key.'_prefix';
						}
					} elseif((!empty($field['custom_prefix']) && empty($post_data[$key])) || (empty($field['custom_prefix']) && !empty($post_data[$key]))) {
						$custom_prefixes_del[] = $key.'_prefix';
					}
					
					if(!empty($field['custom_postfix']) && !empty($post_data[$key])) {
						$custom_postfix = ($post_data['post_id'] != -1) ? get_post_meta($post_data['post_id'], $key.'_postfix', true) : '';
						if('' == $custom_postfix) {
							$post_data[$key] = $post_data[$key].$field['custom_postfix'];
							$custom_postfixes[] = $key.'_postfix';
						}
					} elseif((!empty($field['custom_postfix']) && empty($post_data[$key])) || (empty($field['custom_postfix']) && !empty($post_data[$key]))) {
						$custom_postfixes_del[] = $key.'_postfix';
					}
					
					$custom_fields[$key] = !empty($post_data[$key]) ? $this->sanitize($post_data[$key], $field) : '';

					// hidden map API fields
					if(!empty($post_data['wpfepp_start_geo_lat'])) $custom_fields['medafi_rhmap_latitude'] = $this->sanitize($post_data['wpfepp_start_geo_lat'], array('strip_tags' => 'all'));
					if(!empty($post_data['wpfepp_start_geo_long'])) $custom_fields['medafi_rhmap_longitude'] = $this->sanitize($post_data['wpfepp_start_geo_long'], array('strip_tags' => 'all'));
					break;
				default:
					break;
			}
		}
		
		if(isset($post_data['[price]'])) :
			// save product price
			$sale_price = isset($custom_fields['_sale_price']) ? $custom_fields['_sale_price'] : '';
			$date_from = isset($custom_fields['_sale_price_dates_from']) ? $custom_fields['_sale_price_dates_from'] : '';
			$date_to = isset($custom_fields['_sale_price_dates_to']) ? $custom_fields['_sale_price_dates_to'] : '';
			
			if($date_to && !$date_from) {
				$custom_fields['_sale_price_dates_from'] = strtotime('NOW', current_time( 'timestamp'));
			}
			if('' !== $sale_price && '' === $date_to && '' === $date_from){
				$custom_fields['_price'] = $custom_fields['_sale_price'];
			}else{
				if(isset($custom_fields['_regular_price'])){
					$custom_fields['_price'] = $custom_fields['_regular_price'];
				}else{
					$custom_fields['_price'] = '';
				}
			}
			if('' !== $sale_price && $date_from && $date_from < strtotime('NOW', current_time('timestamp'))) {
				$custom_fields['_price'] = $custom_fields['_sale_price'];
			}
			if ($date_to && $date_to < strtotime('NOW', current_time('timestamp'))) {
				$custom_fields['_price'] = $custom_fields['_regular_price'];
				$custom_fields['_sale_price_dates_from'] = $custom_fields['_sale_price_dates_to'] = $custom_fields['_sale_price'] = '';
			}
		endif;

		// hidden field for check if form is being paid
		if(!empty($post_data['wpfepp_paid_post'])) $custom_fields['_wpfepp_paid_post'] = $this->sanitize($post_data['wpfepp_paid_post'], array('strip_tags' => 'all'));

		// hidden field for check if form is limited form
		if(!empty($post_data['form_limit_number'])) $custom_fields['_form_limit_number'] = $this->sanitize($post_data['form_limit_number'], array('strip_tags' => 'all'));		

		if( $post_data['form_id'] > 0 ) {
			$custom_fields['wpfepp_submit_with_form_id'] = $this->sanitize($post_data['form_id'], array('strip_tags' => 'all'));
		}		
		
		if( $post_data['post_id'] != -1 ) {
			$post['ID'] = $post_data['post_id'];
			$post['comment_status'] = get_post_field('comment_status', $post_data['post_id']);
			
			if( get_post_status( $post_data['post_id'] ) == 'publish' ) {
				$post_time_unix = get_post_time('U', false, $post_data['post_id']);
				$post_date = date( 'Y-m-d H:i:s', $post_time_unix );
				$post['post_date'] = $post_date;
				$post['post_date_gmt'] = get_gmt_from_date( $post_date );
			}
		}

		$post_id = wp_insert_post( $post, true );
		
		if( !is_wp_error( $post_id ) ) {

			$tax_attrs = array();

			foreach ( $hierarchical_taxonomies as $tax => $tax_terms ) {
				wp_set_post_terms( $post_id, $tax_terms, $tax, false );
				
				// searches Product Attribut taxonomy, check if they have terms and collects them into array
				if( !empty( $wc_tax_attrs ) && in_array( $tax, $wc_tax_attrs ) && !empty( $tax_terms[0] ) ) {
					$tax_attrs[] = $tax;
				}
			}
			
			if('product' == get_post_type($post_id)) {
				// adds Attribut taxonomy options to Product
				wpfepp_product_attributes_options($post_id, $tax_attrs);
			}
			
			foreach ($non_hierarchical_taxonomies as $tax => $tax_terms) {
				wp_set_post_terms( $post_id, $tax_terms, $tax, false);
			}
			
			//adds Prefix meta to Post
			if(!empty($custom_prefixes)){
				foreach($custom_prefixes as $index => $custom_prefix) {
					update_post_meta($post_id, $custom_prefix, true);
				}
			}
			//adds Postfix meta to Post
			if(!empty($custom_postfixes)){
				foreach($custom_postfixes as $index => $custom_postfix) {
					update_post_meta($post_id, $custom_postfix, true);
				}
			}
			//deletes Prefix meta from Post
			if(!empty($custom_prefixes_del)){
				foreach($custom_prefixes_del as $index => $custom_prefix_del) {
					delete_post_meta($post_id, $custom_prefix_del);
				}
			}
			//deletes Postfix meta from Post
			if(!empty($custom_postfixes_del)){
				foreach($custom_postfixes_del as $index => $custom_postfix_del) {
					delete_post_meta($post_id, $custom_postfix_del);
				}
			}
			
			/**
			* Fires custom_fields before save them
			*/
			do_action( 'wpfepp_before_update_custom_field', $post_id, $custom_fields );

			foreach ($custom_fields as $meta_key => $value) {
				// grant permission to any newly added files on any existing orders for this product prior to saving
				if($meta_key == '_downloadable_files') {
					add_action( 'woocommerce_process_product_file_download_paths', $post_id, 0, $value );
				}
				// sets/removes product relationship with woocommerce taxonomy 'product_type' and its term 'external'
				if($meta_key == '_external' && $value == 'yes') {
					wp_set_object_terms($post_id, 'external', 'product_type');
				} 
				if($meta_key == '_external' && $value == 'no') {
					wp_set_object_terms($post_id, 'simple', 'product_type');
				}
				// - add/update custon fields to/of post
				update_post_meta( $post_id, $meta_key, $value );
			}

			$ext_thumbnail = ( isset($post_data['parser_rehub_offer_product_url']) && !empty( $post_data['parser_rehub_offer_product_url'] ) ) ? $this->get_ext_image_id( $post_data['parser_rehub_offer_product_url'], $post_data['title'], $post_id ) : '';
			
			if( $thumbnail ) {
				set_post_thumbnail( $post_id, $thumbnail );
			} elseif( !empty( $ext_thumbnail ) ) {
				set_post_thumbnail( $post_id, $ext_thumbnail );
			} else {
				delete_post_thumbnail( $post_id );
			}
			set_post_format( $post_id, $post_formats );
		}
		return $post_id;
	}

	/**
	 * Takes a string and removes potentially harmful HTML and PHP tags from it. This function is run right before post insertion and the writer of the post is not shown any errors.
	 *
	 * @param string $value The string from which harmful tags are to be stripped.
	 * @param array $field The settings array for this field.
	 * @return string The stripped string
	 **/
	private function sanitize( $value, $field ) {

		if( isset( $field['unixtime'] ) && $field['unixtime'] == 1 )
			$value = strtotime( $value );

		if( isset( $field['strip_tags'] ) && $field['strip_tags'] == 'all' )
			$value = wp_strip_all_tags( $value );
		
		if( isset($field['strip_tags']) && $field['strip_tags'] == 'unsafe' )
			$value = wp_kses( $value, $this->get_whitelist() );
		
		if( isset($field['nofollow']) && $field['nofollow'] )
			$value = stripslashes(wp_rel_nofollow($value));

		return $value;
	}

	/**
	 * Fetches a post using the function get_post() and prepares it for display within our form.
	 *
	 * @param integer $post_id The id of the WordPress post to be fetched.
	 * @return array An array containing the post data in the formal that can directly be used by our form.
	 **/
	private function get_post( $post_id ){
		$form_post = array();
		$post_obj = get_post( $post_id );
		foreach ( $this->get_fields() as $key => $field ) {
			switch ( $field['type'] ) {
				case 'title':
					$form_post[$key] = $post_obj->post_title;
					break;
				case 'content':
					$form_post[$key] = $post_obj->post_content;
					break;
				case 'excerpt':
					$form_post[$key] = $post_obj->post_excerpt;
					break;
				case 'thumbnail':
					$form_post[$key] = get_post_thumbnail_id( $post_id );
					break;
				case 'sku':
					$form_post[$key] = get_post_meta( $post_id, '_sku', true );
					break;
				case 'price':
					$form_post[$key] = get_post_meta( $post_id, '_regular_price', true );
					break;
				case 'sale_price':
					$form_post[$key] = get_post_meta( $post_id, '_sale_price', true );
					break;
				case 'product_options':
					$form_post[$key . '_virtual'] = get_post_meta( $post_id, '_virtual', true );
					$form_post[$key . '_downloadable'] = get_post_meta( $post_id, '_downloadable', true );
					$form_post[$key . '_download_limit'] = get_post_meta( $post_id, '_download_limit', true );
					$form_post[$key . '_download_expiry'] = get_post_meta( $post_id, '_download_expiry', true );
					$form_post[$key . '_download_type'] = get_post_meta( $post_id, '_download_type', true );
					$form_post[$key . '_downloadable_files'] = get_post_meta( $post_id, '_downloadable_files', true );
					$form_post[$key . '_external'] = get_post_meta( $post_id, '_external', true );
					$form_post[$key . '_product_url'] = get_post_meta( $post_id, '_product_url', true );
					$form_post[$key . 'product_image_gallery'] = get_post_meta( $post_id, '_product_image_gallery', true );
					break;
				case 'hierarchical_taxonomy':
					$form_post[$key] = wp_get_post_terms( $post_id, $key, array( "fields" => "ids" ) );
					break;
				case 'post_formats':
					$form_post[$key] = get_post_format( $post_id );
					break;
				case 'non_hierarchical_taxonomy':
					$term_names = wp_get_post_terms( $post_id, $key, array( "fields" => "names" ) );
					$form_post[$key] = implode( ', ', $term_names );
					break;
				case 'custom_field':
					$post_meta = get_post_meta( $post_id, $key, true );
					$form_post[$key] = ( $post_meta )?$post_meta:"";
					break;
				default:
					break;
			}
		}
		$form_post['post_id'] = $post_id;
		return $form_post;
	}

	/**
	 * Prints all the restrictions for a field (for instance outputs 'required=""' on required fields). Used while printing form elements.
	 *
	 * @param array $field An array containing field data.
	 * @return string A string containing all the restrictions for the field, ready to be inserted in the form element.
	 **/
	private function print_restrictions($field){

		$restriction_array = array();

		if(isset($field['multiple']) && $field['multiple'])
			$restriction_array[] = 'multiple';

		if( wpfepp_current_user_has($this->settings['no_restrictions']) ){
			$restriction_string = implode(' ', $restriction_array);
			return $restriction_string;
		}

		if(isset($field['required']) && $field['required']){
			if($field['type'] == 'thumbnail')
				$restriction_array[] = 'hiddenrequired="1"';
			else
				$restriction_array[] = 'required';
		}
		if(isset($field['min_words']) && $field['min_words'] && is_numeric($field['min_words']))
			$restriction_array[] = sprintf('minwords="%d"', $field['min_words']);
		if(isset($field['max_words']) && $field['max_words'] && is_numeric($field['max_words']))
			$restriction_array[] = sprintf('maxwords="%d"', $field['max_words']);
		if(isset($field['min_symbols']) && $field['min_symbols'] && is_numeric($field['min_symbols']))
			$restriction_array[] = sprintf('minsymbols="%d"', $field['min_symbols']);
		if(isset($field['max_symbols']) && $field['max_symbols'] && is_numeric($field['max_symbols']))
			$restriction_array[] = sprintf('maxsymbols="%d"', $field['max_symbols']);
		if(isset($field['min_links']) && $field['min_links'] && is_numeric($field['min_links']))
			$restriction_array[] = sprintf('minlinks="%d"', $field['min_links']);
		if(isset($field['max_links']) && $field['max_links'] && is_numeric($field['max_links']))
			$restriction_array[] = sprintf('maxlinks="%d"', $field['max_links']);
		if(isset($field['min_count']) && $field['min_count'] && is_numeric($field['min_count']))
			$restriction_array[] = sprintf('minsegments="%d"', $field['min_count']);
		if(isset($field['max_count']) && $field['max_count'] && is_numeric($field['max_count']))
			$restriction_array[] = sprintf('maxsegments="%d"', $field['max_count']);
		if(isset($field['min_number']) && $field['min_number'] && is_numeric($field['min_number']) && $field['element'] == 'inputnumb')
			$restriction_array[] = sprintf('min="%d"', $field['min_number']);
		if(isset($field['max_number']) && $field['max_number'] && is_numeric($field['max_number']) && $field['element'] == 'inputnumb')
			$restriction_array[] = sprintf('max="%d"', $field['max_number']);
		if(isset($field['step_count']) && $field['step_count'] && is_numeric($field['step_count']) && $field['element'] == 'inputnumb')
			$restriction_array[] = sprintf('step="%d"', $field['step_count']);
		
		$restriction_string = implode(' ', $restriction_array);
		return $restriction_string;
	}

	/**
	 * Takes a multidimensional array and converts every second level array (the errors for an individual field) into an HTML string for output.
	 *
	 * @param array $errors A 2D array of errors.
	 * @return array A 1D array in which each element is an HTML string containing
	 **/
	private function format_errors($form_errors){
		$errors_formatted = array();
		foreach ($form_errors as $key => $field_errors) {
			$errors_formatted[$key] = '<ul><li>'.implode('</li><li>', $field_errors).'</li></ul>';
		}
		return $errors_formatted;
	}

	/**
	 * Counts comma seperated segments in a string. Used for counting the terms of non-hierarichal taxonomies.
	 *
	 * @param string $str The string of terms
	 * @return integer Number of comma-seperated terms
	 **/
	private function segment_count($str){
		if(!trim($str))
			return 0;

		$segments = explode(',', trim($str));
		return count($segments);
	}

	/**
	 * Prints out the image source of a thumbnail
	 *
	 * @param integer $image_id The thumbnail ID.
	 **/
	private function output_thumbnail($image_id){
		if(empty($image_id) || $image_id == -1)
			return;
		echo wp_get_attachment_image( $image_id, array(200,200) );
	}

	/**
	 * Builds and returns a whitelist array of safe HTML tags and attributes to be used with wp_kses
	 *
	 * @return array An array of safe HTML tags and their attributes.
	 **/
	private function get_whitelist() {
		$allowed_attrs = array(
			'class' => array(),
			'id' => array(),
			'style' => array(),
			'title' => array()
		);
		$allowed_html = array(
			'a' => array_merge( $allowed_attrs, array( 'href' => array() ) ),
			'img' => array_merge( 
				$allowed_attrs,
				array(
					'src' => array(),
					'alt' => array(),
					'width' => array(),
					'height' => array()
				)
			),
			'ins' 	=> array_merge( $allowed_attrs, array( 'datetime' => array() ) ),
			'del' => array_merge($allowed_attrs, array( 'datetime' => array() ) ),
			'p' => $allowed_attrs,
			'br' => $allowed_attrs,
			'em' => $allowed_attrs,
			'b' => $allowed_attrs,
			'ol' => $allowed_attrs,
			'ul' => $allowed_attrs,
			'li'	=> $allowed_attrs,
			'table' => $allowed_attrs,
			'tbody' => $allowed_attrs,
			'tr' => $allowed_attrs,
			'td' => $allowed_attrs,
			'div' => $allowed_attrs,
			'code' => $allowed_attrs,
			'pre' => $allowed_attrs,
			'sub' => $allowed_attrs,
			'sup' => $allowed_attrs,
			'span' => $allowed_attrs,
			'q' => $allowed_attrs,
			'code' => $allowed_attrs,
			'h1' => $allowed_attrs,
			'h2' => $allowed_attrs,
			'h3' => $allowed_attrs,
			'h4' => $allowed_attrs,
			'h5' => $allowed_attrs,
			'h6' => $allowed_attrs,
			'abbr' => $allowed_attrs,
			'strong' => $allowed_attrs,
			'blockquote' => $allowed_attrs,
			'address' => $allowed_attrs,
		);
		$allowed_html = apply_filters('wpfepp_form_'.$this->id.'_safe_tags', $allowed_html);
		return $allowed_html;
	}

	/**
	 * A simple getter function for the fields attribute.
	 *
	 * @return array An array containing all field data.
	 **/
	public function get_fields(){
		return $this->fields;
	}

	/**
	 * A simple getter function for the settings attribute.
	 *
	 * @return array An array containing all field data.
	 **/
	public function get_settings(){
		return $this->settings;
	}

	/**
	 * A simple getter function for the emails attribute.
	 *
	 * @return array An array containing email data.
	 **/
	public function get_emails(){
		return $this->emails;
	}
	
	/**
	 * A simple getter function for the extended settings attribute.
	 *
	 * @return array An array containing all extended data.
	 **/
	public function get_extended(){
		return $this->extended;
	}

	/**
	 * Simple getter function for checking form validity
	 *
	 * @return boolean
	 **/
	public function valid()
	{
		return $this->valid;
	}

	/**
	 * Simple getter function for post type
	 *
	 * @return string Post type of the form.
	 **/
	public function post_type()
	{
		return $this->post_type;
	}

	/**
	 * Frontend location scripts.
	 *
	 * Enqueue frontend styles and javascripts for map location module.
	 *
	 * @since 3.2
	 */
	public function enqueue_frontend_location_scripts( $post_id ) {

		$current_start_geo_lat = get_post_meta( $post_id, 'medafi_rhmap_latitude', true );
		$current_start_geo_long = get_post_meta( $post_id, 'medafi_rhmap_longitude', true );
		
		$protocol  = is_ssl() ? 'https' : 'http';
			
		//register google maps api
		if (apply_filters('wpfepp_google_maps_api', true)) {

			//Build Google API url. elements can be modified via filters
			$maps_url_args = array(
				'libraries' => 'places',
				'key' => $this->extended['map_google_key'],
				'region' => $this->extended['map_google_country'],
				'language' => $this->extended['map_google_lang'],
				'sansor' => 'false'
	        );
			
			$maps_api_args = array(
				'protocol'	=> $protocol,
				'url_base' => '://maps.googleapis.com/maps/api/js?',
				'url_data' => http_build_query( apply_filters( 'wpfepp_google_maps_api_args', $maps_url_args ), '', '&amp;' ),
			);
			
			$google_url = apply_filters( 'wpfepp_google_maps_api', $maps_api_args, $this->extended );

			wp_enqueue_script( 'google-maps', implode( '', $google_url ) , array( 'jquery' ), $this->version, false );
		}
		
		wp_localize_script( 'wpfepp-script', 'wpfeppl', array(
			'start_point'	=> $this->extended['map_start_location'],
			'start_geo_lat'	=> !empty($current_start_geo_lat) ? $current_start_geo_lat : $this->extended['start_geo_lat'],
			'start_geo_long' => !empty($current_start_geo_long) ? $current_start_geo_long : $this->extended['start_geo_long'],
			'enable_city_suggest' => $this->extended['enable_city_suggest'],
			'enable_map' => $this->extended['enable_map'],
			'l10n' => array(
				'locked' => __( 'Lock Pin Location', 'wpfepp-plugin' ),
				'unlocked' => __( 'Unlock Pin Location', 'wpfepp-plugin' )
			)
		) );
		
		if ( $this->extended['enable_city_suggest'] )
			wp_enqueue_script( 'geo-tag-txt', plugins_url( '/static/js/geo-tag-text.js', dirname(__FILE__) ), array( 'jquery' ), $this->version, false );
		
		if ( $this->extended['enable_map'] )
			wp_enqueue_script( 'mapifyf', plugins_url( '/static/js/mapify.js', dirname(__FILE__) ), array( 'jquery' ), $this->version, false );
	}
	
	/**
	 * Prints out user defined form fields with the help of do_action() function provided by WordPress.
	 **/
	private function user_defined_fields( $current_values ) {
		do_action( 'wpfepp_form_'.$this->id.'_fields', $current_values );
		do_action( 'wpfepp_form_fields', $current_values, $this );
	}

	/**
	 * Gives users the ability to perform custom operations on the post data after a post has been successfully added/updated.
	 **/
	private function user_defined_actions( $post_data ) {
		do_action( 'wpfepp_form_'.$this->id.'_actions', $post_data );
		do_action( 'wpfepp_form_actions', $post_data, $this );
	}

	/**
	 * By default WordPress does not allow subscribers and contributors to edit their own posts. This function aims rectifies this problem.
	 *
	 * @param string $action The action to check.
	 * @param int Post id.
	 * @return boolean Whether or not the current user can perform the specified action.
	 **/
	private function current_user_can_edit( $post_id ) {
		$post_author_id = get_post_field( 'post_author', $post_id );
		$current_user = wp_get_current_user();
		return ( $post_author_id == $current_user->ID || current_user_can('edit_post', $post_id) );
	}

	private function word_count($str){
		$str = preg_replace('/\s+/', ' ', strip_tags($str));
		return ( substr_count($str, ' ') + 1 );
	}
	
	private function symbol_count($str){
		$str = mb_strlen($str, get_bloginfo('charset'));
		return $str;
	}

	private function print_spaces($times){
		for ( $i=0; $i < $times; $i++ ) { 
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
	}
	
	private function hierarchical_taxonomy_options( $args, $current, $level = -1 ){
		$level++;
		$terms = get_terms( $args );
		if( empty( $terms ) || is_wp_error( $terms ) )
			return;
		foreach( $terms as $term_key => $term ) { ?>
			<option value="<?php echo $term->term_id; ?>" <?php if( is_array($current) && in_array($term->term_id, $current) ) echo 'selected="selected"'; ?> >
				<?php $this->print_spaces( $level ); ?><?php echo $term->name; ?>
			</option>
			<?php 
				$args = array_merge( $args, array( 'parent' => $term->term_id ) );
				$this->hierarchical_taxonomy_options( $args, $current, $level );
		}
	}

	private function downloadable_product_files( $file_names, $file_urls ){
	
		$files = array();
		$file_url_size = sizeof( $file_urls );
		$allowed_file_types = apply_filters( 'woocommerce_downloadable_file_allowed_mime_types', get_allowed_mime_types() );

		for ( $i = 0; $i < $file_url_size; $i ++ ) {
			if ( ! empty( $file_urls[ $i ] ) ) {
				// Find type and file URL
				if ( 0 === strpos( $file_urls[ $i ], 'http' ) ) {
					$file_is  = 'absolute';
					$file_url = esc_url_raw( $file_urls[ $i ] );
				} elseif ( '[' === substr( $file_urls[ $i ], 0, 1 ) && ']' === substr( $file_urls[ $i ], -1 ) ) {
					$file_is  = 'shortcode';
					$file_url = sanitize_text_field( $file_urls[ $i ] );
				} else {
					$file_is = 'relative';
					$file_url = sanitize_text_field( $file_urls[ $i ] );
				}

				$file_name = sanitize_text_field( $file_names[ $i ] );
				$file_hash = md5( $file_url );

				// Validate the file extension
				if ( in_array( $file_is, array( 'absolute', 'relative' ) ) ) {
					$file_type  = wp_check_filetype( strtok( $file_url, '?' ), $allowed_file_types );
					$parsed_url = parse_url( $file_url, PHP_URL_PATH );
					$extension  = pathinfo( $parsed_url, PATHINFO_EXTENSION );

					if ( ! empty( $extension ) && ! in_array( $file_type['type'], $allowed_file_types ) ) {
						WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The downloadable file %s cannot be used as it does not have an allowed file type. Allowed types include: %s', 'wpfepp-plugin' ), '<code>' . basename( $file_url ) . '</code>', '<code>' . implode( ', ', array_keys( $allowed_file_types ) ) . '</code>' ) );
						continue;
					}
				}

				// Validate the file exists
				if ( 'relative' === $file_is ) {
					$_file_url = $file_url;
					if ( '..' === substr( $file_url, 0, 2 ) || '/' !== substr( $file_url, 0, 1 ) ) {
						$_file_url = realpath( ABSPATH . $file_url );
					}

					if ( ! apply_filters( 'woocommerce_downloadable_file_exists', file_exists( $_file_url ), $file_url ) ) {
						WC_Admin_Meta_Boxes::add_error( sprintf( __( 'The downloadable file %s cannot be used as it does not exist on the server.', 'wpfepp-plugin' ), '<code>' . $file_url . '</code>' ) );
						continue;
					}
				}

				$files[ $file_hash ] = array(
					'name' => $file_name,
					'file' => $file_url
				);
			}
		}
		return $files;
	}
	
	private function get_ext_image_id( $img_url, $title, $post_id ) {
		
        if( !$img_url )
            return;

		$img_file = WPFEPP_Image::get_img_file( $img_url, $title );
		
		if ( !$img_file )
			return;
		
		$filetype = wp_check_filetype( basename( $img_file ), null );
		$attachment = array(
			'guid' => $img_file,
			'post_mime_type' => $filetype['type'],
			'post_title' => $title,
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $img_file, $post_id );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $img_file );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		return $attach_id;
	}
	
}

?>