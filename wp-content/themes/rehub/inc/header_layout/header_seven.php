<!-- Logo section -->
<div class="logo_section_wrap<?php if (rehub_option('rehub_logo_inmenu') !='') {echo ' hideontablet';}?>">
    <div class="rh-container">
        <div class="logo-section rh-flex-center-align tabletblockdisplay header_seven_style clearfix">
            <div class="logo">
          		<?php if(rehub_option('rehub_logo')) : ?>
          			<a href="<?php echo home_url(); ?>" class="logo_image"><img src="<?php echo rehub_option('rehub_logo'); ?>" alt="<?php bloginfo( 'name' ); ?>" height="<?php echo rehub_option( 'rehub_logo_retina_height' ); ?>" width="<?php echo rehub_option( 'rehub_logo_retina_width' ); ?>" /></a>
          		<?php elseif (rehub_option('rehub_text_logo')) : ?>
                <div class="textlogo"><?php echo rehub_option('rehub_text_logo'); ?></div>
                <div class="sloganlogo">
                    <?php if(rehub_option('rehub_text_slogan')) : ?><?php echo rehub_option('rehub_text_slogan'); ?><?php else : ?><?php bloginfo( 'description' ); ?><?php endif; ?>
                </div> 
                <?php else : ?>
          			<div class="textlogo"><?php bloginfo( 'name' ); ?></div>
                    <div class="sloganlogo"><?php bloginfo( 'description' ); ?></div>
          		<?php endif; ?>       
            </div>                       
            <div class="search head_search"><?php get_search_form(); ?></div>
            <div class=" rh-flex-right-align">
                <div class="header-actions-logo rh-flex-right-align">
                    <div class="tabledisplay">
                        <?php if(rehub_option('header_seven_more_element') != '') : ?>
                            <?php $custom_element = rehub_option('header_seven_more_element'); ?>
                            <div class="celldisplay link-add-cell">
                                <?php echo do_shortcode($custom_element);?>
                            </div>
                        <?php endif; ?> 
                        <?php if(rehub_option('header_seven_login') != ''):?>
                            <div class="celldisplay login-btn-cell">
                                <?php $loginurl = (rehub_option('custom_login_url')) ? esc_url(rehub_option('custom_login_url')) : '';?>
                                <?php $classmenu = 'rh-header-icon rh_login_icon_n_btn mobileinmenu floatright ';?>
                                <?php echo wpsm_user_modal_shortcode(array('class' =>$classmenu, 'loginurl'=>$loginurl, 'icon'=> 'fal fa-user-alt'));?>                   
                            </div>                            
                        <?php endif;?> 
                        <?php if(rehub_option('header_seven_wishlist') != ''):?>
                            <div class="celldisplay">
                            <a href="<?php echo esc_url(rehub_option('header_seven_wishlist'));?>" class="rh-header-icon mobileinmenu rh-wishlistmenu-link">
                                <?php  
                                    $likedposts = '';       
                                    if ( is_user_logged_in() ) { // user is logged in
                                        global $current_user;
                                        $user_id = $current_user->ID; // current user
                                        $likedposts = get_user_meta( $user_id, "_wished_posts", true);
                                    }
                                    else{
                                        $ip = rehub_get_ip(); // user IP address
                                        $likedposts = get_transient('re_guest_wishes_' . $ip);
                                    } 

                                    $wishnotice = (!empty($likedposts)) ? '<span class="rh-icon-notice">'.count($likedposts).'</span>' : '<span class="rh-icon-notice rhhidden"></span>';
                                ?>
                                <span class="fal fa-heart position-relative">
                                    <?php echo $wishnotice;?>
                                </span>
                            </a>
                            </div>
                        <?php endif;?>                                                           
                        <?php if(rehub_option('header_seven_compare_btn') != ''):?>
                            <div class="celldisplay mobileinmenu rh-comparemenu-link rh-header-icon">
                            <?php echo rh_compare_icon(array());?>
                            </div>
                        <?php endif;?>
                        <?php 
                        if (rehub_option('header_seven_cart') != ''){
                            global $woocommerce;
                            if ($woocommerce){
                            echo '<div class="celldisplay rh_woocartmenu_cell"><a class="rh-header-icon rh-flex-center-align rh_woocartmenu-link cart-contents cart_count_'.$woocommerce->cart->cart_contents_count.'" href="'.wc_get_cart_url().'"><span class="rh_woocartmenu-icon"><span class="rh-icon-notice">'.$woocommerce->cart->cart_contents_count.'</span></span><span class="rh_woocartmenu-amount">'.$woocommerce->cart->get_cart_total().'</span></a></div>';
                            }                            
                        }?>                        
                    </div>                     
                </div>  
            </div>                        
        </div>
    </div>
</div>
<!-- /Logo section -->  
<!-- Main Navigation -->
<div class="search-form-inheader header_icons_menu main-nav<?php if (rehub_option('rehub_logo_inmenu') !='') {echo ' mob-logo-enabled';}?><?php if (rehub_option('rehub_sticky_nav') !=''){echo ' rh-stickme';}?><?php echo $header_menuline_style;?>">  
    <div class="rh-container<?php if (rehub_option('rehub_sticky_nav') && rehub_option('rehub_logo_sticky_url') !=''){echo ' rh-flex-center-align logo_insticky_enabled';}?>"> 
	    <?php 
	        if (rehub_option('rehub_sticky_nav') && rehub_option('rehub_logo_sticky_url') !='') {
	            echo '<a href="'.get_home_url().'" class="logo_image_insticky"><img src="'.rehub_option('rehub_logo_sticky_url').'" alt="'.get_bloginfo( "name" ).'" /></a>';                
	        }             
	    ?>    
        <?php wp_nav_menu( array( 'container_class' => 'top_menu', 'container' => 'nav', 'theme_location' => 'primary-menu', 'fallback_cb' => 'add_menu_for_blank', 'walker' => new Rehub_Walker ) ); ?>
        <div class="responsive_nav_wrap"><?php do_action('rh_mobile_menu_panel'); ?></div>
        <div class="search-header-contents"><?php get_search_form() ?></div>
    </div>
</div>
<!-- /Main Navigation -->