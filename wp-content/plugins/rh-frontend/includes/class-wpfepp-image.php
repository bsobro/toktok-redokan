<?php
/**
 * WPFEPP Image class.
 *
 * @since 3.5.4
 * @package WPFEPP
 **/
 
 class WPFEPP_Image {
	 
    public static function get_img_file( $img_url, $img_title ) {
        if( empty( $img_url ) )
            return false;
		
		// save image localy
		$file_name = self::save_img_localy( $img_url, $img_title );
		
		if( !$file_name )
			return false;
		
		$uploads = wp_upload_dir();
		$image = ltrim( trailingslashit( $uploads['subdir'] ), '\/' ) . $file_name;
		return self::get_full_img_path( $image );

        return $img_file;
    }
	
    static function save_img_localy( $img_uri, $img_title = '', $check_image_type = true ) {
		require_once plugin_dir_path( __FILE__ ) . 'class-wpfepp-helpers.php';
        $newfilename = WPFEPP_Helpers::truncate( $img_title );
        $newfilename = WPFEPP_Helpers::rus2latin( $newfilename );
        $newfilename = preg_replace( '/[^a-zA-Z0-9\-]/', '', $newfilename );
        $newfilename = strtolower( $newfilename );
        if( !$newfilename )
            $newfilename = time();

        $uploads = wp_upload_dir();
        if( $newfilename = self::download_img( $img_uri, $uploads['path'], $newfilename, null, $check_image_type ) )
            return $newfilename;
        else
            return false;
    }
	
    static function get_full_img_path( $img_path ) {
        $uploads = wp_upload_dir();
        return trailingslashit( $uploads['basedir'] ) . $img_path;
    }
	
    static function download_img( $img_uri, $save_dir, $file_name, $file_ext = null, $check_image_type = true ) {
        $response = wp_remote_get( $img_uri, array( 'timeout' => 5, 'redirection' => 1 ) );
        if( is_wp_error( $response ) || (int) wp_remote_retrieve_response_code( $response ) !== 200 )
            return false;

        if( $file_ext === null ) {
            if( $ext = pathinfo( basename( $img_uri ), PATHINFO_EXTENSION ) ) {
                $file_ext = $ext;
            } else {
                $headers = wp_remote_retrieve_headers( $response );
                if( empty($headers['content-type']) )
                    return false;
                $types = array_search( $headers['content-type'], wp_get_mime_types() );
                if( !$types )
                    return false;
                $exts = explode( '|', $types );
                $file_ext = $exts[0];
            }
        }
        if( $file_ext )
            $file_name .= '.' . $file_ext;

        $file_name = wp_unique_filename( $save_dir, $file_name );

        if( $check_image_type ) {
            $filetype = wp_check_filetype( $file_name, null );
            if( substr($filetype['type'], 0, 5) != 'image' )
                return false;
        }

        $image_string = wp_remote_retrieve_body( $response );
        $file_path = trailingslashit( $save_dir ) . $file_name;
        if( !file_put_contents( $file_path, $image_string ) )
            return false;

        if( $check_image_type && !self::is_image( $file_path ) ) {
            @unlink( $file_path );
            return false;
        }
        if( !defined( 'FS_CHMOD_FILE' ) )
            define( 'FS_CHMOD_FILE', ( fileperms(ABSPATH . 'index.php') & 0777 | 0644 ) );
        @chmod( $file_path, FS_CHMOD_FILE );

        return $file_name;
    }
	
    public static function is_image( $path ) {
        if( !$a = getimagesize( $path ) )
            return false;
        $image_type = $a[2];
        if( in_array( $image_type, array( IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP ) ) )
            return true;
        else
            return false;
    }
}