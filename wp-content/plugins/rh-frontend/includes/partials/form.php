<?php
	$form_errors = ( isset( $result['errors'] ) ) ? $result['errors'] : "";
	$submission_status = ( isset( $result['success'] ) ) ? $result['success'] : "";
	$final_post_id 	= ( isset( $current['post_id'] ) ) ? $current['post_id'] : "-1";
	$form_width = ( isset( $this->settings['width'] ) && !empty( $this->settings['width'] ) ) ? ( 'max-width:'. $this->settings['width'] .';') : '';
	$fields = $this->get_fields();
	$parser_on = ( !$current['thumbnail'] && $fields['thumbnail']['parser'] && current_user_can( 'upload_files' ) ) ? true : false;
	$parser_size = ( $fields['thumbnail']['parser_width'] && !empty( $fields['thumbnail']['parser_width'] ) ) ? $fields['thumbnail']['parser_width'] : '500';
?>
<form class="wpfepp wpfepp-form" method="POST" style="<?php echo $form_width ?>">
	<div class="wpfepp-message <?php echo ( $submission_status ) ? "success" : "error"; ?>
		<?php echo isset( $form_errors['form'] ) ? 'display' : ''; ?>">
		<?php echo isset( $form_errors['form'] ) ? $form_errors['form'] : ""; ?>
	</div>
	<div class="wpfepp-form-fields">
		<?php foreach( $fields as $field_key => $field ) : ?>
			<?php if ( wpfepp_is_field_supported( $field['type'], $this->post_type ) ) :?>
				<?php if ( $field['enabled'] ) : ?>
				<?php
					$field_errors = isset( $form_errors[$field_key] ) ? $form_errors[$field_key] : "";
					$element_width = ( isset( $field['width'] ) && !empty( $field['width'] ) ) ? ( 'width:'. $field['width'] .';' ) : ''; 
					$field_current = isset($current[$field_key]) ? $current[$field_key] : '';
					$required_field = ($field['required']) ? '<span class="wpfepp-form-field-errors">*</span>' : ''; 
					$print_restrictions = $this->print_restrictions( $field );
					$unique_key = 'form-'. $this->id .'-'. $field_key;
				?>
				<div class="wpfepp-<?php echo $field_key; ?>-field-container wpfepp-form-field-container" style="<?php echo $element_width; ?>">
					<?php if($field['type'] !== 'product_options') { ?>
					<label for="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-form-field-label"><?php echo $field['label']; ?> <?php echo $required_field; ?></label>
					<?php } ?>
					<div class="wpfepp-form-field-errors"><?php echo $field_errors; ?></div>
					<?php if ( isset( $field['prefix_text'] ) && $field['prefix_text'] ) { ?>
						<div class="wpfepp-prefix-text"><?php echo $field['prefix_text']; ?></div>
					<?php } ?>
					<?php if ( $field['type'] == 'title' ) { ?>
						<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" type="text" value="<?php echo esc_attr( $field_current ); ?>" <?php echo $print_restrictions; ?> />
					<?php } ?>
					<?php if( $field['type'] == 'content' ) { ?>
						<?php if( $field['element'] == 'richtext' ) { ?>
							<?php
							$media_buttons = (isset($field['media_button'])) ? (boolean)$field['media_button'] : true;
							wp_editor($field_current, "wpfepp-$unique_key-field", array( 
								'wpautop'=>true, 
								'media_buttons'=> $media_buttons, 
								'textarea_name'=>$field_key, 
								'textarea_rows'=>10, 
								'editor_class'=>"wpfepp-$field_key-field wpfepp-form-field"
							)); ?>
						<?php } else { ?>
							<textarea id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-plain-field wpfepp-form-field" name="<?php echo $field_key; ?>"><?php echo esc_textarea( $field_current ); ?></textarea>
						<?php } ?>
						<?php if(!wpfepp_current_user_has($this->settings['no_restrictions'])) { ?>
							<script>
								function wpfepp_set_content_restrictions($){
								<?php if ( $field['required'] ) : ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('required', 'true');<?php endif; ?>
								<?php if ( $field["min_words"] && is_numeric( $field["min_words"] ) ) : ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('minwords', '<?php echo $field["min_words"]; ?>');<?php endif; ?>
								<?php if ( $field["max_words"] && is_numeric( $field["max_words"] ) ) : ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('maxwords', '<?php echo $field["max_words"]; ?>');<?php endif; ?>
								<?php if ( $field["max_links"] && is_numeric( $field["max_links"] ) ) : ?>$('textarea#wpfepp-<?php echo $unique_key; ?>-field').attr('maxlinks', '<?php echo $field["max_links"]; ?>');<?php endif; ?> }
							</script>
						<?php } ?>
					<?php } ?>
					<?php if($field['type'] == 'excerpt') { ?>
						<textarea id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $print_restrictions; ?> ><?php echo esc_textarea($field_current); ?></textarea>
					<?php } ?>
					<?php if($field['type'] == 'thumbnail') { ?>
						<?php if(current_user_can('upload_files')) : ?>
						<div class="wpfepp-<?php echo $field_key; ?>-field">
							<div class="wpfepp-<?php echo $field_key; ?>-container"><?php $this->output_thumbnail( $field_current ); ?></div>
							<a class="wpfepp-<?php echo $field_key; ?>-link" href="#"><?php _e( 'Select Featured Image', 'wpfepp-plugin' ); ?></a>
							<a class="wpfepp-<?php echo $field_key; ?>-close" href="#"><span class="dashicons dashicons-no"></span></a>
							<input type="hidden" value="<?php echo ( $field_current ) ? esc_attr( $field_current ) : "-1"; ?>" name="<?php echo $field_key; ?>" class="wpfepp-<?php echo $field_key; ?>-id wpfepp-form-field" <?php echo $print_restrictions; ?> />
						</div>
						<?php else: ?>
							<div class="wpfepp-prefix-text"><?php _e( 'Unfortunately, you do not have enough rights to upload images.', 'wpfepp-plugin' ); ?></div>
						<?php endif; ?>
					<?php } ?>
					<?php if($field['type'] == 'sku') { ?>
						<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" type="text" value="<?php echo esc_attr($field_current); ?>" <?php echo $print_restrictions; ?> />
					<?php } ?>
					<?php if($field['type'] == 'price') { ?>
						<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" type="text" value="<?php echo esc_attr($field_current); ?>" <?php echo $print_restrictions; ?> />
					<?php } ?>
					<?php if($field['type'] == 'sale_price') { ?>
						<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" type="text" value="<?php echo esc_attr($field_current); ?>" <?php echo $print_restrictions; ?> />
					<?php } ?>
					<?php if($field['type'] == 'product_options') { ?>
						<input type="hidden" name="<?php echo $field_key; ?>" value="1" />
						<?php if($field['virtual_prod']) : ?>
						<input type="hidden" name="<?php echo $field_key; ?>_virtual" value="1" />
						<?php endif; ?>
						<?php if($field['prod_gallery']) : ?>
						<?php wp_enqueue_script( 'wpfepp-meta-boxes-product' ); ?>
						<div class="wpfepp-product_gallery-container wpfepp-form-field-container">
							<label for="product_image_gallery"><?php _e( 'Product Gallery', 'wpfepp-plugin' ); ?> <?php echo $required_field; ?></label>
							<?php if($final_post_id == '-1') { ?>
								<?php if(current_user_can('upload_files')) : ?>
									<div id="product_images_container">
										<ul class="product_images"></ul>
										<input type="hidden" class="wpfepp-form-field" id="product_image_gallery" name="product_image_gallery" value="">
									</div>
									<p class="add_product_images hide-if-no-js">
										<a href="#" class="wpfepp-button" data-choose="<?php esc_attr_e( 'Add images to Product gallery', 'wpfepp-plugin' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'wpfepp-plugin' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'wpfepp-plugin' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'wpfepp-plugin' ); ?>"><?php _e( 'Add images', 'wpfepp-plugin' ); ?></a>
									</p>
								<?php else: ?>
									<div class="wpfepp-prefix-text"><?php _e( 'Unfortunately, you do not have enough rights to upload images.', 'wpfepp-plugin' ); ?></div>
								<?php endif; ?>
							<?php } else { ?>
								<?php $post_obj = get_post( $final_post_id ); WC_Meta_Box_Product_Images::output( $post_obj ); ?>
							<?php } ?>
						</div>
						<?php endif; ?>
						<?php if($field['extern_prod']) : ?>
						<div class="wpfepp-product_external-container wpfepp-form-field-container">
							<label for="<?php echo $field_key; ?>_product_url"><?php _e( 'Product URL', 'wpfepp-plugin' ); ?> <?php echo $required_field; ?></label>
							<input type="hidden" name="<?php echo $field_key; ?>_external" value="1" />
							<div class="wpfepp-prefix-text"><?php _e( 'Enter the external URL to the product.', 'wpfepp-plugin' ); ?></div>
							<?php $curr_product_url = isset( $current[$field_key .'_product_url'] ) ? $current[$field_key .'_product_url'] : ''; ?>
							<input type="url" class="wpfepp-<?php echo $field_key; ?>-exturl-field wpfepp-form-field" name="<?php echo $field_key; ?>_product_url" value="<?php echo esc_attr($curr_product_url); ?>" placeholder="<?php esc_attr_e( "http://", 'wpfepp-plugin' ); ?>" <?php echo $print_restrictions; ?> />
						</div>
						<?php endif; ?>
						<?php if($field['down_product']) : ?>
						<div class="wpfepp-downloadable_files-container wpfepp-form-field-container">
							<label for="widefat"><?php _e( "Downloadable Files", "wpfepp-plugin" ); ?> <?php echo $required_field; ?></label>
							<input type="hidden" name="<?php echo $field_key; ?>_downloadable" value="1" />
							<table class="widefat">
								<thead><tr></tr></thead>
								<tbody>
									<?php
									$curr_downloadable_files = isset($current[$field_key .'_downloadable_files']) ? $current[$field_key .'_downloadable_files'] : '';
									if( $curr_downloadable_files ) { foreach($curr_downloadable_files as $file_key => $file_value) { include( 'html-product-download.php' ); }}
									?>		
								</tbody>
								<tfoot><tr><th colspan="4">
									<a href="#" class="wpfepp-button insert" data-row="<?php
										$file_value = array( 'file' => '', 'name' => '' );
										ob_start();
										include( 'html-product-download.php' );
										echo esc_attr(ob_get_clean());
									?>"><?php _e('Add File', 'wpfepp-plugin'); ?></a>
								</th></tr></tfoot>
							</table>
						</div>
						<label for="<?php echo $field_key; ?>_download_limit"><?php _e('Download Limit', 'wpfepp-plugin'); ?></label>
						<div class="wpfepp-prefix-text"><?php _e('Leave blank for unlimited re-downloads.', 'wpfepp-plugin'); ?></div>
						<?php $curr_download_limit = isset($current[$field_key .'_download_limit']) ? $current[$field_key .'_download_limit'] : ''; ?>
						<input type="number" class="wpfepp-<?php echo $field_key; ?>-limit-field" name="<?php echo $field_key; ?>_download_limit" id="wpfepp-<?php echo $field_key; ?>-limit-field" value="<?php echo esc_attr($curr_download_limit); ?>" placeholder="<?php _e('Unlimited', 'wpfepp-plugin'); ?>" /> 
						<label for="<?php echo $field_key; ?>_download_expiry"><?php _e('Download Expiry', 'wpfepp-plugin'); ?></label>
						<div class="wpfepp-prefix-text"><?php _e('Enter the number of days before a download link expires, or leave blank.', 'wpfepp-plugin'); ?></div>
						<?php $curr_download_expiry = isset($current[$field_key .'_download_expiry']) ? $current[$field_key .'_download_expiry'] : ''; ?>
						<input type="number" class="wpfepp-<?php echo $field_key; ?>-expiry-field" name="<?php echo $field_key; ?>_download_expiry" id="wpfepp-<?php echo $field_key; ?>-expiry-field" value="<?php echo esc_attr($curr_download_expiry); ?>" placeholder="<?php _e('Never', 'wpfepp-plugin'); ?>" /> 
						<?php if( $field['down_type'] ) { ?>
						<label for="<?php echo $field_key; ?>_download_type"><?php _e('Download Type', 'wpfepp-plugin'); ?></label>
						<div class="wpfepp-prefix-text"><?php _e('Choose a download type - this controls the <a href="http://schema.org/">schema</a>.', 'wpfepp-plugin'); ?></div>
						<?php $curr_download_type = isset( $current[$field_key .'_download_type'] ) ? $current[$field_key .'_download_type'] : ''; ?>
						<select id="wpfepp-<?php echo $unique_key; ?>-type-field" class="wpfepp-<?php echo $field_key; ?>-type-field" name="<?php echo $field_key; ?>_download_type">
							<option value="" <?php selected($curr_download_type); ?>><?php _e('Standard Product', 'wpfepp-plugin'); ?></option>
							<option value="application" <?php selected($curr_download_type, 'application'); ?>><?php _e('Application/Software', 'wpfepp-plugin'); ?></option>
							<option value="music" <?php selected($curr_download_type, 'music'); ?>><?php _e('Music', 'wpfepp-plugin'); ?></option>
						</select>
						<?php } ?>
						<?php endif; // end $field['down_product'] ?>
					<?php } ?>
					<?php if( $field['type'] == 'hierarchical_taxonomy' ): ?>
						<?php
							wp_enqueue_style('wpfepp-select2-css');
							wp_enqueue_script('wpfepp-select2');
							$exclude_terms = ( isset( $field['exclude'] ) && !empty( $field['exclude'] ) ) ? $field['exclude'] : '';
							$include_terms = ( isset($field['include'] ) && !empty( $field['include'] ) ) ? $field['include'] : '';
							$hide_empty = isset( $field['hide_empty'] ) ? $field['hide_empty'] : 0;
							$args = array( 'taxonomy' => $field_key, 'hide_empty' => $hide_empty, 'exclude' => $exclude_terms, 'include' => $include_terms, 'parent' => 0 );
						?>
						<select id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-hierarchical-taxonomy-field wpfepp-form-field" name="<?php echo $field_key; ?>[]" <?php echo $this->print_restrictions( $field ); ?>>
							<?php if( !$field['multiple'] ): ?><option value=""><?php _e( 'Select', 'wpfepp-plugin' ); ?> ...</option><?php endif; ?>
							<?php $this->hierarchical_taxonomy_options( $args, $field_current ); ?>
						</select>
					<?php endif; ?>
					<?php if( $field['type'] == 'non_hierarchical_taxonomy' ): ?>
						<input id="wpfepp-<?php echo $unique_key; ?>-field" type="text" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-non-hierarchical-taxonomy-field wpfepp-form-field" name="<?php echo $field_key; ?>" value="<?php echo esc_attr( $field_current ); ?>" <?php echo $this->print_restrictions( $field ); ?> />
					<?php endif; ?>
					<?php if( $field['type'] == 'post_formats' ) { ?>
						<?php $formats = get_theme_support( 'post-formats' ); ?>
						<select id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>">
							<option value="standard"><?php _e('Standard', 'wpfepp-plugin'); ?></option>
							<?php foreach ($formats[0] as $key => $format) { ?>
								<option value="<?php echo $format; ?>" <?php selected($field_current, $format); ?>><?php echo ucfirst($format); ?></option>
							<?php } ?>
						</select>
					<?php } ?>
					<?php if( $field['type'] == 'custom_field' ) : ?>
						<?php if( $field['element'] == 'input' || $field['element'] == 'email' || $field['element'] == 'url' ) : ?>
							<?php $change_input_type = ( $field['element'] == 'input' ) ? 'text' : $field['element']; ?>
							<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" type="<?php echo $change_input_type; ?>" name="<?php echo $field_key; ?>" value="<?php echo esc_attr($field_current); ?>" <?php echo $print_restrictions; ?> />
							<?php if( $parser_on && $field_key == 'rehub_offer_product_url' ) : ?>
								<input type="hidden" value="" name="<?php echo 'parser_'. $field_key; ?>" class="wpfepp-parser-<?php echo $field_key; ?> wpfepp-form-field" data-img-size="<?php echo $parser_size; ?>" />
								<div class="wpfepp-form-image-items"></div>
							<?php endif; ?>
						<?php elseif($field['element'] == 'textarea'): ?>
							<textarea id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $print_restrictions; ?>><?php echo esc_textarea($field_current); ?></textarea>
						<?php elseif($field['element'] == 'inputdate') : ?>
							<?php $field_current = ($field['unixtime'] == 1 && !empty($field_current)) ? date_i18n( 'Y-m-d', $field_current ) : $field_current; ?>
							<input id="wpfepp-<?php echo $unique_key; ?>-field" type="text" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field wpfepp-form-field-date" name="<?php echo $field_key; ?>" <?php echo $print_restrictions; ?> value="<?php echo esc_attr($field_current); ?>" /> 
						<?php elseif( $field['element'] == 'inputtime' ) : ?>
							<input id="wpfepp-<?php echo $unique_key; ?>-field" type="time" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" value="<?php echo esc_attr( $field_current ); ?>" /> 
						<?php elseif($field['element'] == 'inputnumb') : ?>
							<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" type="number" name="<?php echo $field_key; ?>" value="<?php echo esc_attr($field_current); ?>" <?php echo $print_restrictions; ?> />	
						<?php elseif($field['element'] == 'map'): ?>
							<input id="wpfepp_map_start_location" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" type="text" name="<?php echo $field_key; ?>" value="<?php echo esc_attr($field_current); ?>" <?php echo $print_restrictions; ?> placeholder="<?php echo $this->extended['adress_placeholder']; ?>" />
							<input type="hidden" name="rh_map_hidden_adress" id="rh_map_hidden_adress" value="" />
							<?php $this->enqueue_frontend_location_scripts($final_post_id); ?>
						<?php elseif($field['element'] == 'checkbox'): ?>
							<input type="hidden" name="<?php echo $field_key; ?>" value="0" />
							<input type="checkbox" id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $print_restrictions; ?> value="1" <?php checked($field_current); ?> />
						<?php elseif( $field['element'] == 'select' ) : ?>
							<?php $field['choices'] = wpfepp_choices( $field['choices'] ); ?>
							<?php $multiple = ($field['multiple']) ? ' multiple' : ''; ?>
							<select id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?><?php if($multiple) : ?>[]<?php endif; ?>"<?php echo $multiple; ?> <?php echo $this->print_restrictions( $field ); ?>>
								<?php $fields_current = is_array($field_current) ? $field_current : ''; ?>
								<?php if(!$multiple) : ?><option value=""><?php _e( 'Select', 'wpfepp-plugin' ); ?> ...</option><?php endif; ?>
								<?php foreach ( $field['choices'] as $choice ): ?>
									<?php if($fields_current && in_array($choice['key'], $fields_current)){ $field_current = $choice['key']; } ?>
									<option value="<?php echo esc_attr( $choice['key'] ); ?>" <?php selected(esc_attr($choice['key']), $field_current, true); ?>><?php echo $choice['val']; ?></option>
								<?php endforeach; ?>
							</select>
						<?php elseif($field['element'] == 'radio'): ?>
							<?php $field['choices'] = wpfepp_choices($field['choices']); ?>
							<?php foreach ($field['choices'] as $choice): ?>
								<input type="radio" value="<?php echo esc_attr($choice['key']); ?>" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" name="<?php echo $field_key; ?>" <?php echo $print_restrictions; ?> <?php checked($field_current); ?> /> <?php echo $choice['val']; ?><br/>
							<?php endforeach; ?>
						<?php elseif( $field['element'] == 'image_url' || $field['element'] == 'image_galery' ) : ?>
							<?php if( current_user_can( 'upload_files' ) ) : ?>
								<?php wp_enqueue_media(); wp_enqueue_script('wpfepp-media'); ?>
								<?php if( $field['element'] == 'image_url' ) { ?>
								<?php $attachdata = ( $field['attachdata'] == 'attid' ) ?  $field['attachdata'] : 'atturl'; ?>
								<?php $attattribute = ( $field['attachdata'] == 'attid' ) ?  'id': 'url'; ?>
								<div class="wpfepp-media wpfepp-media-single" data-title="<?php _e( "Select Item", "wpfepp-plugin" ) ?>" data-button-text="<?php _e( "Select", "wpfepp-plugin" ) ?>" data-multiple="false" data-attribute="<?php echo esc_attr( $attattribute ); ?>">
									<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" type="hidden" name="<?php echo $field_key; ?>" value="<?php echo esc_attr( $field_current ); ?>" />
									<div class="wpfepp-media-preview"><?php echo wpfepp_media_preview_html( $field_current, $attachdata ); ?></div>
									<div class="element-media-controls">
										<a href="#" class="wpfepp-media-select"><?php _e( "Select / Upload File", "wpfepp-plugin" ); ?></a>
										<a href="#" class="wpfepp-media-clear"><span class="dashicons dashicons-no"></span></a>
									</div>
								</div>
								<?php } elseif( $field['element'] == 'image_galery' ) { ?>
								<div class="wpfepp-media wpfepp-media-multiple" data-title="<?php _e( "Select Items", "wpfepp-plugin" ) ?>" data-button-text="<?php _e( "Select", "wpfepp-plugin" ) ?>" data-multiple="true" data-attribute="id">
									<input id="wpfepp-<?php echo $unique_key; ?>-field" class="wpfepp-<?php echo $field_key; ?>-field wpfepp-form-field" type="hidden" name="<?php echo $field_key; ?>" value="<?php echo esc_attr( $field_current ); ?>" />
									<div class="wpfepp-media-preview"><?php echo wpfepp_media_preview_html( $field_current, 'attids' ); ?></div>
									<div class="element-media-controls">
										<a href="#" class="wpfepp-media-select"><?php _e( "Select Gallery Images", "wpfepp-plugin" ); ?></a>
										<a href="#" class="wpfepp-media-clear"><span class="dashicons dashicons-no"></span></a>
									</div>
								</div>
								<?php } ?>
							<?php else: ?>
								<div class="wpfepp-prefix-text"><?php _e( 'Unfortunately, you do not have enough rights to upload images.', 'wpfepp-plugin' ); ?></div>							
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<?php else: ?>
					<?php if ( isset( $field['fallback_value'] ) && !empty($field['fallback_value']) ) : ?>
						<?php if ( $field['type'] == 'custom_field' && $field['element'] == 'input' ) : ?>
						<input type="hidden" name="<?php echo $field_key; ?>" value="<?php echo $field['fallback_value']; ?>" />
						<?php else : ?>
						<textarea style="display:none;" name="<?php echo $field_key; ?>"><?php echo $field['fallback_value']; ?></textarea>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php
		/* 
		* Do actions 'wpfepp_form_[ID]_fields' and 'wpfepp_form_fields' 
		*/
		$this->user_defined_fields( $current );
		?>
		<?php // service fields ?>
		<?php if( $this->settings['captcha_enabled'] && $this->post_status( $final_post_id ) == 'new' ) : $this->captcha->render(); endif; ?>
		<?php if( $this->paid_on ) : ?><input class="wpfepp-paid-id-field" type="hidden" name="wpfepp_paid_post" value="<?php echo $this->id; ?>" /><?php endif; ?>
		<?php if( !empty($this->extended['limit_number']) && $this->extended['limit_number'] > 0 ) : ?><input class="wpfepp-limit-number-field" type="hidden" name="form_limit_number" value="<?php echo $this->extended['limit_number']; ?>" /><?php endif;?>
		<input class="wpfepp-form-id-field" type="hidden" name="form_id" value="<?php echo $this->id; ?>" />
		<input class="wpfepp-post-id-field" type="hidden" name="post_id" value="<?php echo $final_post_id; ?>" />
		<?php wp_nonce_field( 'wpfepp-form-'.$this->id.'-nonce', '_wpnonce', false, true ); ?>
		<input type="hidden" name="action" value="wpfepp_handle_submission_ajax" />
		<?php // send form ?>
		<button type="submit" class="wpfepp-button wpfepp-submit-button <?php echo (isset($this->settings['button_color'])) ? $this->settings['button_color'] : 'blue'; ?>" name="wpfepp-form-<?php echo $this->id; ?>-submit"><?php _e( "Submit", "wpfepp-plugin" ); ?></button>
		<?php if( $this->settings['enable_drafts'] && ($this->post_status($final_post_id) == 'new' || $this->post_status($final_post_id) == 'draft') ): ?>
		<button type="submit" class="wpfepp-button wpfepp-save-button cancel" name="wpfepp-form-<?php echo $this->id; ?>-save"><?php _e('Save Draft','wpfepp-plugin'); ?></button>
		<?php endif; ?>
		<span class="dashicons dashicons-update"></span>
	</div>
</form>