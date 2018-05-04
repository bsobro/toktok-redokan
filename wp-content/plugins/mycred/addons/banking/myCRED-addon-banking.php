<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Addon: Banking
 * Addon URI: http://mycred.me/add-ons/banking/
 * Version: 2.0
 */
define( 'myCRED_BANK',              __FILE__ );
define( 'myCRED_BANK_DIR',          myCRED_ADDONS_DIR . 'banking/' );
define( 'myCRED_BANK_ABSTRACT_DIR', myCRED_BANK_DIR . 'abstracts/' );
define( 'myCRED_BANK_INCLUDES_DIR', myCRED_BANK_DIR . 'includes/' );
define( 'myCRED_BANK_SERVICES_DIR', myCRED_BANK_DIR . 'services/' );

require_once myCRED_BANK_ABSTRACT_DIR . 'mycred-abstract-service.php';

require_once myCRED_BANK_INCLUDES_DIR . 'mycred-banking-functions.php';

require_once myCRED_BANK_SERVICES_DIR . 'mycred-service-central.php';
require_once myCRED_BANK_SERVICES_DIR . 'mycred-service-interest.php';
require_once myCRED_BANK_SERVICES_DIR . 'mycred-service-payouts.php';

/**
 * myCRED_Banking_Module class
 * @since 0.1
 * @version 2.0
 */
if ( ! class_exists( 'myCRED_Banking_Module' ) ) :
	class myCRED_Banking_Module extends myCRED_Module {

		/**
		 * Constructor
		 */
		public function __construct( $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( 'myCRED_Banking_Module', array(
				'module_name' => 'banking',
				'option_id'   => 'mycred_pref_bank',
				'defaults'    => array(
					'active'        => array(),
					'services'      => array(),
					'service_prefs' => array()
				),
				'labels'      => array(
					'menu'        => __( 'Banking', 'mycred' ),
					'page_title'  => __( 'Banking', 'mycred' ),
					'page_header' => __( 'Banking', 'mycred' )
				),
				'screen_id'   => MYCRED_SLUG . '-banking',
				'accordion'   => true,
				'menu_pos'    => 60
			), $type );

		}

		/**
		 * Load Services
		 * @since 1.2
		 * @version 1.0
		 */
		public function module_init() {

			if ( ! empty( $this->services ) ) {

				foreach ( $this->services as $key => $gdata ) {

					if ( $this->is_active( $key ) && isset( $gdata['callback'] ) ) {
						$this->call( 'run', $gdata['callback'] );
					}

				}

			}

			add_action( 'wp_ajax_run-mycred-bank-service', array( $this, 'ajax_handler' ) );

		}

		/**
		 * Module Admin Init
		 * @since 1.7
		 * @version 1.0
		 */
		public function module_admin_init() {

			// User Override
			add_action( 'mycred_user_edit_after_' . $this->mycred_type, array( $this, 'banking_user_screen' ), 20 );

		}

		/**
		 * Call
		 * Either runs a given class method or function.
		 * @since 1.2
		 * @version 1.2
		 */
		public function call( $call, $callback, $return = NULL ) {

			// Class
			if ( is_array( $callback ) && class_exists( $callback[0] ) ) {

				$class = $callback[0];
				$methods = get_class_methods( $class );
				if ( in_array( $call, $methods ) ) {

					$new = new $class( ( isset( $this->service_prefs ) ) ? $this->service_prefs : array(), $this->mycred_type );
					return $new->$call( $return );

				}

			}

			// Function
			elseif ( ! is_array( $callback ) ) {

				if ( function_exists( $callback ) ) {

					if ( $return !== NULL )
						return call_user_func( $callback, $return, $this );
					else
						return call_user_func( $callback, $this );

				}

			}

			if ( $return !== NULL )
				return array();

		}

		/**
		 * Get Bank Services
		 * @since 1.2
		 * @version 1.0
		 */
		public function get( $save = false ) {

			// Savings
			$services['central'] = array(
				'title'        => __( 'Central Banking', 'mycred' ),
				'description'  => __( 'Instead of creating %_plural% out of thin-air, all payouts are made from a nominated "Central Bank" account. Any %_plural% a user spends or loses are deposited back into this account. If the central bank runs out of %_plural%, no %_plural% will be paid out.', 'mycred' ),
				'cron'         => false,
				'icon'         => 'dashicons-admin-site',
				'callback'     => array( 'myCRED_Banking_Service_Central' )
			);

			// Interest
			$services['interest'] = array(
				'title'        => __( 'Compound Interest', 'mycred' ),
				'description'  => __( 'Offer your users interest on the %_plural% they earn on your website. The interest is compounded daily.', 'mycred' ),
				'cron'         => true,
				'icon'         => 'dashicons-vault',
				'callback'     => array( 'myCRED_Banking_Service_Interest' )
			);

			// Inflation
			$services['payouts'] = array(
				'title'       => __( 'Recurring Payouts', 'mycred' ),
				'description' => __( 'Setup mass %_singular% payouts for your users.', 'mycred' ),
				'cron'        => true,
				'icon'         => 'dashicons-update',
				'callback'    => array( 'myCRED_Banking_Service_Payouts' )
			);

			$services = apply_filters( 'mycred_setup_banking', $services );

			if ( $save === true && $this->core->can_edit_plugin() ) {
				$new_data = array(
					'active'        => $this->active,
					'services'      => $services,
					'service_prefs' => $this->service_prefs
				);
				mycred_update_option( $this->option_id, $new_data );
			}

			$this->services = $services;
			return $services;

		}

		/**
		 * Page Header
		 * @since 1.3
		 * @version 1.0
		 */
		public function settings_header() {

			$banking_icons = plugins_url( 'assets/images/gateway-icons.png', myCRED_THIS );

			wp_enqueue_style( 'mycred-bootstrap-grid' );
			wp_enqueue_style( 'mycred-forms' );

			wp_register_script( 'mycred-bank-manage-schedules', plugins_url( 'assets/js/manage-schedules.js', myCRED_BANK ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-slide', 'jquery-numerator' ), myCRED_VERSION );

			wp_localize_script(
				'mycred-bank-manage-schedules',
				'Banking',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'token'          => wp_create_nonce( 'run-mycred-bank-task' . $this->mycred_type ),
					'new'            => esc_attr__( 'New Recurring Payout', 'mycred' ),
					'edit'           => esc_attr__( 'Edit Recurring Payout', 'mycred' ),
					'close'          => esc_attr__( 'Close', 'mycred' ),
					'emptyfields'    => esc_attr__( 'Please fill out all required fields that are highlighted in red.', 'mycred' ),
					'confirmremoval' => esc_attr__( 'Are you sure you want to remove this schedule? This can not be undone!', 'mycred' )
				)
			);
			wp_enqueue_script( 'mycred-bank-manage-schedules' );

?>
<style type="text/css">
.mycred-update-balance { font-family: "Open Sans",sans-serif; background-color: white !important; z-index: 9999 !important; border: none !important; border-radius: 0 !important; background-image: none !important; padding: 0 !important; overflow: visible !important; }
.mycred-update-balance.ui-dialog .ui-dialog-content { padding: 0 0 0 0; }
.mycred-update-balance .ui-widget-header { border: none !important; background: transparent !important; font-weight: normal !important; }
.mycred-update-balance .ui-dialog-titlebar { line-height: 24px !important; border-bottom: 1px solid #ddd !important; border-left: none; border-top: none; border-right: none; padding: 12px !important; border-radius: 0 !important; }
.mycred-update-balance .ui-dialog-titlebar:hover { cursor: move; }
.mycred-update-balance .ui-dialog-titlebar-close { float: right; margin: 0 12px 0 0; background: 0 0; border: none; -webkit-box-shadow: none; box-shadow: none; color: #666; cursor: pointer; display: block; padding: 0; position: absolute; top: 0; right: 0; width: 36px; height: 36px; text-align: center; font-size: 13px; line-height: 26px; vertical-align: top; white-space: nowrap; }
.mycred-update-balance .ui-icon { display: none !important; }
.mycred-update-balance .ui-button:focus, .mycred-update-balance .ui-button:active { outline: none !important; }
.mycred-update-balance .ui-button .ui-button-text { display: block; text-indent: 0; }
.mycred-update-balance .ui-dialog-title { font-size: 22px; font-weight: 600; margin: 0 0 0 0; width: auto !important; float: none !important; }
.ui-widget-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: repeat-x scroll 70% 70% #000; opacity: 0.7; overflow: hidden; background: repeat-x scroll 70% 70% #000; z-index: 99; }
#manage-recurring-schedule { min-height: 4px !important; background-color: #f3f3f3; }
#manage-recurring-schedule-form h3 { margin: 0 0 12px 0; }
.mycred-metabox .form .has-error .form-control { border-color: #dc3232; }
.alert { padding: 24px; }
.alert-warning { background-color: #dc3232; color: white; }
.alert-success { background-color: #46b450; color: white; }
</style>
<?php

		}

		/**
		 * Admin Page
		 * @since 0.1
		 * @version 1.1
		 */
		public function admin_page() {

			// Security
			if ( ! $this->core->can_edit_creds() ) wp_die( 'Access Denied' );

			// Get installed
			$installed = $this->get();

?>
<div class="wrap mycred-metabox" id="myCRED-wrap">

	<?php $this->update_notice(); ?>

	<h1><?php echo sprintf( __( '%s Banking', 'mycred' ), mycred_label() ); ?></h1>
	<form method="post" class="form" action="options.php">

		<?php settings_fields( $this->settings_name ); ?>

		<!-- Loop though Services -->
		<div class="list-items expandable-li" id="accordion">
<?php

			// Installed Services
			if ( ! empty( $installed ) ) {
				foreach ( $installed as $key => $data ) {

?>
			<h4><span class="dashicons <?php echo $data['icon']; ?><?php if ( $this->is_active( $key ) ) echo ' active'; else echo ' static'; ?>"></span><?php echo $this->core->template_tags_general( $data['title'] ); ?></h4>
			<div class="body" style="display: none;">
				<p><?php echo nl2br( $this->core->template_tags_general( $data['description'] ) ); ?></p>
				<?php if ( $data['cron'] && defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) : ?>
				<div class="alert alert-warning"><strong><?php _e( 'Warning', 'mycred' ); ?></strong> - <?php _e( 'This banking service uses the WordPress CRON to schedule events. If the WordPress CRON is disabled, this service will not work correctly.', 'mycred' ); ?></div>
				<?php endif; ?>
				<label class="subheader"><?php _e( 'Enable', 'mycred' ); ?></label>
				<ol>
					<li>
						<input type="checkbox" name="<?php echo $this->option_id; ?>[active][]" id="mycred-bank-service-<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $this->is_active( $key ) ) echo ' checked="checked"'; ?> />
					</li>
				</ol>

				<?php $this->call( 'preferences', $data['callback'] ); ?>

			</div>
<?php

				}
			}

?>

		</div>

		<?php submit_button( __( 'Update Changes', 'mycred' ), 'primary large', 'submit', false ); ?>

	</form>
</div>
<style type="text/css">
body .ui-dialog.ui.widget { height: auto !important; }
</style>
<div id="manage-recurring-schedule" style="display: none;">
	<div class="mycred-container">
		<form class="form" method="post" action="" id="manage-recurring-schedule-form"></form>
		<div id="mycred-processing"><div class="loading-indicator"></div></div>
	</div>
</div>
<?php

		}

		/**
		 * Sanititze Settings
		 * @since 1.2
		 * @version 1.1
		 */
		public function sanitize_settings( $post ) {

			$installed            = $this->get();

			// Construct new settings
			$new_post             = array();
			$new_post['services'] = $installed;

			if ( empty( $post['active'] ) || ! isset( $post['active'] ) )
				$post['active'] = array();

			$new_post['active']   = $post['active'];

			// Loop though all installed hooks
			if ( ! empty( $installed ) ) {
				foreach ( $installed as $key => $data ) {

					if ( isset( $data['callback'] ) && isset( $post['service_prefs'][ $key ] ) ) {

						// Old settings
						$old_settings = $post['service_prefs'][ $key ];

						// New settings
						$new_settings = $this->call( 'sanitise_preferences', $data['callback'], $old_settings );

						// If something went wrong use the old settings
						if ( empty( $new_settings ) || $new_settings === NULL || ! is_array( $new_settings ) )
							$new_post['service_prefs'][ $key ] = $old_settings;
						// Else we got ourselves new settings
						else
							$new_post['service_prefs'][ $key ] = $new_settings;

						// Handle de-activation
						if ( in_array( $key, (array) $this->active ) && ! in_array( $key, $new_post['active'] ) )
							$this->call( 'deactivate', $data['callback'], $new_post['service_prefs'][ $key ] );

						// Handle activation
						if ( ! in_array( $key, (array) $this->active ) && in_array( $key, $new_post['active'] ) )
							$this->call( 'activate', $data['callback'], $new_post['service_prefs'][ $key ] );

						// Next item

					}

				}
			}

			return $new_post;

		}

		/**
		 * User Screen
		 * @since 1.7
		 * @version 1.0
		 */
		public function banking_user_screen( $user ) {

			if ( ! empty( $this->services ) ) {

				foreach ( $this->services as $key => $gdata ) {

					if ( $this->is_active( $key ) && isset( $gdata['callback'] ) ) {
						$this->call( 'user_screen', $gdata['callback'], $user );
					}

				}

			}

		}

		/**
		 * Ajax Handler
		 * @since 1.7
		 * @version 1.0
		 */
		public function ajax_handler() {

			// Make sure this is an ajax call for this point type
			if ( isset( $_REQUEST['_token'] ) && wp_verify_nonce( $_REQUEST['_token'], 'run-mycred-bank-task' . $this->mycred_type ) ) {

				// Make sure ajax call is made by an admin
				if ( $this->core->can_edit_creds() || $this->core->can_edit_plugin() ) {

					// Get the service requesting to use this
					$service   = sanitize_key( $_POST['service'] );
					$installed = $this->get();

					// If there is such a service, load it's ajax handler
					if ( array_key_exists( $service, $installed ) )
						$this->call( 'ajax_handler', $installed[ $service ]['callback'] );

				}

			}

		}

	}
endif;

/**
 * Load Banking Module
 * @since 1.2
 * @version 1.1
 */
if ( ! function_exists( 'mycred_load_banking_addon' ) ) :
	function mycred_load_banking_addon( $modules, $point_types ) {

		foreach ( $point_types as $type => $title ) {
			$modules['type'][ $type ]['banking'] = new myCRED_Banking_Module( $type );
			$modules['type'][ $type ]['banking']->load();
		}

		return $modules;

	}
endif;
add_filter( 'mycred_load_modules', 'mycred_load_banking_addon', 20, 2 );
