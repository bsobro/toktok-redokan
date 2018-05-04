<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

/**
 * Manages the form settings tab of the forms page.
 *
 * @package WPFEPP
 * @since 2.3.0
 **/
class WPFEPP_Tab_Form_Settings extends WPFEPP_Tab
{
	/**
	 * An instance of the form manager class, responsible for instantiating this tab.
	 *
	 * @var WPFEPP_Form_Manager
	 **/
	private $form_manager;

	/**
	 * Class constructor. Initializes class variables and calls parent constructor.
	 *
	 * @var string $version Plugin version.
	 * @var string $slug Tab slug.
	 * @var string $name Tab name.
	 * @var string $form_manager An instance of the form manager class.
	 **/
	function __construct($version, $slug, $name, $form_manager) {
		$this->form_manager = $form_manager;
		parent::__construct($version, $slug, $name);
	}

	/**
	 * Registers the actions of this class with WordPress. This function is called by add_actions of WPFEPP_Tab_Collection, which in turn is called by add_actions of WPFEPP_Form_Manager.
	 **/
	public function add_actions(){
		add_action('admin_init', array($this, 'save_settings'));
	}

	/**
	 * When users hit the submit button this function handles the request and redirects them back to the page.
	 **/
	public function save_settings(){
		if(!$this->form_manager->is_page())
			return;

		$result = 0;

		if( $_GET['action'] == 'edit' && isset($_POST['update-form-settings']) && isset($_POST['form_settings']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpfepp-update-form-settings') ){
			$form_settings 	= $this->validator->validate($_POST['form_settings']);
			$result 		= $this->db->update_form_settings($_GET['form'], $form_settings);
			$sendback 		= esc_url_raw( add_query_arg( array( 'updated' => $result ) ) );
			wp_redirect($sendback);
		}
	}

	/**
	 * Outputs the contents of the tab with the help of WordPress' settings API.
	 **/
	public function display() {
		$form = $this->db->get( $_GET['form'] );
		$form_settings = $form['settings'];
		$page = 'wpfepp_form_settings_tab';
		$section = 'wpfepp_form_settings_section';
		$callback = array( $this->renderer, 'render' );
		$args = array( 'group' => 'form_settings', 'curr' => $form_settings );
		
		add_settings_section( $section, '', null, $page);

		add_settings_field(
		    'no_restrictions', __('Disable restrictions for', 'wpfepp-plugin'), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "These roles will have unrestricted access to the form.", "wpfepp-plugin" ),
					'id' => 'no_restrictions',
					'type' => 'roles'
				),
				$args
		    )
		);

		add_settings_field(
		    'instantly_publish', __( "Instantly publish posts by", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "The post submitted by these roles will be published instantly.", "wpfepp-plugin" ),
					'id' => 'instantly_publish',
					'type' => 'roles'
				),
				$args
		    )
		);
		add_settings_field(
		    'width', __( "Width", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "Maximum form width.", "wpfepp-plugin" ),
					'id' => 'width',
					'type' => 'text'
				),
				$args
		    )
		);
		add_settings_field(
		    'redirect_url', __( "Redirect URL", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "The user will be redirected to this URL after successful submission. Leave this empty to disable redirection.", "wpfepp-plugin" ),
					'id' => 'redirect_url',
					'type' => 'text'
				),
				$args
		    )
		);
		add_settings_field(
		    'button_color', __( "Button Color", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "Color of the submission button.", "wpfepp-plugin" ),
					'id' => 'button_color',
					'type' => 'select',
					'items' => array( 'blue' => __( "Blue", "wpfepp-plugin" ), 'green' => __( "Green", "wpfepp-plugin" ), 'red' => __( "Red", "wpfepp-plugin" ), 'brown' => __( "Brown", "wpfepp-plugin" ) )
				),
				$args
		    )
		);
		add_settings_field(
		    'enable_drafts', __( "Allow users to save drafts", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => '',
					'id' => 'enable_drafts',
					'type' => 'bool'
				),
				$args
		    )
		);
	    add_settings_field(
	        'user_emails', __( "User Emails", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "Send thank you email to user on post submission?", "wpfepp-plugin" ),
					'id' => 'user_emails',
					'type' => 'bool'
				),
				$args
		    )
	    );
	    add_settings_field(
	        'admin_emails', __( "Admin Email New", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "Send email to admin on post submission?", "wpfepp-plugin" ),
					'id' => 'admin_emails',
					'type' => 'bool'
				),
				$args
		    )
	    );
	    add_settings_field(
	        'admin_email_up', __( "Admin Email Edit", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "Send email to admin on post updating?", "wpfepp-plugin" ),
					'id' => 'admin_email_up',
					'type' => 'bool'
				),
				$args
		    )
	    );
	    add_settings_field(
	        'copyscape_enabled', __( "Enable CopyScape", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "Enable CopyScape for this form. In order to use this feature you need to add your CopyScape keys in the plugin settings.", "wpfepp-plugin" ),
					'id' => 'copyscape_enabled',
					'type' => 'bool'
				),
				$args
		    )
	    );
	    add_settings_field(
	        'captcha_enabled', __( "Enable Captcha", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'desc' => __( "Enable captcha for this form. In order to use this feature you need to add your ReCaptcha keys in the plugin settings.", "wpfepp-plugin" ),
					'id' => 'captcha_enabled',
					'type' => 'bool'
				),
				$args
		    )
	    );

	    ?>
			<form method="POST">
				<?php do_settings_sections( $page ); ?>
				<?php wp_nonce_field( 'wpfepp-update-form-settings', '_wpnonce', false, true ); ?>
				<?php submit_button(__('Save Settings', 'wpfepp-plugin'), 'primary', 'update-form-settings'); ?>
			</form>
	    <?php
	}
}