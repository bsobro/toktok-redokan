<?php

class Joonas {
	private static $initiated = false;
	
	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;

        /*
		add_action( 'wp_insert_comment', array( 'Akismet', 'auto_check_update_meta' ), 10, 2 );
		add_filter( 'preprocess_comment', array( 'Akismet', 'auto_check_comment' ), 1 );
		add_filter( 'rest_pre_insert_comment', array( 'Akismet', 'rest_auto_check_comment' ), 1 );

		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments' ) );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_old_comments_meta' ) );
		add_action( 'akismet_scheduled_delete', array( 'Akismet', 'delete_orphaned_commentmeta' ) );
		add_action( 'akismet_schedule_cron_recheck', array( 'Akismet', 'cron_recheck' ) );

		add_action( 'comment_form',  array( 'Akismet',  'add_comment_nonce' ), 1 );

		add_action( 'admin_head-edit-comments.php', array( 'Akismet', 'load_form_js' ) );
		add_action( 'comment_form', array( 'Akismet', 'load_form_js' ) );
		add_action( 'comment_form', array( 'Akismet', 'inject_ak_js' ) );
		add_filter( 'script_loader_tag', array( 'Akismet', 'set_form_js_async' ), 10, 3 );

		add_filter( 'comment_moderation_recipients', array( 'Akismet', 'disable_moderation_emails_if_unreachable' ), 1000, 2 );
		add_filter( 'pre_comment_approved', array( 'Akismet', 'last_comment_status' ), 10, 2 );
		
		add_action( 'transition_comment_status', array( 'Akismet', 'transition_comment_status' ), 10, 3 );

		// Run this early in the pingback call, before doing a remote fetch of the source uri
		add_action( 'xmlrpc_call', array( 'Akismet', 'pre_check_pingback' ) );
		*/
    }

	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function activate() {
		if ( version_compare( $GLOBALS['wp_version'], JOONAS__MINIMUM_WP_VERSION, '<' ) ) {
            // something to do
		}
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function deactivate( ) {
		// Remove any scheduled cron jobs.
		$joonas_cron_events = array(
			// 'akismet_schedule_cron_recheck',
			// 'akismet_scheduled_delete',
		);
		
		foreach ( $joonas_cron_events as $joonas_cron_event ) {
			$timestamp = wp_next_scheduled( $joonas_cron_event );
			
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $joonas_cron_event );
			}
		}
	}
	
}
