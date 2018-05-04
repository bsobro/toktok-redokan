<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php

if( !function_exists('rehub_login_form') ) {
function rehub_login_form( $login_only  = 0 ) {
    global $user_ID, $user_identity, $user_level;
    
    if ( $user_ID ) : ?>
        <?php if( empty( $login_only ) ): ?>
        <div id="user-login">
            <p class="welcome-frase"><?php _e( 'Welcome' , 'rehub_framework' ) ?> <strong><?php echo $user_identity ?></strong></p>
            <span class="author-avatar"><?php echo get_avatar( $user_ID, $size = '60'); ?></span>
            <ul>
                <li><a href="<?php echo home_url() ?>/wp-admin/"><?php _e( 'Dashboard' , 'rehub_framework' ) ?> </a></li>
                <li><a href="<?php echo home_url() ?>/wp-admin/profile.php"><?php _e( 'Your Profile' , 'rehub_framework' ) ?> </a></li>
                <li><a href="<?php echo wp_logout_url(); ?>"><?php _e( 'Logout' , 'rehub_framework' ) ?> </a></li>
            </ul>
            <div class="clear"></div>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <div id="login-form">
            <form action="<?php echo home_url() ?>/wp-login.php" method="post">
                <p id="log-username"><input type="text" class="def_inp" name="log" id="log" value="<?php _e( 'Username' , 'rehub_framework' ) ?>" onfocus="if (this.value == '<?php _e( 'Username' , 'rehub_framework' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Username' , 'rehub_framework' ) ?>';}"  size="33" /></p>
                <p id="log-pass"><input type="password" class="def_inp" name="pwd" id="pwd" value="<?php _e( 'Password' , 'rehub_framework' ) ?>" onfocus="if (this.value == '<?php _e( 'Password' , 'rehub_framework' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Password' , 'rehub_framework' ) ?>';}" size="33" /></p>
                <input type="submit" name="submit" value="<?php _e( 'Log in' , 'rehub_framework' ) ?>" class="def_btn sys_btn" />
                <label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> <?php _e( 'Remember Me' , 'rehub_framework' ) ?></label>
                <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
            </form>
            <ul class="login-links">
                <?php if ( get_option('users_can_register') ) : ?><?php echo wp_register() ?><?php endif; ?>
                <li><a href="<?php echo home_url() ?>/wp-login.php?action=lostpassword"><?php _e( 'Lost your password?' , 'rehub_framework' ) ?></a></li>
            </ul>
        </div>
    <?php endif;
}
}

//////////////////////////////////////////////////////////////////
// AUTHOR SOCIAL LINKS
//////////////////////////////////////////////////////////////////
function rehub_contactmethods( $contactmethods ) {

    $contactmethods['twitter']   = __('Url of Twitter page', 'rehub_framework');
    $contactmethods['facebook']  = __('Url of Facebook page', 'rehub_framework');
    $contactmethods['google']    = __('Url of Google Plus page', 'rehub_framework');
    $contactmethods['tumblr']    = __('Url of Tumblr page', 'rehub_framework');
    $contactmethods['instagram'] = __('Url of Instagram page', 'rehub_framework');
    $contactmethods['vkontakte'] = __('Url of Vk.com page', 'rehub_framework');
    $contactmethods['youtube'] = __('Url of Youtube page', 'rehub_framework');

    return $contactmethods;
}
add_filter('user_contactmethods','rehub_contactmethods',10,1);

/**
 * Display Users Badges
 * Will echo all badge images a given user has earned.
 * @since 1.5
 * @version 1.2
 */
if ( ! function_exists( 'rh_mycred_display_users_badges' ) ) :
    function rh_mycred_display_users_badges( $user_id = NULL ) {

        if ( $user_id === NULL || $user_id == 0 ) return;
        if (!function_exists('mycred_get_users_badges')) return;
        $users_badges = mycred_get_users_badges( $user_id );
        if ( ! empty( $users_badges ) ) {
        do_action( 'mycred_before_users_badges', $user_id, $users_badges );
        echo '<div class="rh_mycred-users-badges">';
            foreach ( $users_badges as $badge_id => $level ) {
                $badge = mycred_get_badge( $badge_id, $level );
                if ( $badge->level_image !== false ) {
                    echo apply_filters( 'mycred_the_badge', $badge->level_image, $badge_id, $badge, $user_id );
                }
            }
        echo '</div>';
        do_action( 'mycred_after_users_badges', $user_id, $users_badges );
        }   
    }
endif;

if(!function_exists('rh_author_detail_box')){
    function rh_author_detail_box (){
        ?>
        <?php 
            $author_ID = get_the_author_meta('ID');
            $count_likes = ( get_user_meta( $author_ID, 'overall_post_likes', true) ) ? get_user_meta( $author_ID, 'overall_post_likes', true) : '0';
            if(function_exists('mycred_get_users_rank')){
                if(rehub_option('rh_mycred_custom_points')){
                    $custompoint = rehub_option('rh_mycred_custom_points');
                    $mycredrank = mycred_get_users_rank($author_ID, $custompoint );
                }
                else{
                    $mycredrank = mycred_get_users_rank($author_ID);        
                }
            }
            if(function_exists('mycred_display_users_total_balance')){
                if(rehub_option('rh_mycred_custom_points')){
                    $custompoint = rehub_option('rh_mycred_custom_points');
                    $mycredpoint = mycred_render_shortcode_my_balance(array('type'=>$custompoint, 'user_id'=>$author_ID, 'wrapper'=>'', 'balance_el' => '') );
                }
                else{
                    $mycredpoint = mycred_render_shortcode_my_balance(array('user_id'=>$author_ID, 'wrapper'=>'', 'balance_el' => '') );           
                }
            }           
        ?>
            <div class="author_detail_box clearfix"><?php echo get_avatar( get_the_author_meta('email'), '69' ); ?>
                <div class="clearfix">
                    <?php if ( function_exists('bp_core_get_user_domain') ) : ?>
                        <a href="<?php echo bp_core_get_user_domain( $author_ID ); ?>" class="see_full_profile_btn"><?php _e( 'Show full profile', 'rehub_framework' ); ?></a>
                    <?php endif; ?>                
                    <h4>
                        <?php the_author_posts_link(); ?>
                        <?php   
                            if (function_exists('bp_get_member_type')){     
                                $membertype = bp_get_member_type($author_ID);
                                $membertype_object = bp_get_member_type_object($membertype);
                                $membertype_label = (!empty($membertype_object) && is_object($membertype_object)) ? $membertype_object->labels['singular_name'] : '';
                                if($membertype_label){
                                    echo '<span class="rh-user-rank-mc rh-user-rank-'.$membertype.'">'.$membertype_label.'</span>';
                                }
                            }
                        ?>                        
                    </h4>
                    <div class="social_icon small_i">
                        <div class="comm_meta_cred">
                            <?php if ( function_exists( 'mycred_get_users_badges' ) && $author_ID !=0 ) : ?>
                                <?php rh_mycred_display_users_badges( $author_ID ) ?>
                            <?php endif; ?>
                            <?php if (!empty($mycredpoint)) :?><i class="far fa-star"></i> <?php echo $mycredpoint; ?><?php endif;?>
                        </div>                     
                        <?php if(get_the_author_meta('user_url')) : ?><a href="<?php the_author_meta('user_url'); ?>" class="author-social hm" rel="nofollow"><i class="far fa-home"></i></a><?php endif; ?>
                        <?php if(get_the_author_meta('facebook')) : ?><a href="<?php the_author_meta('facebook'); ?>" class="author-social fb" rel="nofollow"><i class="fab fa-facebook"></i></a><?php endif; ?>
                        <?php if(get_the_author_meta('twitter')) : ?><a href="<?php the_author_meta('twitter'); ?>" class="author-social tw" rel="nofollow"><i class="fab fa-twitter"></i></a><?php endif; ?>
                        <?php if(get_the_author_meta('google')) : ?><a href="<?php the_author_meta('google'); ?>?rel=author" class="author-social gp" rel="nofollow"><i class="fab fa-google-plus"></i></a><?php endif; ?>
                        <?php if(get_the_author_meta('tumblr')) : ?><a href="<?php the_author_meta('tumblr'); ?>" class="author-social tm" rel="nofollow"><i class="fab fa-tumblr"></i></a><?php endif; ?>
                        <?php if(get_the_author_meta('instagram')) : ?><a href="<?php the_author_meta('instagram'); ?>" class="author-social ins" rel="nofollow"><i class="fab fa-instagram"></i></a><?php endif; ?>
                        <?php if(get_the_author_meta('vkontakte')) : ?><a href="<?php the_author_meta('vkontakte'); ?>" class="author-social vk" rel="nofollow"><i class="fab fa-vk"></i></a><?php endif; ?>
                        <?php if(get_the_author_meta('youtube')) : ?><a href="<?php the_author_meta('youtube'); ?>" class="author-social yt" rel="nofollow"><i class="fab fa-youtube"></i></a><?php endif; ?>
                     </div>
                    <?php if (get_the_author_meta('description') !='') :?><p><?php the_author_meta('description'); ?></p><?php endif;?>
                    <p>
                </div>
            </div>
        <?php
    }
    add_shortcode('rh_author_detail_box', 'rh_author_detail_box');
}

/* Redirect from author profile to BP */
if(rehub_option('bp_redirect') =='1'){
    add_action( 'template_redirect', 'rh_redirect_author_archive_to_profile' ); 
}
function rh_redirect_author_archive_to_profile() {
  if(is_author()){
    $user_id = get_query_var( 'author' );
    if (function_exists('bp_core_get_user_domain')) {
        wp_redirect( bp_core_get_user_domain( $user_id ) );       
    }
  }
}

/* Add FontAwesome icons to WP social login */
if( !function_exists('wslrehub_use_fontawesome_icons') ) {
function wslrehub_use_fontawesome_icons( $provider_id, $provider_name, $authenticate_url ){ ?>
   <a rel="nofollow" href="<?php echo $authenticate_url; ?>" data-provider="<?php echo $provider_id ?>" class="wp-social-login-provider wp-social-login-provider-<?php echo strtolower( $provider_id ); ?>">
      <span>
         <i class="fab fa-<?php echo strtolower( $provider_id ); ?>"></i>
         <?php echo $provider_name; ?>
      </span>
   </a><?php
}
add_filter( 'wsl_render_auth_widget_alter_provider_icon_markup', 'wslrehub_use_fontawesome_icons', 10, 3 ); 
}

/* Remove admin bar from users */
if( !function_exists('rh_remove_admin_bar') ) {
function rh_remove_admin_bar() {
    if(!current_user_can('administrator') && !is_admin()){
        show_admin_bar(false);
    }
}
}
if (rehub_option('remove_admin_bar') =='1'){
    add_action('after_setup_theme', 'rh_remove_admin_bar');    
}


if ( function_exists( 'mycred' ) ) {
    /**
     * Register Hook
     */
    add_filter( 'mycred_setup_hooks', 'mycred_register_overall_post_likes_hook', 120 );
    function mycred_register_overall_post_likes_hook( $installed ) {
        if ( ! function_exists( 'hot_count' ) ) return $installed;
        $installed['overallpostlikes'] = array(
            'title' => __( 'Hot Meter & Thumbs Likes', 'rehub_framework' ),
            'description' => __( 'Awards %_plural% to Author for post likes via the Hot Meter.', 'rehub_framework' ),
            'callback' => array( 'myCRED_Hook_Overall_Post_Likes' )
        );
        return $installed;
    }

    add_filter( 'mycred_all_references', 'add_overall_post_likes_references' );
    function add_overall_post_likes_references( $references ) {      
        $references['ref_overall_post_likes'] = __( 'Hot Meter & Thumbs Likes', 'rehub_framework' );
        return $references;
    }

    /**
     * Overall Post Likes Hook
     */
    add_action( 'mycred_load_hooks', 'mycred_load_overall_post_likes_hook', 120 );
    function mycred_load_overall_post_likes_hook() {
        // If the hook has been replaced or if plugin is not installed, exit now
        if ( class_exists( 'myCRED_Hook_Overall_Post_Likes' ) || ! function_exists( 'hot_count' ) ) return;
        class myCRED_Hook_Overall_Post_Likes extends myCRED_Hook {
            /**
             * Construct
             */
            function __construct( $hook_prefs, $type = 'mycred_default' ) {
                parent::__construct( array(
                    'id' => 'overallpostlikes',
                    'defaults' => array(
                        'added' => array(
                            'creds' => '1',
                            'log'   => '%plural% for added a post like',
                            'limit' => '0/x'
                        ),
                        'removed' => array(
                            'creds' => '-1',
                            'log'   => '%plural% deduction for removed a post like'
                        ),
                    )
                ), $hook_prefs, $type );
            }

            /**
             * Run
             */
            public function run() {               
                //add_action( 'rh_overall_post_likes_add', array( $this, 'add_post_likes' ) );
                // add_action( 'rh_overall_post_likes_remove', array( $this, 'remove_post_likes' ) );
                add_action( 'rh_overall_post_likes_add', array( $this, 'get_post_likes_ajax' ) );
            }

            /**
             * Get Ajax Data
             */
            public function get_post_likes_ajax() {
                $post_id = intval( $_POST['post_id'] );            
                if ( $post_id && $_POST['hot_count'] == 'hot' )
                    $this->add_post_likes( $post_id );
                if ( $post_id && $_POST['hot_count'] == 'cold' )
                    $this->remove_post_likes( $post_id );
            }
            
            /**
             * Added Like
             */
            public function add_post_likes( $post_id ) {
                $post = get_post( $post_id );
                $user_id = get_current_user_id();
                if ( $user_id != $post->post_author ) {
                    // Award post author for being added like to his post
                    if ( $this->prefs['added']['creds'] != 0 && ! $this->core->exclude_user( $post->post_author ) ) {
                        // Limit
                        if ( ! $this->over_hook_limit( 'added', 'ref_overall_post_likes', $post->post_author ) ) {
                            // Execute
                            $this->core->add_creds(
                                'ref_overall_post_likes',
                                $post->post_author,
                                $this->prefs['added']['creds'],
                                $this->prefs['added']['log'],
                                $post_id,
                                array( 'ref_type' => 'post', 'by' => $user_id ),
                                $this->mycred_type
                            );
                        }
                    }
                }
            }

            /**
             * Removed Like
             */
            public function remove_post_likes( $post_id ) {
                $post = get_post( $post_id );
                $user_id = get_current_user_id();
                if ( $user_id != $post->post_author ) {
                    if ( $this->prefs['removed']['creds'] != 0 && ! $this->core->exclude_user( $post->post_author ) ) {
                        $this->core->add_creds(
                            'ref_overall_post_likes',
                            $post->post_author,
                            $this->prefs['removed']['creds'],
                            $this->prefs['removed']['log'],
                            $post_id,
                            array( 'ref_type' => 'post', 'by' => $user_id ),
                            $this->mycred_type
                        );
                    }
                }
            }

            /**
             * Preferences for Post Likes
             */
            public function preferences() {
            $prefs = $this->prefs;
            ?>
                <label class="subheader" for="<?php echo $this->field_id( array( 'added' => 'creds' ) ); ?>"><?php _e( 'Author Content is liked', 'rehub_framework' ); ?></label>
                <ol>
                    <li>
                        <div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'added' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'added' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['added']['creds'] ); ?>" size="8" /></div>
                    </li>
                    <li>
                        <label for="<?php echo $this->field_id( array( 'added' => 'limit' ) ); ?>"><?php _e( 'Limit', 'rehub_framework' ); ?></label>
                        <?php echo $this->hook_limit_setting( $this->field_name( array( 'added' => 'limit' ) ), $this->field_id( array( 'added' => 'limit' ) ), $prefs['added']['limit'] ); ?>
                    </li>
                </ol>
                <label class="subheader" for="<?php echo $this->field_id( array( 'added' => 'log' ) ); ?>"><?php _e( 'Log Template', 'rehub_framework' ); ?></label>
                <ol>
                    <li>
                        <div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'added' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'added' => 'log' ) ); ?>" value="<?php echo esc_attr( $prefs['added']['log'] ); ?>" class="long" /></div>
                        <span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
                    </li>
                </ol>

                <label class="subheader" for="<?php echo $this->field_id( array( 'removed' => 'creds' ) ); ?>"><?php _e( 'Author Content is disliked', 'rehub_framework' ); ?></label>
                <ol>
                    <li>
                        <div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'removed' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'removed' => 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['removed']['creds'] ); ?>" size="8" /></div>
                    </li>
                </ol>
                <label class="subheader" for="<?php echo $this->field_id( array( 'removed' => 'log' ) ); ?>"><?php _e( 'Log Template', 'rehub_framework' ); ?></label>
                <ol>
                    <li>
                        <div class="h2"><input type="text" name="<?php echo $this->field_name( array( 'removed' => 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'removed' => 'log' ) ); ?>" value="<?php echo esc_attr( $prefs['removed']['log'] ); ?>" class="long" /></div>
                        <span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
                    </li>
                </ol>
            <?php
            }
            
            /**
             * Sanitise Preferences
             */
            function sanitise_preferences( $data ) {

                if ( isset( $data['added']['limit'] ) && isset( $data['added']['limit_by'] ) ) {
                    $limit = sanitize_text_field( $data['added']['limit'] );
                    if ( $limit == '' ) $limit = 0;
                    $data['added']['limit'] = $limit . '/' . $data['added']['limit_by'];
                    unset( $data['added']['limit_by'] );
                }

                return $data;
            }
        }
    }
}

//FUNCTIONS FOR GEO LOCATOR. USE IT WITH GEO MY WP PLUGIN

// Pop-up info window on the map
if (!function_exists('rh_gmw_vendor_in_popup')){
    function rh_gmw_vendor_in_popup ($output, $location, $args, $gmw){
        $userid = $location->ID;
        if (defined('wcv_plugin_dir')) {
            $avatar = WCV_Vendors::is_vendor($userid) ? '<img src='.rh_show_vendor_avatar($userid, 120, 120).' />' : bp_core_fetch_avatar ( array( 'item_id' => $userid, 'type' => 'full' ) );
            $link = WCV_Vendors::is_vendor($userid) ? WCV_Vendors::get_vendor_shop_page($userid) : bp_get_member_permalink();
            $name = WCV_Vendors::is_vendor($userid) ? WCV_Vendors::get_vendor_sold_by( $userid ) : $location->display_name;
        }
        elseif ( class_exists( 'WeDevs_Dokan' ) ){
            $is_vendor = dokan_is_user_seller( $userid);
            $avatar = $is_vendor ? '<img src='.rh_show_vendor_avatar($userid, 120, 120).' />' : bp_core_fetch_avatar ( array( 'item_id' => $userid, 'type' => 'full' ) );
            $link = $is_vendor ? dokan_get_store_url($userid) : bp_core_get_user_domain($userid);
            $name = $is_vendor ? get_user_meta( $userid, 'dokan_store_name', true ) : $location->display_name;
        }
        elseif (class_exists('WCMp')){
            $is_vendor = is_user_wcmp_vendor( $userid );
            $avatar = $is_vendor ? '<img src='.rh_show_vendor_avatar($userid, 120, 120).' />' : bp_core_fetch_avatar ( array( 'item_id' => $userid, 'type' => 'full' ) );
            if($is_vendor){
                $vendorobj = get_wcmp_vendor($userid);
                $link = $vendorobj->permalink;
                $name = get_user_meta($userid, '_vendor_page_title', true);                 
            } 
            else{
                $link = bp_get_member_permalink();
                $name = $location->display_name;                
            }           
        }
        else {
            $avatar = bp_get_member_avatar($args= 'type=full');
            $link = bp_get_member_permalink();
            $name = $location->display_name;
        }

        $args_fields = (!empty($args["address_fields"])) ? $args["address_fields"] : '';
     
        $output                  = array();
        $output['wrap']         = '<div class="gmw-fl-infow-window-wrapper wppl-fl-info-window">';
        $output['image']         = '<div class="thumb wppl-info-window-thumb">'.$avatar.'</div>';
        $output['content_start'] = '<div class="content wppl-info-window-info"><table>';
        $output['title']          = '<tr><td><span class="wppl-info-window-permalink"><a href="'.esc_url($link).'">'.esc_attr($name).'</a></span></td></tr>';
        if($args_fields){
            $output['address_fields']       = '<tr><td><span class="address gmw-icon-location">'.gmw_get_location_address( $location, $args["address_fields"], $gmw ).'</span></td></tr>';
        }
        
        if ( !empty($args['distance'] ) && isset( $location->distance ) ) {
            $output['distance'] = '<tr><td><span class="distance">'. esc_attr( $location->distance ) . ' ' .$location->units.'</td></tr>';
        }
         
        $output['content_end']  = '</table></div>';
        $output['/wrap']          = '</div>';
        return $output;
    }
}
add_filter( 'gmw_fl_info_window_content', 'rh_gmw_vendor_in_popup', 10, 4);

// Replace the default location pin for vendor / client location - ext. Members (Friends) Locator
if (!function_exists('rh_gmw_vendor_mapin')){
    function rh_gmw_vendor_mapin ($map_icon, $location){
        $user_id = $location->ID;
        $map_icon = get_template_directory_uri() . '/images/default/mapuserpin.png'; 
        $map_vendor_icon = get_template_directory_uri() . '/images/default/mapvendorpin.png';  
        if(defined('wcv_plugin_dir')) {
            $is_vendor = WCV_Vendors::is_vendor($user_id);
        }elseif(class_exists('WeDevs_Dokan')){
            $is_vendor = dokan_is_user_seller($user_id);
        }elseif(class_exists('WCMp')){
            $is_vendor = is_user_wcmp_vendor($user_id);
        }
        if($is_vendor)
            return $map_vendor_icon;
        return $map_icon;
    }
}
add_filter( 'gmw_fl_map_icon', 'rh_gmw_vendor_mapin', 10, 2);


// Replace the default location pin for group location - ext. Groups Locator
if (!function_exists('rh_gmwgl_vendor_mapin')){
    function rh_gmwgl_vendor_mapin ($member, $gmw_form){
        return get_template_directory_uri() . '/images/default/mappostpin.png';               
    }
}
add_filter( 'gmw_gl_map_icon', 'rh_gmwgl_vendor_mapin', 10, 2);


// GMW Function - Update post, product location base on user location
function rh_gmw_friends_pass_map_data( $post_id, $post ) {
    $user_id = $post->post_author;
    if (function_exists('gmw_get_location')){        
        $gmw_member_info = gmw_get_location('user', $user_id);
        if (!empty( $gmw_member_info ) && is_object($gmw_member_info)){
            $address = $gmw_member_info->address;
            if(!empty($address) && function_exists('gmw_update_post_location')) {
                gmw_update_post_location($post_id, stripslashes($address), $user_id, true);
            }            
        }
    }
}
if(rehub_option('post_sync_with_user_location') == 1){
    add_action( 'publish_post', 'rh_gmw_friends_pass_map_data', 10, 2 );
    add_action( 'publish_product', 'rh_gmw_friends_pass_map_data', 10, 2 );    
}
/* END GEO MY WP PLUGIN HOOKS*/


function disable_s2_member_in_rehub($redirect=true){
    if(defined('DOING_AJAX') && DOING_AJAX){
        return false;
    }
    else{
        return $redirect;
    }
}
add_filter('ws_plugin__s2member_login_redirect', 'disable_s2_member_in_rehub');

//Automatically assign vendor role to new roles of user
if (rehub_option('rh_sync_role') != ''){
    $data = rehub_option('rh_sync_role');
    $data = explode(':', $data);
    if(!empty($data[0]) && !empty($data[1]) && !empty($data[2])){
        add_action( 'set_user_role', 'assign_to_rhcustom_role', 30, 3 );
        function assign_to_rhcustom_role( $user_id, $new_role, $old_roles ) {
            $data = rehub_option('rh_sync_role');
            $data = explode(':', $data);            
            $wp_user_object = new WP_User($user_id);
            $vendor_role   = $data[0];
            $roles_remove = array_map('trim', explode(",", $data[1]));          
            $roles_add = array_map('trim', explode(",", $data[2]));
            if ( in_array($new_role, $roles_remove) ) {
                $wp_user_object->remove_role( $vendor_role ); 
            }
            elseif ( in_array($new_role, $roles_add) ) {
                $wp_user_object->add_role( $vendor_role ); 
            }
            else {
                return;
            }
        }       
    }
}

if (rehub_option('rh_award_role_mycred') != ''){
    add_filter( 'mycred_add_finished', 'rh_award_new_role_mycred', 99, 3 );
    function rh_award_new_role_mycred( $reply, $request, $mycred ) {
        // Make sure that if any other filter has declined this we also decline
        if ( $reply === false ) return $reply;

        // Exclude admins
        if ( user_can( $request['user_id'], 'manage_options' ) ) return $reply;

        extract( $request );

        $rolechangedarray = rehub_option('rh_award_role_mycred');

        $rolechangedarray = explode(PHP_EOL, $rolechangedarray);
        $thresholds = array();

        foreach ($rolechangedarray as $key => $value) {
            $values = explode(':', $value);
            if (empty($values[0]) || empty($values[1])) return;
            $roleforchange = trim($values[0]);
            $numberforchange = trim($values[1]);            
            $thresholds[$roleforchange] = (int)$numberforchange;
        }

        // Get users current balance
        $current_balance = $mycred->get_users_balance( $user_id, $type );
        $current_balance = (int)$current_balance + (int)$amount;

        // Check if the users current balance awards a new role
        $new_role = false;
        foreach ( $thresholds as $role => $min ) {
            if ( $current_balance >= $min )
                $new_role = $role;
        }

        // Change users role if we have one
        if ( $new_role !== false ){
            if(rehub_option('rh_award_type_mycred') ==1 && function_exists('bp_get_member_type')){
                $roles = bp_get_member_type($user_id, false);
                if(!empty($roles) && is_array($roles)){
                    if (!in_array( $new_role, (array) $roles)){
                        bp_set_member_type( $user_id, $new_role );
                    }                     
                }else{
                    bp_set_member_type( $user_id, $new_role );
                } 
            }else{
                $wp_user_object = new WP_User($user_id);
                if(empty($wp_user_object)) return;
                if (!in_array( $new_role, (array) $wp_user_object->roles )){
                    $wp_user_object->add_role($new_role);
                }                
            }
        }
        return $reply;
    }
}

if (!function_exists('rh_bp_show_vendor_in_loop')){
    function rh_bp_show_vendor_in_loop ($vendor_id){
        $out = '';
        if (defined('wcv_plugin_dir')){
            if(WCV_Vendors::is_vendor($vendor_id)){
                $out .='<div class="store_member_in_m_loop"><span class="store_member_in_m_loop_l">'.__('Owner of shop:', 'rehub_framework').'</span> ';
                $out .='<a href="'.WCV_Vendors::get_vendor_shop_page( $vendor_id).'" class="store_member_in_m_loop_a">'.get_user_meta( $vendor_id, 'pv_shop_name', true ).'</a>';
                $out .='</div>';                
            }
        }
        elseif ( class_exists( 'WeDevs_Dokan' ) ){
            $sold_by = dokan_is_user_seller( $vendor_id );
            if ($sold_by){
                $store_info = dokan_get_store_info( $vendor_id );
                $out .='<div class="store_member_in_m_loop"><span class="store_member_in_m_loop_l">'.__('Owner of shop:', 'rehub_framework').'</span> ';
                $out .='<a href="'.dokan_get_store_url( $vendor_id ).'" class="store_member_in_m_loop_a">'.esc_html( $store_info['store_name'] ).'</a>';
                $out .='</div>';                
            }
        }
        elseif (class_exists('WCMp')){
            $is_vendor = is_user_wcmp_vendor( $vendor_id );
            if($is_vendor){
                $vendorobj = get_wcmp_vendor($vendor_id);
                $store_url = $vendorobj->permalink;
                $store_name = get_user_meta($vendor_id, '_vendor_page_title', true); 
                $out .='<div class="store_member_in_m_loop"><span class="store_member_in_m_loop_l">'.__('Owner of shop:', 'rehub_framework').'</span> ';
                $out .='<a href="'.$store_url.'" class="store_member_in_m_loop_a">'.esc_html($store_name ).'</a>';
                $out .='</div>'; 
            }
        }        
        return $out;
    }
}