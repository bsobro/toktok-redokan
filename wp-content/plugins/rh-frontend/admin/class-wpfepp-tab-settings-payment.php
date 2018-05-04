<?php

if( !class_exists('WPFEPP_Tab') ){
    require_once 'class-wpfepp-tab.php';
}

class WPFEPP_Tab_Settings_Payment extends WPFEPP_Tab
{
	/**
	 * Class constructor. Calls the parent constructor.
	 *
	 * @var string $version Plugin version.
	 * @var string $slug Tab slug.
	 * @var string $name Tab name.
	 **/
	function __construct($version, $slug, $name) {
		parent::__construct($version, $slug, $name);
	}

	/**
	 * Registers the actions of this class with WordPress. This function is called by add_actions of WPFEPP_Tab_Collection, which in turn is called by add_actions of WPFEPP_Plugin_Settings.
	 **/
	public function add_actions(){
		add_action( 'admin_init', array($this, 'create_options') );
	}

	/**
	 * Creates the settings section, resgisters setting and adds settings fields. Uses WordPress' settings API.
	 *
	 * @see add_settings_section()
	 * @see register_setting()
	 * @see add_settings_field()
	 **/
	public function create_options(){
		$section 		= 'wpfepp_payment_settings_section';
		$tab 			= 'wpfepp_payment_settings_tab';
		$option_id 		= 'wpfepp_payment_settings';
		$curr_settings 	= get_option($option_id);
		$callback 		= array( $this->renderer, 'render' );
		$args 			= array( 'group' => $option_id, 'curr' => $curr_settings);

		add_settings_section( $section, '', array($this, 'section_callback'), $tab );

		register_setting( $tab, $option_id, array($this->validator, 'validate') );

	    add_settings_field(
	        'turn_on_payment', __('Support Payment Submit in Woocommerce', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('When checked you can add to WooCommerce a paid posting package.', 'wpfepp-plugin'),
					'id' 	=> 'turn_on_payment',
					'type' 	=> 'checkbox'
				),
				$args
			)
	    );
	}

	/**
	 * Outputs the contents of the tab with the help of WordPress' settings API.
	 **/
	public function display(){
	    ?>
	    <form method="post" action="options.php">
		    <?php
				settings_fields( 'wpfepp_payment_settings_tab' );
				do_settings_sections( 'wpfepp_payment_settings_tab' );
				submit_button( __('Save Settings', 'wpfepp-plugin'), 'primary', null, true );
			?>
		</form>
		<?php
	}

	public function section_callback($args){
		?>
			<p class="description"><?php _e('To use paid sumbission for posting, you must have installed plugin WooCommerce. <br>Then enable function below. You will get new product post type inside Woocommerce - <em>Paid submit package</em>.<br>Create product with this product type and configure package details in <em>Package Option<em>.<br>Also, I recommend to disable Catalog visibility of this product in Publish section and make date of publishing in past. <br>Don\'t forget to add a correct Form ID where you want to have paid submission. <br>Then, you can use shortcode <code>[wpfepp_submission_form form="1" paid="1"]</code> to add payment option to form.', 'wpfepp-plugin'); ?></p>
		<?php	
	}
}