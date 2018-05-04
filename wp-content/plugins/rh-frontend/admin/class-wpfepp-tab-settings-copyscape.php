<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

class WPFEPP_Tab_Settings_CopyScape extends WPFEPP_Tab
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
		$section 		= 'wpfepp_copyscape_settings_section';
		$tab 			= 'wpfepp_copyscape_settings_tab';
		$option_id 		= 'wpfepp_copyscape_settings';
		$curr_settings 	= get_option($option_id);
		$callback 		= array($this->renderer, 'render');
		$args 			= array('group' => $option_id, 'curr' => $curr_settings);

		add_settings_section( $section, '', array($this, 'section_callback'), $tab );

		register_setting( $tab, $option_id, array($this->validator, 'validate') );

	    add_settings_field(
	        'username', __('Username', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'id' 	=> 'username',
					'desc' 	=> __('Your copyscape username.', 'wpfepp-plugin'),
					'type' 	=> 'text'
				),
				$args
			)
	    );

	    add_settings_field(
	        'api_key', __('API Key', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> sprintf(__('Your copyscape API key. You can get this key %s.', 'wpfepp-plugin'), sprintf('<a href="http://www.copyscape.com/apiconfigure.php#key">%s</a>', __('here', 'wpfepp-plugin'))),
					'id' 	=> 'api_key',
					'type' 	=> 'text'
				),
				$args
			)
	    );

		add_settings_field(
	        'block', __('Do not accept unoriginal submissions', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'id' 	=> 'block',
					'desc' 	=> __('If you set this option to true, a user will not be able to submit unoriginal content. A new CopyScape query will be done on each try. If you set this to false, the submission will be added to the pending queue and the copyscape status will be displayed in a new column (see next option).', 'wpfepp-plugin'),
					'type' 	=> 'bool'
				),
				$args
			)
	    );

		add_settings_field(
	        'column_types', __('Add a CopyScape column for these post types', 'wpfepp-plugin'), $callback, $tab, $section,
	        array_merge(
				array(
					'id' 	=> 'column_types',
					'desc' 	=> __('The new columns will appear in the admin area.', 'wpfepp-plugin'),
					'type' 	=> 'multicheckbox',
					'items' => wpfepp_get_post_types()
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
				settings_fields( 'wpfepp_copyscape_settings_tab' );
				do_settings_sections( 'wpfepp_copyscape_settings_tab' );
				submit_button( __('Save Settings', 'wpfepp-plugin'), 'primary', null, true );
			?>
		</form>
		<?php
	}

	public function section_callback($args){
		?>
			<p class="description"><?php _e('In this section you can manage your CopyScape API keys and settings.', 'wpfepp-plugin'); ?></p>
		<?php
	}
}