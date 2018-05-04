<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

class WPFEPP_Tab_Settings_Data extends WPFEPP_Tab
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
		$section 		= 'wpfepp_data_settings_section';
		$tab 			= 'wpfepp_data_settings_tab';
		$option_id 		= 'wpfepp_data_settings';
		$curr_settings 	= get_option($option_id);
		$callback 		= array( $this->renderer, 'render' );
		$args 			= array( 'group' => $option_id, 'curr' => $curr_settings);

		add_settings_section( $section, '', array($this, 'section_callback'), $tab );

		register_setting( $tab, $option_id, array($this->validator, 'validate') );

	    add_settings_field(
	        'delete_on_uninstall', __('Delete All Data on Uninstallation', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('If you want to permanently remove this plugin then you might want to set this to true.', 'wpfepp-plugin'),
					'id' 	=> 'delete_on_uninstall',
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
				settings_fields( 'wpfepp_data_settings_tab' );
				do_settings_sections( 'wpfepp_data_settings_tab' );
				submit_button( __('Save Settings', 'wpfepp-plugin'), 'primary', null, true );
			?>
		</form>
		<?php
	}

	public function section_callback($args){}
}