<?php 
if ( ! class_exists( 'GMW_Form_Init' ) ) {
	return;
}

/**
 * GMW_PT_Search_Query class
 * 
 */
class GMW_Posts_Locator_Form extends GMW_Form {
    
    /**
     * gmw_location database fields that will be 
     *
     * added to the posts query.
     * 
     * @var array
     */
    public $db_fields = array(
        'ID as location_id',
        'object_type',
        'object_id',
        'featured as featured_location',
        'user_id',
        'latitude as lat',
        'longitude as lng',
        'street',
        'city',
        'region_name',
        'postcode',
        'country_code',
        'address',
        'formatted_address',
        'map_icon'
    );

    /**
     * Permalink hook
     * 
     * @var string
     */
    public $object_permalink_hook = 'the_permalink';

    /**
     * Info window data
     * 
     * @param  [type] $location [description]
     * @return [type]           [description]
     */
    public function get_info_window_args( $post ) {

        if ( isset( $this->form['info_window']['image'] ) ) {
            if ( $this->form['info_window']['image']['enabled'] == '' ) {
                $image = false;
            } else {
                $image = get_the_post_thumbnail( $post->ID, array( 
                    $this->form['info_window']['image']['width'],
                    $this->form['info_window']['image']['height'] 
                ) );
            }
        } else {
            $image = get_the_post_thumbnail( $post->ID, array( 200, 200 ) );
        }

        return array(
            'prefix' => $this->prefix,
            'type'   => ! empty( $this->form['info_window']['iw_type'] ) ? $this->form['info_window']['iw_type'] : 'standard',
            'image'  => $image,
            'url'    => get_permalink( $post->ID ),
            'title'  => $post->post_title
        );
    }

    /**
     * Query taxonomies on form submission
     * 
     * @param  [type] $tax_args [description]
     * @param  [type] $gmw      [description]
     * @return [type]           [description]
     */
    public function query_taxonomies() {

        // query taxonomies if submitted in form
        if ( empty( $this->form['form_values']['tax'] ) ) {
            return false;
        }

        $tax_value = false;
        $output    = array( 'relation' => 'AND' );
            
        foreach ( $this->form['form_values']['tax'] as $taxonomy => $values ) {

            if ( array_filter( $values ) )  { 
                $output[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'id',
                    'terms'    => $values,
                    'operator' => 'IN'
                );
            }

            // extend the taxonomy query
            $output = apply_filters( 'gmw_pt_query_taxonomy', $output, $taxonomy, $values, $this->form );
        }

        // verify that there is at least one query to performe
        if ( empty( $output[0] ) ) {
            $output = false;
        }

        return $output;
    }

    /**
     * Modify wp_query clauses to search by distance
     * @param $clauses
     * @return $clauses
     */
    public function query_clauses( $clauses ) {
                  
        $count     = 0;
        $db_fields = '';
           
        // generate the db fields
        foreach ( $this->db_fields as $field ) {

            if ( $count > 0 ) {
                $db_fields .= ', ';
            }

            $count++;

            if ( strpos( $field, 'as' ) !== FALSE ) {
                
                $field = explode( ' as ', $field );
                
                $db_fields .= "gmw_locations.{$field[0]} as {$field[1]}";

                // Here we are including latitude and longitude fields
                // using their original field name.
                // for backward compatibility, we also need to have "lat" and "lng" 
                // in the location object and that is what we did in the line above.
                // The lat and lng field are too involve and need to carfully change it.
                // eventually we want to completly move to using latitude and longitude.
                if ( $field[0] == 'latitude' || $field[0] == 'longitude' ) {
                    $db_fields .= ",gmw_locations.{$field[0]}";
                }

            } else {

                $db_fields .= "gmw_locations.{$field}";
            }
        }

        global $wpdb;

        // add the location db fields to the query
        $clauses['fields'] .= ", {$db_fields}";

        // get address filters query
        $address_filters = GMW_Location::query_address_fields( $this->get_address_filters(), $this->form );

        // when address provided, and not filtering based on address fields, we will do proximity search
        if ( $address_filters == '' && ! empty( $this->form['lat'] ) && ! empty( $this->form['lng'] ) ) {

            // generate some radius/units data
            if ( in_array( $this->form['units_array']['units'], array( 'imperial', 3959, 'miles' ) ) ) {
                $earth_radius = 3959;
                $units        = 'mi';
                $degree       = 69.0;
            } else {
                $earth_radius = 6371;
                $units        = 'km';
                $degree       = 111.045;
            }

            // add units to locations data
            $clauses['fields'] .= ", '{$units}' AS units";

            // since these values are repeatable, we escape them previous 
            // the query instead of running multiple prepares.
            $lat      = esc_sql( $this->form['lat'] );
            $lng      = esc_sql( $this->form['lng'] );
            $distance = esc_sql( $this->form['radius'] );
        
            $clauses['fields'] .= ", ROUND( {$earth_radius} * acos( cos( radians( {$lat} ) ) * cos( radians( gmw_locations.latitude ) ) * cos( radians( gmw_locations.longitude ) - radians( {$lng} ) ) + sin( radians( {$lat} ) ) * sin( radians( gmw_locations.latitude ) ) ),1 ) AS distance";

            $clauses['join'] .= " INNER JOIN {$wpdb->base_prefix}gmw_locations gmw_locations ON $wpdb->posts.ID = gmw_locations.object_id ";

            // calculate the between point 
            $bet_lat1 = $lat - ( $distance / $degree );
            $bet_lat2 = $lat + ( $distance / $degree );
            $bet_lng1 = $lng - ( $distance / ( $degree * cos( deg2rad( $lat ) ) ) );
            $bet_lng2 = $lng + ( $distance / ( $degree * cos( deg2rad( $lat ) ) ) );

            $clauses['where'] .= " AND gmw_locations.object_type = 'post'";
            $clauses['where'] .= " AND gmw_locations.latitude BETWEEN {$bet_lat1} AND {$bet_lat2}";
            $clauses['where'] .= " AND gmw_locations.longitude BETWEEN {$bet_lng1} AND {$bet_lng2} ";

            // filter locations based on the distance
            $clauses['having'] = "HAVING distance <= {$distance} OR distance IS NULL";
    
            // if we order by the distance.
            if ( $this->form['query_args']['orderby'] == 'distance' ) {
                $clauses['orderby'] = 'distance';
            }

        } else {
            
            //if showing posts without location
            if ( $this->enable_objects_without_location ) {
                
                // left join the location table into the query to display posts with no location as well
                $clauses['join']  .= " LEFT JOIN {$wpdb->base_prefix}gmw_locations gmw_locations ON $wpdb->posts.ID = gmw_locations.object_id ";
                $clauses['where'] .= " {$address_filters} ";
                $clauses['where'] .= " AND gmw_locations.object_type = 'post'";

            } else {

                $clauses['join']  .= " INNER JOIN {$wpdb->base_prefix}gmw_locations gmw_locations ON $wpdb->posts.ID = gmw_locations.object_id ";
                $clauses['where'] .= " {$address_filters} AND ( gmw_locations.latitude != 0.000000 && gmw_locations.longitude != 0.000000 ) ";
                $clauses['where'] .= " AND gmw_locations.object_type = 'post'";
            }      
        }  
        
        // modify the clauses
        $clauses = apply_filters( 'gmw_pt_location_query_clauses', $clauses, $this->form );

        if ( ! empty( $clauses['having'] ) ) {

            if ( empty( $clauses['groupby'] ) ) {
                $clauses['groupby'] = $wpdb->prefix.'posts.ID';
            }

            $clauses['groupby'] .= ' '.$clauses['having'];

            unset( $clauses['having'] );
        } 

        return $clauses; 
    }

    /**
     * Query results
     * 
     * @return [type] [description]
     */
    public function search_query() {

    	// get the post types from page load settings
        if ( $this->form['page_load_action'] ) { 

            $post_types = ! empty( $this->form['page_load_results']['post_types'] ) ? $this->form['page_load_results']['post_types'] : 'post';	

        // when set to 1 means that we need to show all post types
        } elseif ( ! empty( $this->form['form_values']['post'] ) && array_filter( $this->form['form_values']['post'] ) ) {

            $post_types = $this->form['form_values']['post'];

        } else {

            $post_types = ! empty( $this->form['search_form']['post_types'] ) ? $this->form['search_form']['post_types'] : 'post';
        }
        
        // get query args for cache
        if ( $this->form['page_load_action'] ) {

            $gmw_query_args = $this->form['page_load_results'];

        } elseif ( $this->form['submitted'] ) {  

            $gmw_query_args = $this->form['form_values'];
        }

        $gmw_query_args['show_non_located'] = $this->enable_objects_without_location;

        // tax query can be disable if a custom query is needed.
        if ( apply_filters( 'gmw_enable_taxonomy_search_query', true, $this->form, $this ) ) {
            $tax_args = $this->query_taxonomies(); 
        } else {
            $tax_args = array();
        }
        
        $meta_args = false;
        
        if ( empty( $this->form['get_per_page'] ) || $this->form['get_per_page'] == -1 ) {
            $this->form['get_per_page'] = -1;
        }

        //query args
        $this->form['query_args'] = apply_filters( 'gmw_pt_search_query_args', array(
            'post_type'           => $post_types,
            'post_status'         => array( 'publish' ),
            'tax_query'           => apply_filters( 'gmw_pt_tax_query', $tax_args, $this->form ),
            'posts_per_page'      => $this->form['get_per_page'],
            'paged'               => $this->form['paged'],
            'meta_query'          => apply_filters( 'gmw_pt_meta_query', $meta_args, $this->form ),
            'ignore_sticky_posts' => 1,
            'orderby'             => 'distance',
            'gmw_args'            => $gmw_query_args
        ), $this->form, $this );

        $this->form = apply_filters( 'gmw_pt_form_before_posts_query', $this->form, $this );

        $internal_cache = GMW()->internal_cache;

        if ( $internal_cache ) {

            // cache key
            $hash = md5( json_encode( $this->form['query_args'] ) );
            $query_args_hash = 'gmw' . $hash . GMW_Cache_Helper::get_transient_version( 'gmw_get_object_post_query' );
        }

        // look for query in cache
        if ( ! $internal_cache || false === ( $this->query = get_transient( $query_args_hash ) ) ) {
        //if ( 1 == 1 ) {   
            //print_r( 'WP posts query done' );
            
            //add filters to wp_query to do radius calculation and get locations detail into results
            add_filter( 'posts_clauses', array( $this, 'query_clauses' ) );

	        // posts query
	        $this->query = new WP_Query( $this->form['query_args'] );
            
            remove_filter( 'posts_clauses', array( $this, 'query_clauses' ) );
            
            // set new query in transient     
            if ( $internal_cache ) {
                    
                /**
                 * This is a temporary solution for an issue with caching SQL requests
                 * For some reason when LIKE is being used in SQL WordPress replace the % of the LIKE
                 * with long random numbers. This SQL is still being saved in the transient. Hoever,
                 * it is not being pulled back properly when GEO my WP trying to use it. 
                 * It shows an error "unserialize(): Error at offset " and the value returns blank.
                 * As a temporary work around, we remove the [request] value, which contains the long numbers, from the WP_Query and save it in the transien without it. 
                 * @var [type]
                 */
                unset( $this->query->request );

                set_transient( $query_args_hash, $this->query, GMW()->internal_cache_expiration );
            }
        }   

        //Modify the form after the search query
        $this->form = apply_filters( 'gmw_pt_form_after_posts_query', $this->form, $this );

        // make sure posts exist
        if ( empty( $this->query->posts ) ) {
            return false;
        }

        $this->form['results'] 	     = $this->query->posts;
        $this->form['results_count'] = count( $this->query->posts );
        $this->form['total_results'] = $this->query->found_posts;
        $this->form['max_pages']     = $this->query->max_num_pages;
	               
        // if showing the list of results we use the 'the_post'
        // hook to generate the_location data. 
        if ( $this->form['display_list'] ) {
            
            add_action( 'the_post', array( $this, 'the_post' ), 5 );

            add_action( 'gmw_shortcode_end', array( $this, 'remove_the_post' ) );

        // otherwise, if only the map shows, we need to run a loop
        // to generate the map data of each location.
        } else {
            
            foreach ( $this->form['results'] as $post ) {
                $this->map_locations[] = $this->get_map_location( $post, false );
            }
        }

        return $this->form['results'];
    }

    /**
     * generate the location data.
     */
    public function the_post( $post ) {

        $post = parent::the_location( $post->ID, $post );

        return $post;
    }

    /**
     * Remove the_post action hook when form completed 
     * 
     * @param  [type] $form [description]
     * @return [type]       [description]
     */
    public function remove_the_post( $form ) {

        if ( $this->form['ID'] == $form['ID'] ) {
            remove_action( 'the_post', array( $this, 'the_post' ), 5 );
        }
    }
}
?>