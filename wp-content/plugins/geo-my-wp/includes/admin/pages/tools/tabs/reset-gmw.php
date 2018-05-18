<?php
/**
 * admin tools "Reset" tab
 * 
 * @since  2.5
 * @author Eyal Fitoussi
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Export/Import tab output
 *
 * @access public
 * @since 2.5
 * @author Eyal Fitoussi
 */
function gmw_reset_tab() {
?>

<?php do_action( 'gmw_reset_gmw_tab_top' ); ?>

<?php do_action( 'gmw_reset_gmw_tab_before_uninstall' ); ?>

<div id="poststuff" class="metabox-holder">
		
	<div id="post-body">
	
		<div id="post-body-content">

			<div class="postbox">

				<h3 class="hndle">
					<span><?php _e( 'Uninstall GEO my WP Data', 'geo-my-wp' ); ?></span>
				</h3>

				<div class="inside">
					<p>
						<?php _e( "Use this form to remove some or all the data generated by GEO my WP.", "GMW" ); ?>
					</p>
					<p style="color:red;">
						<?php _e( "Note, this action cannot be undone! <br /> Once you remove any of the data you will not be able to restore it unless you have a backup.", "GMW" ); ?>
						<br />
						<?php printf( __( "You should consider <a href=\"%s\">backing up your data before removing it.</a>", "GMW" ), admin_url( 'admin.php?page=gmw-tools&tab=import_export' ) ); ?>
					</p>
					<p>
						<?php _e( "Check the checkboxes of the items that you would like to remove then click on the \"Clear Data\" button.", "GMW" ); ?>
					</p>

					<form method="post" action="<?php echo admin_url( 'admin.php?page=gmw-tools&tab=reset_gmw' ); ?>">
						<p>
							<!-- Settings -->
							<span>
								<input type="checkbox" class="gmw-reset-item" name="gmw_reset_items[]" value="settings" />
								<?php _e( 'Settings', 'geo-my-wp' ); ?>
							</span>
							
							<!-- License keys -->
							<span>
								<input type="checkbox" class="gmw-reset-item" name="gmw_reset_items[]" value="licenses" />
								<?php _e( 'License Keys', 'geo-my-wp' ); ?>
							</span>
							
							<?php 
							global $wpdb;
							
							//look for places_locator table
							$forms_table = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}gmw_forms'", ARRAY_A );

							//if table exists make it avaliable for uninstallation
							if ( count( $forms_table ) != 0 ) {
							?>
								<!-- forms -->
								<span>
									<input 
										type="checkbox" 
										class="gmw-reset-item" 
										name="gmw_reset_items[]" 
										value="forms" 
										onchange="jQuery( '#form-table-remove-warning' ).toggle();"
									/>
									<?php _e( 'Forms', 'geo-my-wp' ); ?>
								</span>

								<em id="form-table-remove-warning" class="reset-warning-cb">
									<?php _e( 'This will permanently remove the GEO my WP forms table and all GEO my WP forms created on this site.' ); ?>
								</em>
							<?php
							}
						
							//look for places_locator table
							$locations_table = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}gmw_locations'", ARRAY_A );

							//if table exists make it avaliable for uninstallation
							if ( count( $locations_table ) != 0 ) {
							?>
								<span>
									<input 
										type="checkbox" 
										class="gmw-reset-item" 
										name="gmw_reset_items[]" 
										value="locations_table" 
										onchange="jQuery( '#locations-table-remove-warning' ).toggle();" 
									/>
									<?php printf( __( 'GMW Locations Database Table ( %sgmw_locations )', 'geo-my-wp' ), $wpdb->prefix ); ?>
								</span>
								
								<em id="locations-table-remove-warning" class="reset-warning-cb">
									<?php _e( 'This will permanently remove GEO my WP locations table and all GEO my WP locations created on this site.', 'geo-my-wp' ); ?>	
								</em>			
							<?php
							}

							//look for places_locator table
							$locaitonmeta_table = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}gmw_locationmeta'", ARRAY_A );
							
							//if table exists make it avaliable for uninstallation
							if ( count( $locaitonmeta_table ) != 0 ) {
							?>
								<span>
									<input 
										type="checkbox" 
										class="gmw-reset-item" 
										name="gmw_reset_items[]" 
										value="locationmeta_table" 
										onchange="jQuery( '#locaitonmeta-table-remove-warning' ).toggle();"
									/>
									<?php printf( __( 'GMW Locations Meta Database Table ( %sgmw_locationmeta )', 'geo-my-wp' ), $wpdb->prefix ); ?>
								</span>

								<em id="locaitonmeta-table-remove-warning" class="reset-warning-cb">
									<?php _e( 'This will permanently remove GEO my WP locaitons meta table and all of its content.', 'geo-my-wp' ); ?>
								</em>							
							<?php } ?>	

							<span>
								<input 
									type="checkbox" 
									class="gmw-reset-item" 
									name="gmw_reset_items[]" 
									value="uninstall"
									onchange="if ( jQuery( this ).is( ':checked' ) ) { jQuery( this ).closest( 'form' ).find( '.gmw-reset-item' ).prop( 'checked', true ); } else { jQuery( this ).closest( 'form' ).find( '.gmw-reset-item' ).prop( 'checked', false ); }"
								/>
								<?php _e( 'Completely Uninstall and deactivate GEO my WP', 'geo-my-wp' ); ?>
							</span>							
						</p>
						<p>
							<input type="hidden" name="gmw_action" value="reset_data" />
							
							<?php wp_nonce_field( 'gmw_reset_data_nonce', 'gmw_reset_data_nonce' ); ?>
							
							<input type="submit" class="button-secondary" value="<?php _e( 'Clear Data','geo-my-wp' ); ?>" id="gmw-clear-data-button" />
						</p>
						<script>
							jQuery(document).ready(function($) {
								$('#gmw-clear-data-button').click(function() {
									if ( !jQuery('.gmw-reset-item').is(':checked') ) { 					
										alert("<?php echo _e( 'You must check at least one item which you would like to remove.', 'geo-my-wp'); ?>"); 
										return false; 
									} else { 
										return confirm( "<?php echo _e( 'You are about to permanently remove some or all of GEO my WP data. This action cannot be undone! Would you like to proceed?', 'geo-my-wp'); ?>"); 
									}
								});				
							});
						</script>
					</form>
					
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->
		</div>
	</div>
</div>

<?php do_action( 'gmw_reset_gmw_tab_after_uninstall' ); ?>
		
<?php do_action( 'gmw_reset_gmw_tab_bottom' ); ?>

<?php
}
add_action( 'gmw_tools_reset_gmw_tab', 'gmw_reset_tab' );

/**
 * Admin notice
 * @param  [type] $messages [description]
 * @return [type]           [description]
 */
function gmw_reset_data_notice_message( $messages ) {
	
	$messages['reset_gmw_data'] = __( 'GEO my WP data cleared.', 'geo-my-wp' );
	
	return $messages;
}
add_filter( 'gmw_admin_notices_messages', 'gmw_reset_data_notice_message' );

/**
 * Clear GMW data
 *
 * @since 2.5
 * @return void
 */
function gmw_reset_data() {

	//make sure at least one item is checked
	if ( empty( $_POST['gmw_reset_items'] ) ) {
		
		wp_die( __( "You must check at least one checkbox of an item that you'd like to clear.", "GMW" ) );
	}

	//look for nonce
	if ( empty( $_POST['gmw_reset_data_nonce'] ) ) {
		
		wp_die( __( 'Cheatin\' eh?!', 'geo-my-wp' ) );
	}

	//varify nonce
	if ( ! wp_verify_nonce( $_POST['gmw_reset_data_nonce'], 'gmw_reset_data_nonce' ) ) {
		
		wp_die( __( 'Cheatin\' eh?!', 'geo-my-wp' ) );
	}

	//clear settings
	if ( in_array( 'settings', $_POST['gmw_reset_items'] ) ) {
		delete_option( 'gmw_options' );
	}

	//clear forms
	if ( in_array( 'forms', $_POST['gmw_reset_items'] ) ) {

		global $wpdb;
	    
	    $table_name = $wpdb->prefix . 'gmw_forms';
	    $sql = "DROP TABLE IF EXISTS {$table_name}";
	    $wpdb->query( $sql );
	}
	
	//clear licensess
	if ( in_array( 'licenses', $_POST['gmw_reset_items'] ) ) {	
		delete_option( 'gmw_license_keys' );
		delete_option( 'gmw_premium_plugin_status' );
	}
	
	//clear posts table
	if ( in_array( 'locations_table', $_POST['gmw_reset_items'] ) ) {
		
		global $wpdb;
	    
	    $table_name = $wpdb->prefix . 'gmw_locations';
	    $sql = "DROP TABLE IF EXISTS {$table_name}";

	    $wpdb->query( $sql );
	}
	
	//clear members table
	if ( in_array( 'locationmeta_table', $_POST['gmw_reset_items'] ) ) {
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'gmw_locationmeta';
	    $sql = "DROP TABLE IF EXISTS {$table_name}";

	    $wpdb->query( $sql );
	}

	$page = 'admin.php?page=gmw-tools&tab=reset_gmw&gmw_notice=reset_gmw_data&gmw_notice_status=updated';

	//deactivate GEO my WP
	if ( in_array( 'uninstall', $_POST['gmw_reset_items'] ) ) {
		
		$page = 'index.php';

		deactivate_plugins( GMW_BASENAME );
	}
	
	wp_safe_redirect( admin_url( $page ) );
	
	exit;
}
add_action( 'gmw_reset_data', 'gmw_reset_data' );