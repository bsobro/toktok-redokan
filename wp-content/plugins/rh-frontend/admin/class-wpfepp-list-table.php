<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Creates a list table the WordPress backend. Extends WP_List_Table class provided by WordPress.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_List_Table extends WP_List_Table {

    /**
     * Plugin version. This is used in wp_enqueue_style and wp_enqueue_script to make sure that the end user doesn't get outdated scripts and styles because of browser caching.
     *
     * @access private
     * @var string
     **/
    private $version;
    
    /**
     * Fetches data from the database table so that the list table can be populated
     *
     * @access private
     * @var WPFEPP_DB_Table
     **/
    private $db;

    /**
     * Class constructor. Initializes class attributes and invokes the constructor of parent class WP_List_Table
     **/
    public function __construct( $version ){
        $this->load_dependencies();
        $this->version = $version;
        $this->db = WPFEPP_DB_Table::get_instance();

        global $status, $page;
        parent::__construct( array(
            'singular' => 'form',
            'plural' => 'forms',
            'ajax' => false,
            'screen' => $_REQUEST['page']
        ) );
    }

    private function load_dependencies(){
        require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-db-table.php';
    }
    
    /**
     * The default function used for printing column values. This function is used whenever a more specific function isn't available.
     *
     * @param array $item An associative array representing a row from the database table.
     * @param string $column_name Name of the column currently being displayed.
     * @return string Column value.
     **/
    function column_default( $item, $column_name ){
        return stripslashes( $item[$column_name] );
    }

    /**
     * Prints out the form name and action links in each row.
     *
     * @param array $item An associative array representing a row from the database table.
     * @return string Form name and action links formatted into a single string.
     **/
    function column_name( $item ){
        
        //Build row actions
        $actions = array(
            'edit' => sprintf( '<a href="?page=%s&action=%s&form=%s">%s</a>', $_REQUEST['page'], 'edit', $item['id'], __( 'Edit', 'wpfepp-plugin' ) ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&form=%s">%s</a>', $_REQUEST['page'], 'delete', $item['id'], __( 'Delete', 'wpfepp-plugin' ) )
        );
        
        //Return the title contents
        return sprintf('%1$s %2$s', stripslashes( $item['name'] ), $this->row_actions( $actions ) );
    }

    /**
     * Prints out a check box in each row.
     *
     * @param array $item An associative array representing a row from the database table.
     * @return string HTML for the check box.
     **/
    function column_cb( $item ){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            $item['id']  //The value of the checkbox should be the record's id
        );
    }
    
    /**
     * Dictates the table's columns and their titles.
     *
     * @return array 
     **/
    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'id' => __( 'ID', 'wpfepp-plugin' ),
            'name' => __( 'Name', 'wpfepp-plugin' ),
            'post_type' => __( 'Post Type', 'wpfepp-plugin' ),
            'description' => __( 'Description', 'wpfepp-plugin' )
        );
        return $columns;
    }

    /**
     * Defines the bulk actions for the list table.
     *
     * @return array An associative array of bulk actions.
     **/
    function get_bulk_actions() {
        $actions = array(
            'delete' => __( 'Delete', 'wpfepp-plugin' )
        );
        return $actions;
    }

    /**
     * Queries the database, sorts and filters the data, and gets it ready to be displayed.
     **/
    public function prepare_items() {

        $per_page = 25;
        $hidden  = array();
        $columns = $this->get_columns();
        $sortable = array();
        $curr_page = $this->get_pagenum();
        $total_items  = $this->db->get_total_count();
        $data = $this->db->get_forms( $curr_page, $per_page );
        $this->items = $data;
        $this->_column_headers  = array( $columns, $hidden, $sortable );
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil( $total_items/$per_page )   //WE have to calculate the total number of pages
        ) );
    }
    
}