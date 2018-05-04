<?php

/**
 * A singleton class responsible for interacting with the database.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_DB_Table
{
	/**
	 * A variable initialized with the global $wpdb and used for performing database operations
	 *
	 * @access private
	 * @var wpdb
	 **/
	private $db;

	/**
	 * Name of the database table
	 *
	 * @access private
	 * @var string
	 **/
	private $table_name;

	/**
	 * Version of the database table. Changing this number will cause the database table to be dropped and built again.
	 *
	 * @access private
	 * @var string
	 **/
	private $db_version;

	/**
	 * Class constructor. Initializes all the variables.
	 *
	 * @return void
	 **/
	public function __construct()
	{
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = $this->db->prefix ."wpfepp_forms";
		$this->old_table_name = "wpfepp_forms"; // since v.2.3
		$this->db_version = "2.3";
	}

	/**
	 * Returns the existing instance of the class. If an instance does not exist, creates and returns a new instance.
	 *
	 * @return WPFEPP_DB_Table An instance of the WPFEPP_DB_Table class.
	 **/
	public static function get_instance() {
		static $instance = null;
		if($instance == null){
			$instance = new WPFEPP_DB_Table();
		}
		return $instance;
	}

	/**
	 * Creates the database table. It is also responsible for recreating the table if it is not up-to-date.
	 **/
	public function create_table() {
		$current_version = get_option( 'wpfepp_db_table_version' );
		if($current_version && $current_version == $this->db_version && $this->db->get_var("SHOW TABLES LIKE '$this->table_name'") == $this->table_name){
			return;
		}

			$charset_collate = $this->db->get_charset_collate();
			$sql = "
				CREATE TABLE $this->table_name (
					id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
					name TINYTEXT NOT NULL,
					post_type TINYTEXT NOT NULL,
					description TEXT NULL,
					fields LONGTEXT NULL,
					settings LONGTEXT NULL,
					emails LONGTEXT NULL,
					extended LONGTEXT NULL,
					UNIQUE KEY id (id)
				) $charset_collate;
			";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
		// since v.2.3
		if($this->db->get_var("SHOW TABLES LIKE '$this->old_table_name'") == $this->old_table_name){
			$this->upgrade_new_name_table();
		}
		
		update_option('wpfepp_db_table_version', $this->db_version);
	}

	/**
	 * Removes the database table.
	 **/
	public function remove_table(){
		$query = "DROP TABLE $this->table_name";
		return $this->db->query($query);
	}

	/**
	 * Getter function for the database version variable.
	 *
	 * @return string Database version.
	 **/
	public function get_db_version(){
		return $this->db_version;
	}

	/**
	 * Adds a new form to the database table.
	 *
	 * @param string Form name.
	 * @param string Post type that this form is meant to handle.
	 * @param string Form description.
	 * @param array An array consisting of all the form fields and their restrictions. Stored as a serialized string in the database.
	 * @param array An array consisting of form settings. Stored as a serialized string in the database.
	 * @param array An array consisting of form extended settings. Stored as a serialized string in the database.
	 * @return mixed False in case the row could not be inserted. Otherwize the number of affected rows.
	 **/
	public function add($name, $post_type, $description="", $fields="", $settings="", $emails="", $extended=""){
		return $this->db->insert( $this->table_name, array('name' => $name, 'post_type' => $post_type, 'description' => $description, 'fields' => $this->serialize($fields), 'settings' => $this->serialize($settings), 'emails' => $this->serialize($emails), 'extended' => $this->serialize($extended) ) );
	}

	/**
	 * Fetches and returns a row from the database.
	 *
	 * @param int $id Database id of the row.
	 * @return array An associative array with column names as keys.
	 **/
	public function get( $id ){
		$form = $this->db->get_row("SELECT * FROM $this->table_name WHERE id=$id", ARRAY_A);
		if($form){
			$form["fields"] 	= $this->unserialize($form["fields"]);
			$form["settings"] 	= $this->unserialize($form["settings"]);
			$form["emails"] 	= $this->unserialize($form["emails"]);
			$form["extended"] 	= $this->unserialize($form["extended"]);
		}
		return $form;
	}

	public function get_forms_for_select(){
		$forms = $this->db->get_results("SELECT * FROM $this->table_name", ARRAY_A);
		$rtn_arr = array();
		if(is_array($forms) && count($forms)){
			foreach ($forms as $key => $form) {
				$rtn_arr[$form['id']] = $form['id'] . ' - ' . $form['name'];
			}
		}
		return $rtn_arr;
	}

	/**
	 * Fetches and returns the total number of rows in the database table.
	 *
	 * @return int Number of rows in the database table.
	 **/
	public function get_total_count(){
		$count = $this->db->get_var("SELECT COUNT(*) FROM $this->table_name");
		return isset($count)?$count:0;
	}

	/**
	 * Gets all the rows in a particular range. Used by the list table class for fetching forms on a particular page.
	 *
	 * @param int $curr_page Current page of the list table.
	 * @param int $per_page Number of items to fetch.
	 * @return array A numerically indexed array of associative arrays, using column names as keys.
	 **/
	public function get_forms($curr_page, $per_page, $post_type = false){
		$start = (($curr_page-1)*$per_page);
		$where = ($post_type) ? "post_type = '$post_type'" : 1;
		$query = "SELECT * FROM $this->table_name WHERE $where ORDER BY id DESC LIMIT $start, $per_page";
		return $this->db->get_results( $query, ARRAY_A );
	}

	/**
	 * Deletes single row at once. 
	 *
	 * @param number $id An number string of row id.
	 * @return mixed Number of rows affected or false in case of failure.
	 **/
	public function delete_single( $id ) {
		$query = "DELETE FROM $this->table_name WHERE id = ($id)";
		return $this->db->query( $query );
	}
	
	/**
	 * Deletes multiple rows at once. 
	 *
	 * @param array $ids An array of row ids.
	 * @return mixed Number of rows affected or false in case of failure.
	 **/
	public function delete_multiple( $ids ) {
		$id_string = join( ',', $ids );
		$query = "DELETE FROM $this->table_name WHERE id IN ($id_string)";
		return $this->db->query( $query );
	}

	/**
	 * Checks if a row exists in the database table.
	 *
	 * @param int $id Id of the row whose existence we want to check.
	 * @return int Number of rows that have the provided id as primary key.
	 **/
	public function form_exists( $id ){
		return $this->db->get_var("SELECT COUNT(*) FROM $this->table_name WHERE id=$id");
	}

	/**
	 * Updates the 'fields' column of a particular row.
	 *
	 * @param int $id Id of the row whose 'fields' column we wish to update.
	 * @param array $fields An array of fields. This array is serialized before insertion into the database.
	 * @return mixed Number of rows affected or false in case of failure.
	 **/
	public function update_form_fields($id, $fields){
		return $this->db->update( $this->table_name, array('fields' => $this->serialize($fields)), array('id' => $id) );
	}

	/**
	 * Updates the 'settings' column of a particular row.
	 *
	 * @param int $id Id of the row whose 'settings' column we wish to update.
	 * @param array $settings An array of form settings. This array is serialized before insertion into the database.
	 * @param array $extended An array of form extended settings. This array is serialized before insertion into the database.
	 * @return mixed Number of rows affected or false in case of failure.
	 **/
	public function update_form_settings($id, $settings){
		return $this->db->update( $this->table_name, array('settings' => $this->serialize($settings)), array('id' => $id) );
	}

	public function update_form_emails($id, $emails){
		return $this->db->update( $this->table_name, array('emails' => $this->serialize($emails)), array('id' => $id) );
	}
	
	public function update_form_extended($id, $extended){
		return $this->db->update( $this->table_name, array('extended' => $this->serialize($extended)), array('id' => $id) );
	}

	/**
	 * Upgrades all the existing forms in the database to include the new fields and settings offered by the latest plugin version.
	 *
	 * @param array $default_fields An array containing the default form fields and their settings.
	 * @param array $default_settings An array containing the default form settings.
	 * @param array $default_emails An array containing the default emails.
	 * @param array $default_extended An array containing the default emails.
	 * @param array $default_custom_field An array containing the default custom field settings.
	 * @param array $post_type The post type for which we want to upgrade the forms.
	 **/
	public function upgrade_forms( $default_fields, $default_settings, $default_emails, $default_extended, $default_custom_field, $post_type ) {
		$forms = $this->get_forms(1, 1000, $post_type);
		if(is_array($forms) && count($forms)){
			foreach ($forms as $key => $form) {
				$form_fields 		= $this->unserialize($form['fields']);
				$form_settings 		= $this->unserialize($form['settings']);
				$form_emails 		= $this->unserialize($form['emails']);
				$form_extended 		= $this->unserialize($form['extended']);
				
				$upgraded_fields 	= wpfepp_update_form_fields($form_fields, $default_fields, $default_custom_field);
				$upgraded_settings 	= wpfepp_update_array($form_settings, $default_settings);
				$upgraded_emails 	= wpfepp_update_array($form_emails, $default_emails);
				$upgraded_extended 	= wpfepp_update_array($form_extended, $default_extended);
				
				$this->db->update( $this->table_name, array('fields' => $this->serialize($upgraded_fields), 'settings' => $this->serialize($upgraded_settings), 'emails' => $this->serialize($upgraded_emails), 'extended' => $this->serialize($upgraded_extended)), array('id' => $form['id']) );
			}
		}
	}

	private function serialize($item){
		return base64_encode(serialize($item));
	}

	private function unserialize($item){
		if(base64_decode($item, true) !== false)
			$item = base64_decode($item);

		return unserialize($item);
	}

	public function delete_post_meta( $meta_key ) {
		$table = $this->db->postmeta;
		return $this->db->query("DELETE FROM $table WHERE meta_key = $meta_key");
	}

	/**
	 * Removes old table and moves its data into renamed one
	 *
	 * @since v.2.3
	 **/
	public function upgrade_new_name_table(){
		$copy_query = "INSERT INTO $this->table_name SELECT * FROM $this->old_table_name";
		$this->db->query($copy_query);
		$delete_query = "DROP TABLE $this->old_table_name";
		$this->db->query($delete_query);
	}
	
}

?>