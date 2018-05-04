<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

class WPFEPP_Tab_Settings_Email extends WPFEPP_Tab
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
		$section 		= 'wpfepp_email_settings_section';
		$tab 			= 'wpfepp_email_settings_tab';
		$option_id 		= 'wpfepp_email_settings';
		$curr_settings 	= get_option($option_id);
		$callback 		= array($this->renderer, 'render');
		$args 			= array('group' => $option_id, 'curr' => $curr_settings);

		add_settings_section( $section, '', array($this, 'section_callback'), $tab );

		register_setting( $tab, $option_id, array($this->validator, 'validate') );

	    add_settings_field(
	        'sender_name', __('Sender Name', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('This name will be used as the sender name in notification emails.', 'wpfepp-plugin'),
					'id' 	=> 'sender_name',
					'type' 	=> 'text'
				),
				$args
			)
	    );

	    add_settings_field(
	        'sender_address', __('Sender Address', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('Emails will be sent from this address.', 'wpfepp-plugin'),
					'id' 	=> 'sender_address',
					'type' 	=> 'text'
				),
				$args
			)
	    );

	    add_settings_field(
	        'email_format', __('Email Format', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('In which format would you like to send the emails.', 'wpfepp-plugin'),
					'id' 	=> 'email_format',
					'type' 	=> 'select',
					'items' => array('plain' => __('Plain Text', 'wpfepp-plugin'), 'html' => __('HTML', 'wpfepp-plugin'))
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
				settings_fields( 'wpfepp_email_settings_tab' );
				do_settings_sections( 'wpfepp_email_settings_tab' );
				submit_button( __('Save Settings', 'wpfepp-plugin'), 'primary', null, true );
			?>
		</form>
		<?php
	}

	public function section_callback($args){
		?>
			<p class="description"><?php _e('These are the general email settings of the plugin. You can modify the actual content of the emails by editing your form.', 'wpfepp-plugin'); ?></p>
		<?php
	}
}