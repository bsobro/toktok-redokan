<div class="wrap">
	<h2><?php _e( 'Frontend Publishing Forms', 'wpfepp-plugin' ); ?> 
		<img id="wpfepp-loading" src="<?php echo plugins_url( 'static/img/loading.gif', dirname( dirname( __FILE__ ) ) ); ?>" />
	</h2>
	<?php $paged = ( isset( $_GET['paged'] ) ? $_GET['paged'] : '1' ); ?>
	<div class="wpfepp-op">
		<?php if( $paged == 1 ) : ?>
			<div id="wpfepp-form-creator" class="form-wrap">
				<h3><?php _e('Create a New Form', 'wpfepp-plugin'); ?></h3>
				<form class="wpfepp-ajax-form">
					<div class="form-field form-required">
						<label><?php _e( 'Form Name', 'wpfepp-plugin' ); ?>:</label>
						<input type="text" class="wpfepp-required" name="form_name" />
						<p><?php _e( 'The name of this form', 'wpfepp-plugin' ); ?></p>
					</div>
					<div class="form-field">
						<label><?php _e( 'Description', 'wpfepp-plugin' ); ?>:</label>
						<textarea name="form_description"></textarea>
						<p><?php _e ('A tiny description explaining what this form does', 'wpfepp-plugin' ); ?></p>
					</div>
					<div class="form-field form-required">
						<label><?php _e( 'Post Type', 'wpfepp-plugin' ); ?>:</label>
						<?php if( is_array( $this->post_types ) && count( $this->post_types ) ) : ?>
							<select name="post_type">
								<?php foreach( $this->post_types as $type ) : ?>
									<?php if( $type != 'attachment' ) : ?>
										<option value="<?php echo $type; ?>"><?php echo $type; ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						<?php else : ?>
							<input type="text" name="post_type" value="post" disabled />
						<?php endif; ?>
						<p><?php _e( 'The post type of this form', 'wpfepp-plugin' ); ?></p>
					</div>
					<div class="form-field">
						<label><?php _e( 'Import elements from', 'wpfepp-plugin' ); ?>:</label>
						<?php $this->display_form_selector(); ?>
						<p>
							<?php _e( 'If you want to duplicate the fields, settings and emails of an existing form, specify it here. Otherwise defaults values will be used for everything.', 'wpfepp-plugin' ); ?>
						</p>
					</div>
					<?php wp_nonce_field( 'wpfepp-create-form', '_wpnonce', false, true ); ?>
					<input type="hidden" name="action" value="wpfepp_create_form_ajax" />
					<input type="hidden" name="page" value="<?php echo $this->page; ?>" />
					<input type="hidden" name="hook_suffix" value="<?php echo $this->page; ?>" />
					<?php submit_button( __('Create Form', 'wpfepp-plugin'), 'primary', 'wpfepp-create-form', false ); ?>
				</form>
			</div>
		<?php endif; ?>
		<div id="<?php echo ( ( $paged == 1 ) ? 'wpfepp-form-list-table-container' : '' ); ?>">
			<?php $this->render_form_list_table(); ?>
		</div>
	</div>
</div>