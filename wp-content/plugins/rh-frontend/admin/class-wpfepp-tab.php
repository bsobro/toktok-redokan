<?php

/**
 * Represents an admin page tab. This class is not used directly. Instead, more specific child classes are used.
 *
 * @package WPFEPP
 * @since 2.3.0
 **/
class WPFEPP_Tab {

    /**
     * Plugin version.
     *
     * @access protected
     * @var string
     **/
    protected $version;

    /**
     * The slug of this tab.
     *
     * @access protected
     * @var string
     **/
	protected $slug;

	/**
	 * The human readable tab name that will be visible to the user.
	 *
     * @access protected
	 * @var string
	 **/
	protected $name;

	/**
	 * An instance of the WPFEPP_Field_Renderer class. It is responsible for printing the HTML of each setting field.
	 *
     * @access protected
	 * @var WPFEPP_Field_Renderer
	 **/
	protected $renderer;

	/**
	 * An instance of the WPFEPP_Field_Validator class. It is responsible for sanitizing the user input.
	 *
     * @access protected
	 * @var WPFEPP_Field_Validator
	 **/
	protected $validator;

	/**
	 * Used for fetching information from the database.
	 *
     * @access protected
	 * @var WPFEPP_DB_Table
	 **/
	protected $db;

	/**
	 * Class constructor. Initializes all the class variables.
	 *
	 * @var string $version Plugin version.
	 * @var string $slug Tab slug.
	 * @var string $name Tab name.
	 **/
	public function __construct($version, $slug, $name)
	{
		$this->load_dependencies();

		$this->version 		= $version;
		$this->slug 		= $slug;
		$this->name 		= $name;
		$this->renderer 	= WPFEPP_Field_Renderer::get_instance();
		$this->validator 	= WPFEPP_Field_Validator::get_instance();
		$this->db 			= WPFEPP_DB_Table::get_instance();
	}

	/**
	 * This function is called by the tab collection class. Classes extending WPFEPP_Tab are supposed to override this function.
	 **/
	public function add_actions(){}

	/**
	 * Includes the files of other classes required by WPFEPP_Tab.
	 **/
	private function load_dependencies(){
		require_once 'class-wpfepp-field-renderer.php';
		require_once 'class-wpfepp-field-validator.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-db-table.php';
	}

	/**
	 * Getter function for tab slug.
	 *
	 * @return string Tab slug.
	 **/
	public function get_slug(){
		return $this->slug;
	}

	/**
	 * Getter function for tab name.
	 *
	 * @return string Tab name.
	 **/
	public function get_name(){
		return $this->name;
	}
}

?>