<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

/**
 * Manages the emails tab of the forms page.
 *
 * @package WPFEPP
 * @since 2.3.0
 **/
class WPFEPP_Tab_Form_Emails extends WPFEPP_Tab
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
		add_action('admin_init', array($this, 'save_emails'));
	}

	/**
	 * When users hit the submit button this function handles the request and redirects them back to the page.
	 **/
	public function save_emails(){
		if(!$this->form_manager->is_page())
			return;

		$result = 0;

		if( $_GET['action'] == 'edit' && isset($_POST['update-form-emails']) && isset($_POST['form_emails']) && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpfepp-update-form-emails') ){
			$form_emails 	= $this->validator->validate($_POST['form_emails']);
			$result 		= $this->db->update_form_emails($_GET['form'], $form_emails);
			$sendback 		= esc_url_raw( add_query_arg( array( 'updated' => $result ) ) );
			wp_redirect($sendback);
		}
	}

	/**
	 * Outputs the contents of the tab with the help of WordPress' settings API.
	 **/
	public function display() {

		$form 	= $this->db->get($_GET['form']);
		$form_emails = $form['emails'];
		$section = 'wpfepp_form_emails_section';
		$page = 'wpfepp_form_emails_tab';
		$callback = array($this->renderer, 'render');
		$args = array( 'group' => 'form_emails', 'curr' => $form_emails );

		add_settings_section( $section, '', array( $this, 'section_callback' ), $page );

		add_settings_field(
		    'user_email_subject', __( "User Email Subject", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'id' => 'user_email_subject',
					'type' => 'text'
				),
				$args
		    )
		);
		add_settings_field(
		    'user_email_content', __( "User Email Content", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'id' => 'user_email_content',
					'type' => 'textarea'
				),
				$args
		    )
		);
		add_settings_field(
		    'admin_email_subject', __( "Admin Email Subject (created)", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'id' => 'admin_email_subject',
					'type' => 'text'
				),
				$args
		    )
		);
		add_settings_field(
		    'admin_email_content', __( "Admin Email Content (created)", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'id' => 'admin_email_content',
					'type' => 'textarea'
				),
				$args
		    )
		);		
		add_settings_field(
		    'admin_email_subject_up', __( "Admin Email Subject (updated)", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'id' => 'admin_email_subject_up',
					'type' => 'text'
				),
				$args
		    )
		);
		add_settings_field(
		    'admin_email_content_up', __( "Admin Email Content (updated)", "wpfepp-plugin" ), $callback, $page, $section,
		    array_merge(
				array(
					'id' => 'admin_email_content_up',
					'type' => 'textarea'
				),
				$args
		    )
		);

		?>
			<form method="POST">
				<?php do_settings_sections( $page ); ?>
				<?php wp_nonce_field( 'wpfepp-update-form-emails', '_wpnonce', false, true ); ?>
				<?php submit_button(__('Save Changes', 'wpfepp-plugin'), 'primary', 'update-form-emails'); ?>
			</form>
		<?php
	}

	public function section_callback($args){
		?>
			<p class="description"><?php printf('%s <a href="'.admin_url('admin.php?page=wpfepp_settings&tab=email').'">%s</a>.', __('You can enable or disable the notification emails in the settings tab of this form. The sender name, sender address and email format can be modified', 'wpfepp-plugin'), __('here', 'wpfepp-plugin')); ?></p>
			<p class="description"><?php printf('You can use these placeholders in any of the following fields: %s, %s, %s, %s, %s, %s, %s, %s', '%%POST_TITLE%%', '%%POST_PERMALINK%%', '%%AUTHOR_NAME%%', '%%SITE_NAME%%', '%%SITE_URL%%', '%%ADMIN_NAME%%', '%%EDIT_LINK%%', '%%COPYSCAPE_STATUS%%'); ?></p>
		<?php
	}
}