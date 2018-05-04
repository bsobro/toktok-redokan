<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

class WPFEPP_Tab_Settings_ReCaptcha extends WPFEPP_Tab
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
		$section 		= 'wpfepp_recaptcha_settings_section';
		$tab 			= 'wpfepp_recaptcha_settings_tab';
		$option_id 		= 'wpfepp_recaptcha_settings';
		$curr_settings 	= get_option($option_id);
		$callback 		= array($this->renderer, 'render');
		$args 			= array('group' => $option_id, 'curr' => $curr_settings);

		add_settings_section( $section, '', array($this, 'section_callback'), $tab );

		register_setting( $tab, $option_id, array($this->validator, 'validate') );

	    add_settings_field(
	        'site_key', __('Site Key', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'id' 	=> 'site_key',
					'desc' 	=> sprintf(__('Your ReCaptcha site key. You can get this key %s.', 'wpfepp-plugin'), sprintf('<a href="http://www.google.com/recaptcha/admin">%s</a>', __('here', 'wpfepp-plugin'))),
					'type' 	=> 'text'
				),
				$args
			)
	    );

	    add_settings_field(
	        'secret', __('Secret', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> sprintf(__('Your ReCaptcha secret. You can get it %s.', 'wpfepp-plugin'), sprintf('<a href="http://www.google.com/recaptcha/admin">%s</a>', __('here', 'wpfepp-plugin'))),
					'id' 	=> 'secret',
					'type' 	=> 'text'
				),
				$args
			)
	    );

		add_settings_field(
	        'theme', __('Theme', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'id' 	=> 'theme',
					'desc' 	=> __('Color scheme of the ReCaptcha widget.', 'wpfepp-plugin'),
					'type' 	=> 'select',
					'items' => array('light' => 'Light', 'dark' => 'Dark')
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
				settings_fields( 'wpfepp_recaptcha_settings_tab' );
				do_settings_sections( 'wpfepp_recaptcha_settings_tab' );
				submit_button( __('Save Settings', 'wpfepp-plugin'), 'primary', null, true );
			?>
		</form>
		<?php
	}

	public function section_callback($args){
		?>
			<p class="description"><?php _e('In this section you can manage your ReCaptcha API keys and settings.', 'wpfepp-plugin'); ?></p>
		<?php
	}
}