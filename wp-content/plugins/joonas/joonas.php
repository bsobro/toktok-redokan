<?php
 /*
 Plugin Name: Plugin Name is joonas
 Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 Description: A brief description of the Plugin.
 Version: 0.1.0
 Author: Joona Yoon
 Author URI: http://URI_Of_The_Plugin_Author
 License: A "Slug" license name e.g. GPL2
 */


register_activation_hook( __FILE__, array( 'JoonasPlugIn', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'JoonasPlugIn', 'plugin_deactivation' ) );


?>
