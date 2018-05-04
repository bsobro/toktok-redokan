<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

/**
 * Manages the form fields tab of the forms page.
 *
 * @package WPFEPP
 * @since 2.3.0
 **/
class WPFEPP_Tab_Form_Fields extends WPFEPP_Tab
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
		add_action('admin_init', array($this, 'save_fields'));
		add_action( 'wp_ajax_wpfepp_create_custom_field_ajax', array($this, 'create_custom_field_ajax') );
	}

	/**
	 * An ajax callback function that creates a new custom field with default settings.
	 **/
	public function create_custom_field_ajax(){
		$label = stripslashes( esc_attr($_POST['label']) );
		$meta_key = $_POST['meta_key'];
		$defaults = $this->form_manager->get_defaults();
		
		if( !($label && $meta_key) ){
			$response = array('success' => false, 'error' => __('You missed a required item.', 'wpfepp-plugin'));
			die(json_encode($response));
		}
		
		if( $meta_key != sanitize_key($meta_key) ){
			$response = array('success' => false, 'error' => __('The meta key is invalid.', 'wpfepp-plugin'));
			die( json_encode( $response ) );
		}

		$field_settings = array_merge(array('widget_label' => $label, 'label'=> $label), $defaults['custom_field']);
		ob_start();
		$this->display_item($meta_key, $field_settings, false);
		$widget_html = ob_get_clean();
		$response = array('success' => true, 'widget_html' => $widget_html);
		die(json_encode($response, JSON_HEX_QUOT));
	}

	/**
	 * When users hit the submit button this function handles the request and redirects them back to the page.
	 **/
	public function save_fields(){
		if(!$this->form_manager->is_page())
			return;

		$result = 0;

		if( $_GET['action'] == 'edit' && isset($_POST['update-form-fields']) && isset($_POST['form_fields']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpfepp-update-form-fields') ){
			$form_fields = $this->validate($_POST['form_fields']);
			$result = $this->db->update_form_fields($_GET['form'], $form_fields);
			$sendback = esc_url_raw( add_query_arg( array( 'updated' => $result ) ) );
			wp_redirect($sendback);
		}
	}

	/**
	 * A special validation function for this tab. It uses the validator to validate each item.
	 *
	 * @var array $fields A two dimmensional array in which each array contains the settings of a particular front-end form field like title.
	 **/
	private function validate($fields) {
		foreach ($fields as $key => $field) {
			$fields[$key] = $this->validator->validate($field);
		}
		return $fields;
	}

	/**
	 * Outputs the HTML of a single dragable widget. Every widget corresponds to a front-end form field for instance title. Makes extensive use of WordPress' settings API.
	 *
	 * @var string $field_key Field key.
	 * @var string $field Current settings of the field.
	 **/
	public function display_item( $field_key, $field, $post_type ) {
			$page 			= 'wpfepp_'.$field_key.'_widget';
			$section 		= $page.'_section';
			$callback 	= array($this->renderer, 'render');
			$args 			= array('group' => 'form_fields', 'subgroup' => $field_key, 'curr' => $field);

			add_settings_section( $section, '', null, $page );

			add_settings_field( 'enabled', __( 'Enabled', 'wpfepp-plugin' ), $callback, $page, $section, 
				array_merge( array( 'id' => 'enabled', 'type' => 'bool' ), $args)
			);
			add_settings_field('required', __( 'Required', 'wpfepp-plugin' ), $callback, $page, $section, 
				array_merge( array( 'id' => 'required', 'type' => 'bool'), $args )
			);
			if( !in_array( $field['type'], array( 'product_options' ) ) ) {
				add_settings_field( 'label', __( 'Label', 'wpfepp-plugin' ), $callback, $page, $section, 
					array_merge( array( 'id' => 'label', 'type' => 'text' ), $args)
				);
			}
			if( $field['type'] == 'custom_field' ) {
				add_settings_field( 'element', __( 'Form Element', 'wpfepp-plugin' ), $callback, $page, $section, 
					array_merge(
						array(
							'id' => 'element',
							'type' => 'select',
							'items' => array( 
								'input' => __( "Text field", "wpfepp-plugin" ), 
								'textarea' => __( "Textarea", "wpfepp-plugin" ), 
								'checkbox' => __( "Checkbox", "wpfepp-plugin" ), 
								'select' => __( "Select", "wpfepp-plugin" ), 
								'radio' => __( "Radio buttons", "wpfepp-plugin" ), 
								'email' => __( "Email", "wpfepp-plugin" ), 
								'url' => __( "URL field", "wpfepp-plugin" ), 
								'image_url' => __( "File upload", "wpfepp-plugin" ), 
								'inputdate' => __( "Date field", "wpfepp-plugin" ), 
								'inputtime' => __( "Time field", "wpfepp-plugin" ), 
								'inputnumb' => __( "Numeric field", "wpfepp-plugin" ), 
								'map' => __( "Address input", "wpfepp-plugin" ), 
								'image_galery' => __( "Image galery", "wpfepp-plugin" ) )
						), $args
					)
				);
				add_settings_field( 'step_count', __( 'Step Count', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'step_count',
							'type' => 'int',
							'desc' => __('The step attribute specifies the legal number interval that you want the user to enter.', 'wpfepp-plugin')
						), $args
					)
				);	
				add_settings_field( 'min_number', __( 'Min value', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'min_number',
							'type' => 'int',
							'desc' => __('Minimum number that you want the user to enter.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field( 'max_number', __( 'Max value', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'max_number',
							'type' => 'int',
							'desc' => __('Maximum number that you want the user to enter.', 'wpfepp-plugin')
						), $args
					)
				);							
				add_settings_field( 'choices', __( 'Choices', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'choices',
							'type' => 'textarea',
							'desc' => __('The choices for select and radio elements. One per line. Key value pairs can be added like this: key|Value', 'wpfepp-plugin')
						), $args 
					)
				);
			}
			
			add_settings_field( 'width', __( 'Field Width', 'wpfepp-plugin' ), $callback, $page, $section, 
				array_merge(
					array(
						'id' => 'width',
						'type' => 'text',
						'desc' => __( 'Width in pixels or percentage. (e.g. 300px)', 'wpfepp-plugin' )
					), $args 
				)
			);
			if( in_array( $field['type'], array( 'thumbnail' ) ) ) {
				add_settings_field('parser', __( 'Parser Image', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge( 
						array( 
							'id' => 'parser', 
							'type' => 'bool', 
							'desc' => __( 'You can enable auto parsing image from external URL in this checkbox. You must have a custom field with key "rehub_offer_product_url" in the form which will be used for external URL.', 'wpfepp-plugin' ) 
						), $args 
					)
				);
				add_settings_field('parser_width', __( 'Min. Width', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge( 
						array( 
							'id' => 'parser_width', 
							'type' => 'int', 
							'desc' => __( 'Minimal size (width / height) of images for parsing. Default value: 500', 'wpfepp-plugin' ) 
						), $args )
				);
			}
			if( in_array( $field['type'], array( 'title', 'content', 'excerpt', 'custom_field' ) ) ) {
				add_settings_field('min_words', __( 'Min Words', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge( array( 'id' => 'min_words', 'type' => 'int'), $args )
				);
				add_settings_field( 'max_words', __( 'Max Words', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge( array( 'id' => 'max_words', 'type' => 'int' ), $args )
				);
			}
			if(in_array($field['type'], array('sku', 'price', 'sale_price', 'custom_field'))) {
				add_settings_field('min_symbols', __( 'Min Symbols', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(array('id' => 'min_symbols', 'type' => 'int'), $args)
				);
				add_settings_field('max_symbols', __( 'Max Symbols', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(array('id' => 'max_symbols', 'type' => 'int'), $args)
				);				
			}
			
			if(in_array($field['type'], array('title', 'content', 'excerpt', 'sku', 'price', 'sale_price', 'custom_field'))) {
				add_settings_field( 'strip_tags', __( 'Strip Tags', 'wpfepp-plugin' ), $callback, $page, $section, 
					array_merge(
						array(
							'id' 	=> 'strip_tags',
							'type' 	=> 'select',
							'desc' 	=> __('These HTML tags will be removed before the post is inserted into the database.', 'wpfepp-plugin'),
							'items'	=> array('none' => __('None', 'wpfepp-plugin'), 'unsafe' => __('Unsafe', 'wpfepp-plugin'), 'all' => __('All', 'wpfepp-plugin'))
						), $args
					)
				);
			}
			
			if( $field['type'] == 'product_options' ) {
				add_settings_field( 'prod_gallery', __( 'Product Gallery', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'prod_gallery', 
							'type' => 'bool', 
							'desc' => __('Check this to allow Product Gallery in products.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field( 'virtual_prod', __( 'Virtual Product', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'virtual_prod', 
							'type' => 'bool', 
							'desc' => __('Check this to set up Product type as Virtual.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field( 'extern_prod', __( 'External Product', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'extern_prod', 
							'type' => 'bool', 
							'desc' => __('Check this to set up Product type as External.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field( 'down_product', __( 'Downloadable Product', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'down_product', 
							'type' => 'bool', 
							'desc' => __('Check this to set up Product type as Downloadable.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field( 'down_type', __('Download Type', 'wpfepp-plugin'), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'down_type', 
							'type' => 'bool', 
							'desc' => __('Check this to display Download Type option for the schema.', 'wpfepp-plugin')
						), $args
					)
				);
			}			
			
			if( $field['type'] == 'custom_field' ) {
				add_settings_field( 'multiple', __('Multiple', 'wpfepp-plugin'), $callback, $page, $section, 
					array_merge(
						array(
							'id' 	=> 'multiple',
							'type' 	=> 'bool',
							'desc' 	=> __('Enable it if you need the Select field with multiple choices.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field( 'custom_prefix', __('Prefix', 'wpfepp-plugin'), $callback, $page, $section, 
					array_merge(
						array(
							'id' => 'custom_prefix',
							'type' => 'text',
							'desc' => __( 'Some text or symbol which will be added at the start of the Meta value. To set break space use <code>&amp;nbsp;</code>', 'wpfepp-plugin' )
						), $args
					)
				);
				add_settings_field( 'custom_postfix', __('Postfix', 'wpfepp-plugin'), $callback, $page, $section, 
					array_merge(
						array(
							'id' => 'custom_postfix',
							'type' => 'text',
							'desc' => __( 'Some text or symbol which will be added at the end of the Meta value. To set break space use <code>&amp;nbsp;</code>', 'wpfepp-plugin' )
						), $args
					)
				);
				add_settings_field( 'attachdata', __('Data type', 'wpfepp-plugin'), $callback, $page, $section, 
					array_merge(
						array(
							'id' 	=> 'attachdata',
							'type' 	=> 'select',
							'items' => array( 'atturl' => __('Attachment URL', 'wpfepp-plugin'), 'attid' => __('Attachment ID', 'wpfepp-plugin')),
							'desc' 	=> __('Select how to save data from this field.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field( 'unixtime', __( 'Unix timestamp', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' => 'unixtime', 
							'type' => 'bool', 
							'desc' => __('Some meta fields need it. E.g. "_sale_price_dates_from" and "_sale_price_dates_to"', 'wpfepp-plugin')
						), $args
					)
				);
			}

			if( $field['type'] == 'content' ) {
				add_settings_field('media_button', __( 'Display Media Button', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' 	=> 'media_button',
							'type' 	=> 'bool',
							'desc'	=> __('The media button will show up only if the user has permission to upload media.', 'wpfepp-plugin')
						), $args
					)
				);
				add_settings_field('max_links', __( 'Max Links', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(array('id' => 'max_links', 'type' => 'int'), $args)
				);
				add_settings_field('element', __( 'Form Element', 'wpfepp-plugin' ), $callback, $page, $section, 
					array_merge(
						array(
							'id' 	=> 'element',
							'type' 	=> 'select',
							'items' => array( 'richtext' => __( 'Rich Text Editor', 'wpfepp-plugin' ), 'plaintext' => __( 'Textarea', 'wpfepp-plugin' ) )
						),
						$args
					)
				);
			}
			
			if(in_array($field['type'], array('content', 'excerpt', 'custom_field'))) {
				add_settings_field('nofollow', __( 'Nofollow All Links', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(array('id' => 'nofollow', 'type' => 'bool'), $args)
				);
			}

			if($field['type'] == 'hierarchical_taxonomy') {
				add_settings_field('multiple', __( 'Allow Multiple Selections', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(array('id' => 'multiple', 'type' => 'bool'), $args)
				);
				add_settings_field('hide_empty', __( 'Hide Empty', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(array('id' => 'hide_empty', 'type' => 'bool'), $args)
				);
				add_settings_field('exclude', __( 'Exclude', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' 	=> 'exclude',
							'type' 	=> 'text',
							'desc' 	=> __('A comma-seperated list of term IDs that you want to exclude.', 'wpfepp-plugin')
						),
						$args
					)
				);
				add_settings_field('include', __( 'Include', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' 	=> 'include',
							'type' 	=> 'text',
							'desc' 	=> __('A comma-seperated list of term IDs that you want to include.', 'wpfepp-plugin')
						),
						$args
					)
				);
			}

			if($field['type'] == 'non_hierarchical_taxonomy') {
				add_settings_field('min_count', __( 'Min Count', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' 	=> 'min_count',
							'type' 	=> 'int',
							'desc' 	=> __('Minimum number of terms that you want the user to enter.', 'wpfepp-plugin')
						),
						$args
					)
				);
				add_settings_field('max_count', __( 'Max Count', 'wpfepp-plugin' ), $callback, $page, $section,
					array_merge(
						array(
							'id' 	=> 'max_count',
							'type' 	=> 'int',
							'desc' 	=> __('Maximum number of terms that you want the user to enter.', 'wpfepp-plugin')
						),
						$args
					)
				);
			}
			if(in_array($field['type'], array('title', 'content', 'excerpt', 'sku', 'price', 'sale_price', 'custom_field'))) {
				add_settings_field('strip_tags', __( 'Strip Tags', 'wpfepp-plugin' ), $callback, $page, $section, 
					array_merge(
						array(
							'id' 	=> 'strip_tags',
							'type' 	=> 'select',
							'desc' 	=> __('These HTML tags will be removed before the post is inserted into the database.', 'wpfepp-plugin'),
							'items'	=> array('none' => __('None', 'wpfepp-plugin'), 'unsafe' => __('Unsafe', 'wpfepp-plugin'), 'all' => __('All', 'wpfepp-plugin'))
						),
						$args
					)
				);
			}
			add_settings_field('prefix_text', __( 'Instructions', 'wpfepp-plugin' ), $callback, $page, $section, 
				array_merge(
					array(
						'id' 	=> 'prefix_text',
						'type' 	=> 'textarea',
						'desc' 	=> 'The text that you want to place above this field.'
					), 
					$args
				)
			);
			if(in_array($field['type'], array('content', 'excerpt', 'thumbnail', 'price', 'hierarchical_taxonomy', 'non_hierarchical_taxonomy', 'custom_field'))) {
				add_settings_field('fallback_value', __( 'Fallback Value', 'wpfepp-plugin' ), $callback, $page, $section, 
					array_merge(
						array(
							'id' 	=> 'fallback_value',
							'type' 	=> 'textarea',
							'desc' 	=> 'The value to use when this field is disabled. You can leave this empty. For content, excerpt and custom fields enter text. For hierarchical taxonomies (e.g. category) enter comma-seperated IDs, for non-hierarchical taxonomies (e.g. tag) add a comma-seperated list of slugs, and for post thumb add an image ID.'
						), 
						$args
					)
				);
			}
			$dashicon_yes = '<span class="dashicons dashicons-yes"></span>';
			?>
				<li class="wpfepp-widget-container<?php echo wpfepp_is_field_supported( $field['type'], $post_type ) ? '' : ' hidden'; ?>">
					<div class="wpfepp-widget-head">
						<strong><?php echo stripslashes( esc_html( $field['widget_label'] ) ); ?></strong> : 
						<span class="wpfepp-expand"><span class="dashicons dashicons-arrow-down"></span></span>
						<span><?php if( $field['enabled'] ) :  echo __( 'Enabled', 'wpfepp-plugin' ), $dashicon_yes; endif; ?></span>
						<span><?php if( $field['required'] ) :  echo __( 'Required', 'wpfepp-plugin' ), $dashicon_yes; endif; ?></span>
						<span><?php if( isset($field['fallback_value']) && !$field['enabled'] ) : echo __( 'Hidden', 'wpfepp-plugin' ), $dashicon_yes; endif; ?></span>
						<?php if( $field['type'] == 'custom_field' ) : ?>
							<span class="wpfepp-custom-field-delete"><span class="dashicons dashicons-trash"></span></span>
							<span><?php _e( 'Meta Field:', 'wpfepp-plugin' ); ?> <code><?php echo $field_key; ?></code></span>
						<?php endif; ?>
						<?php if( isset($field['fallback_value']) && !empty($field['fallback_value']) ) : ?>
							<span><?php _e( 'Default value:', 'wpfepp-plugin' ); ?> <code><?php echo $field['fallback_value']; ?></code></span>
						<?php endif; ?>
					</div>
					<div class="wpfepp-widget-body">
						<?php do_settings_sections( $page ); ?>
						<input type="hidden" name="form_fields[<?php echo $field_key; ?>][type]" value="<?php echo $field['type']; ?>">
						<input type="hidden" name="form_fields[<?php echo $field_key; ?>][widget_label]" value="<?php echo stripslashes( esc_attr( $field['widget_label'] ) ); ?>">
					</div>
				</li>
			<?php
	}

	/**
	 * Loops through the two dimmensional array of field settings and prints out their HTML using the display_item() function above.
	 **/
	public function display_items(){
		$form 	= $this->db->get($_GET['form']);
		$fields = $form['fields'];
		$post_type = $form['post_type'];

		foreach($fields as $field_key => $field){
			$this->display_item($field_key, $field, $post_type);
		}
	}

	/**
	 * Outputs the contents of the tab with the help of display_items().
	 **/
	public function display() {
		?>
			<div class="wpfepp-op-content">
				<form method="POST">
					<ul id="wpfepp-sortable">
						<?php $this->display_items(); ?>
					</ul>
					<?php wp_nonce_field( 'wpfepp-update-form-fields', '_wpnonce', false, true ); ?>
					<?php submit_button(__('Update Form', 'wpfepp-plugin'), 'primary', 'update-form-fields'); ?>
				</form>
			</div>
			
			<div class="wpfepp-op-sidebar">
				<div class="side-block custom-field-builder">
					<h3><?php _e( "Custom Fields", "wpfepp-plugin" ); ?></h3>
					<form class="wpfepp-ajax-form">
						<label><?php _e( "Label", "wpfepp-plugin" ); ?></label>
						<input class="wpfepp-required" type="text" name="label" />
						<label><?php _e( "Meta Key", "wpfepp-plugin" ); ?></label>
						<input class="wpfepp-required" type="text" name="meta_key" />
						<input type="hidden" name="action" value="wpfepp_create_custom_field_ajax" />
						<?php submit_button( __( "Add field", "wpfepp-plugin" ), 'primary', 'wpfepp-add-custom-field' ); ?>
					</form>
				</div>
				<?php if( WPFEPP_REHUB ){ ?>
				<div class="side-block custom-field-builder">
					<h3><?php _e( "REhub Theme Fields", "wpfepp-plugin" ); ?></h3>
					<ul class="wpfepp-rehub-fields">
						<li data-field="is_editor_choice"><?php _e( "Editor choice (val: 0-3)", "wpfepp-plugin" ); ?></li>
						<li data-field="rh_post_image_gallery"><?php _e( "Image gallery", "wpfepp-plugin" ); ?></li>
						<li data-field="medafi_rhmap"><?php _e( "Map meta field", "wpfepp-plugin" ); ?></li>
						<li data-field="rehub_offer_coupon_mask"><?php _e( "Mask coupon code (val: 0-1)", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_product_coupon"><?php _e( "Offer product Coupon code", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_coupon_date"><?php _e( "Offer product Coupon date", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_product_desc"><?php _e( "Offer product Description", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_product_thumb"><?php _e( "Offer product Image URL", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_logo_url"><?php _e( "Offer product Logo URL", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_name"><?php _e( "Offer product Name", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_product_price_old"><?php _e( "Offer product Old price", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_product_price"><?php _e( "Offer product Price", "wpfepp-plugin" ); ?>
						<li data-field="rehub_offer_product_url"><?php _e( "Offer product URL", "wpfepp-plugin" ); ?>
						<li data-field="rh_post_image_videos"><?php _e( "Post video", "wpfepp-plugin" ); ?></li>
						<?php if( class_exists( 'MetaDataFilter' ) ) : ?>
							<li data-field="meta_data_filter_cat"><?php _e( "MDFT filter category", "wpfepp-plugin" ); ?></li>
						<?php endif; ?>
						<?php if( class_exists( 'WooCommerce' ) ) : ?>
							<li data-field="rehub_woo_coupon_code"><?php _e( "WC product Coupon code", "wpfepp-plugin" ); ?>
							<li data-field="rehub_woo_coupon_date"><?php _e( "WC product Coupon date", "wpfepp-plugin" ); ?>
							<li data-field="rehub_woo_coupon_url"><?php _e( "WC product Coupon URL", "wpfepp-plugin" ); ?>
							<li data-field="rehub_woo_coupon_coupon_img_url"><?php _e( "WC Coupon Image URL", "wpfepp-plugin" ); ?>
						<?php endif; ?>
					</ul>
				</div>
			<?php } ?>
			</div>
		<?php
	}
}

?>