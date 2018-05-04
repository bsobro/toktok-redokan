<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

class WPFEPP_Tab_Settings_Errors extends WPFEPP_Tab
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
	 * Registers the actions of this class with WordPress. This function is called by add_actions of WPFEPP_Tab_Collection, which in turn is called by add_actions of WPFEPP_Form_Manager.
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
		$section 		= 'wpfepp_errors_section';
		$tab 			= 'wpfepp_errors_tab';
		$option_id 		= 'wpfepp_errors';
		$current_errors = get_option($option_id);

		$callback 		= array($this->renderer, 'render');
		$args 			= array( 'group' => $option_id, 'curr' => $current_errors );
		
		add_settings_section( $section, '', array($this, 'section_callback'), $tab );

		register_setting( $tab, $option_id, array($this->validator, 'validate') );

	    add_settings_field(
	        'form', __('Form Error', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The main error shown when the user hits the submission button.', 'wpfepp-plugin'),
					'id' 	=> 'form',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'required', __('Required Field', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the user misses a required field.', 'wpfepp-plugin'),
					'id' 	=> 'required',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'min_words', __('Minimum Words', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the submission does not meet the minimum words requirement.', 'wpfepp-plugin'),
					'id' 	=> 'min_words',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'max_words', __('Maximum Words', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the submission does not meet the maximum words requirement.', 'wpfepp-plugin'),
					'id' 	=> 'max_words',
					'type' 	=> 'text'
				),
				$args
			)
	    );
		add_settings_field(
	        'min_symbols', __('Minimum Symbols', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the submission does not meet the minimum symbols requirement.', 'wpfepp-plugin'),
					'id' 	=> 'min_symbols',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'max_symbols', __('Maximum Symbols', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the submission does not meet the maximum symbols requirement.', 'wpfepp-plugin'),
					'id' 	=> 'max_symbols',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'max_links', __('Maximum Links', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the submission does not meet the maximum links requirement (used for non-hierarichal taxonomies like tags).', 'wpfepp-plugin'),
					'id' 	=> 'max_links',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'min_segments', __('Minimum Segments', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the submission does not meet the minimum segments requirement (used for non-hierarichal taxonomies like tags).', 'wpfepp-plugin'),
					'id' 	=> 'min_segments',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'max_segments', __('Maximum Segments', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the submission does not meet the maximum segments requirement (used for non-hierarichal taxonomies like tags).', 'wpfepp-plugin'),
					'id' 	=> 'max_segments',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'invalid_email', __('Invalid Email', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the user enters an invalid email.', 'wpfepp-plugin'),
					'id' 	=> 'invalid_email',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'invalid_url', __('Invalid URL', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the user enters an invalid URL.', 'wpfepp-plugin'),
					'id' 	=> 'invalid_url',
					'type' 	=> 'text'
				),
				$args
			)
	    );
	    add_settings_field(
	        'copyscape', __('CopyScape Error', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('The error shown when the content does not pass CopyScape (if you choose not to accept unoriginal submissions in the CopyScape tab).', 'wpfepp-plugin'),
					'id' 	=> 'copyscape',
					'type' 	=> 'text'
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
				settings_fields( 'wpfepp_errors_tab' );
				do_settings_sections( 'wpfepp_errors_tab' );
				submit_button( __('Save Settings', 'wpfepp-plugin'), 'primary', null, true );
			?>
		</form>
		<?php
	}

	function section_callback($args){
		?>
			<p class="description"><?php _e('Here you can customize the errors displayed by the plugin.', 'wpfepp-plugin'); ?></p>
		<?php	
	}

}