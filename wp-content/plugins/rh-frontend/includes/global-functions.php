<?php
/* Debug Plugin function */
function wpfepp_log( $value, $variable = '' ) {
	if ( true === WP_DEBUG ) {
		if ( is_array( $value ) || is_object( $value ) ) {
			error_log( $variable .' = '. print_r( $value, true ) );
		} else {
			error_log( $variable .' = '. $value );
		}
	}
}

/**
 * Creates an associative array of roles in which each element has role slug as the key and role name as value e.g. 'administrator' => 'Administrator'
 *
 * @return array Array of roles.
 **/
function wpfepp_get_roles(){
	global $wp_roles;
	$roles = $wp_roles->roles;
	$rtn_arr 	= array();
	foreach ($roles as $key => $role) {
		$rtn_arr[$key] = $role['name'];
	}
	return $rtn_arr;
}

/**
 * Checks if the current user has a role for which the value of in the passed array is 1.
 *
 * @var array $roles An array in which the value corresponsponding each role is either 1 or 0.
 * @return bool A boolean variable indicating whether the current user has a role for which the value is 1.
 **/
function wpfepp_current_user_has( $roles ){
	$current_user = wp_get_current_user();
	foreach ( $current_user->roles as $role ) {
		if( isset($roles[$role]) && $roles[$role] )
			return true;
	}
	return false;
}

/**
 * Prints a list of roles in our special checkbox format.
 *
 * @var string $name Value for the name attribute of the checkboxes.
 * @var array $checked An array indicating which roles to check.
 **/
function wpfepp_print_roles_checkboxes($name, $checked){
	$roles = wpfepp_get_roles();
	?>
		<?php foreach ($roles as $key => $role): ?>
			<input type="hidden" name="<?php echo $name; ?>[<?php echo $key; ?>]" value="0" />
			<input type="checkbox" id="<?php echo $name; ?>[<?php echo $key; ?>]" name="<?php echo $name; ?>[<?php echo $key; ?>]" value="1" <?php if( isset($checked[$key]) ) checked( $checked[$key] ); ?> />
			<label for="<?php echo $name; ?>[<?php echo $key; ?>]"><?php echo $role; ?></label>
			<br/>
		<?php endforeach; ?>
	<?php
}

/**
 * Fetches an array of roles from wpfepp_get_roles() and convert it into a settings array.
 *
 * @return array An array of settings e.g. array( 'administrator' => '1', editor => '0' ... )
 **/
function wpfepp_prepare_default_role_settings(){
	$rtn_arr = array();
	global $wp_roles;
	$roles = $wp_roles->roles;
	foreach ($roles as $key => $role) {
		$rtn_arr[$key] = false;
	}
	return $rtn_arr;
}

/**
 * A recursive function that checks an array for missing keys. If any are found, inserts default values from the second array.
 *
 * @var array $current The array to be checked.
 * @var array $default The array from which we can get the missing values.
 * @return array The patched array.
 **/
function wpfepp_update_array($current, $default) {
	$current = ($current && is_array($current)) ? $current : array();
	foreach ($default as $key => $value) {
		if( !array_key_exists($key, $current) ){
			$current[$key] = $value;
		}
		elseif( is_array( $value ) ){
			$current[$key] = wpfepp_update_array( $current[$key], $value );
		}
	}
	return $current;
}

function wpfepp_update_form_fields( $current, $default, $default_custom ) {
	$current = wpfepp_update_array( $current, $default );
	foreach ($current as $key => $field) {
		if( $field['type'] == 'custom_field' )
			$current[$key] = wpfepp_update_array( $field, $default_custom );
	}
	return $current;
}

/**
 * Checks to see if a field is supported by the current post type and theme.
 *
 * @var string $post_type The post type of the current form.
 * @var string $field_type The type of field we want to check.
 * @return bool A boolean variable indicating whether or not the field is supported.
 **/
function wpfepp_is_field_supported( $field_type, $post_type ) {
	
	if($field_type == 'thumbnail' ) {
		return ( post_type_supports($post_type, 'thumbnail') && get_theme_support('post-thumbnails') );
	}
	elseif($field_type == 'post_formats') {
		$formats = get_theme_support('post-formats');
		return ( post_type_supports($post_type, 'post-formats') && is_array($formats) && count($formats) && is_array($formats[0]) && count($formats[0]) );
	}
	elseif($field_type == 'content'){
		return post_type_supports($post_type, 'editor');
	}
	elseif($field_type == 'title' || $field_type == 'excerpt') {
		return post_type_supports($post_type, $field_type);
	}
	elseif(($field_type == 'sku' || $field_type == 'price' || $field_type == 'sale_price' || $field_type == 'product_options') && !class_exists('Woocommerce')) {
		return false;
	}
	return true;
}

function wpfepp_choices($str){
	$choices = array();

	if(empty($str))
		return $choices;
	$lines = explode("\n", $str);
	$count = 0;
	foreach ($lines as $line) {
		if(!empty($line)){
			$line_val = explode("|", $line);
			if(count($line_val) > 1){
				$choices[$count]['key'] = $line_val[0];
				$choices[$count]['val'] = $line_val[1];
			}
			else{
				$choices[$count]['key'] = $line_val[0];
				$choices[$count]['val'] = $line_val[0];
			}
			$count++;
		}
	}
	return $choices;
}

/**
 * Output the html of a form and includes the necessary scripts and stylesheets.
 *
 * @var int $form_id Form ID.
 * @author 
 **/
function wpfepp_submission_form($form_id) {
	echo do_shortcode( sprintf('[wpfepp_submission_form form="%s"]', $form_id) );
}

/**
 * Output the html of a post table and includes the necessary scripts and stylesheets.
 *
 * @var int $form_id Form ID.
 */
function wpfepp_post_table($form_id) {
	echo do_shortcode( sprintf('[wpfepp_post_table form="%s"]', $form_id) );
}

/**
 * Gets post type names
 *
 * returns arr $types
 */
function wpfepp_get_post_types() {
	$types = get_post_types( array('show_ui'=>true), 'names', 'and' );
	unset( $types['attachment'] );
	return $types;
}

function wpfepp_get_post_type_settings() {
	$settings = array();
	$types = wpfepp_get_post_types();
	foreach ( $types as $key => $type ) {
		$settings[$type] = false;
	}
	return $settings;
}

if( !function_exists( 'ot_get_media_post_ID' ) ) {
	function ot_get_media_post_ID() {
		return -1;
	}
}

/**
 * Runs on update of the plugin
 */
function wpfepp_check_update() {
	require plugin_dir_path( __FILE__ ) . 'class-update-checker.php';
}

/**
 * Pass Map data to Geo My WP plugin
 */
function rh_gmw_pass_map_data($postid, $custom_fields) {
	$address = !empty($custom_fields['_rh_gmw_map_hidden_adress']) ? $custom_fields['_rh_gmw_map_hidden_adress'] : '';
	if(!empty($address) && function_exists('gmw_update_post_location')) {
		// update post location
		gmw_update_post_location($postid, stripslashes($address));
	}
}
add_action('wpfepp_before_update_custom_field', 'rh_gmw_pass_map_data', 10, 2);

/**
 * Get WC attribute taxonomies.
 *
 * @return array of attribut names
 */
function wpfepp_get_attribute_taxonomies() {
	$arr_names = array();
	if ( false === ( $attribute_taxonomies = get_transient( 'wc_attribute_taxonomies' ) ) ) {
		global $wpdb;
		$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies order by attribute_name ASC;" );
		set_transient( 'wc_attribute_taxonomies', $attribute_taxonomies );
	}
	foreach( $attribute_taxonomies as $attribute_taxonomie ) {
		$arr_names[] = 'pa_' . $attribute_taxonomie->attribute_name;
	}
	return $arr_names;
}

/** 
 * Save Product attribut options
 */
function wpfepp_product_attributes_options( $post_id, $tax_attrs ) {
	
	if( empty( $post_id ) )
		return;
	$atts = array();
	if( !empty( $tax_attrs ) ) {
		for( $i = 0, $cc = count( $tax_attrs ); $i < $cc; ++$i ) {
			$tax_name = sanitize_title( $tax_attrs[$i] );
			$atts[ $tax_name ] = array(
				'name' => $tax_name,
				'value' => '',
				'position' => $i,
				'is_visible' => 1,
				'is_variation' => 0,
				'is_taxonomy' => 1
			);
		}
	} 
	/** 
	* Filter attribut options
	*/
	$attributes = apply_filters( 'wpfepp_product_attributes_options', $atts, $post_id, $tax_attrs );
	update_post_meta( $post_id, '_product_attributes', $attributes );
}

/* 
 * Takes multiple media IDs | ID | URL and returns preview HTML for them
 */
function wpfepp_media_preview_html( $media_data, $media_type ) {
	
	if ( ! $media_data )
		return;
	$html = '';
	if ( $media_type == 'attids' ) {
		foreach ( explode( ',', $media_data ) as $id ) {
			$html .= wp_get_attachment_image( trim( $id ), 'thumbnail');
		}
	} 
	if ( $media_type == 'attid' ) {
		$html = ( $media_data ) ? wp_get_attachment_image( $media_data, 'thumbnail', true ) : '';
	}
	if ( $media_type == 'atturl' ) {
		$id = wpfepp_get_attachment_id_by_url( $media_data );
		$html = ( $id ) ? wp_get_attachment_image( $id, 'thumbnail', true ) : '';
	}
	return $html;
}

/* 
 * Retrives Attachment ID by File URL
 */
function wpfepp_get_attachment_id_by_url( $url ) {

	$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
	if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
		return;
	}
	global $wpdb;
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
	return $attachment[0];
}
 
?>