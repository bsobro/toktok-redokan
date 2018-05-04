<!-- Logo section -->
<div class="<?php if (rehub_option('rehub_sticky_nav') !=''){echo 'rh-stickme ';}?>logo_section_wrap header_one_row header_nine_style">
    <div class="rh-container">
        <div class="logo-section rh-flex-center-align rh-flex-columns tabletblockdisplay"> 

            <div class="main-nav flex-3col-1 rh-flex-right-align<?php echo $header_menuline_style;?>">      
                <?php wp_nav_menu( array( 'container_class' => 'top_menu', 'container' => 'nav', 'theme_location' => 'primary-menu', 'fallback_cb' => 'add_menu_for_blank', 'walker' => new Rehub_Walker ) ); ?>
                <div class="responsive_nav_wrap"><?php do_action('rh_mobile_menu_panel'); ?></div>
                <div class="search-header-contents"><?php get_search_form() ?></div>
            </div>

            <div class="logo hideontablet flex-3col-2">
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

            <div class="flex-3col-3">
                <div class="header-actions-logo floatright">
                    <div class="tabledisplay">
                        <?php if(rehub_option('header_nine_more_element') != '') : ?>
                            <?php $custom_element = rehub_option('header_nine_more_element'); ?>
                            <div class="celldisplay link-add-cell">
                                <?php echo do_shortcode($custom_element);?>
                            </div>
                        <?php endif; ?>                                    
                        <div class="celldisplay login-btn-cell">
                            <?php $loginurl = (rehub_option('custom_login_url')) ? esc_url(rehub_option('custom_login_url')) : '';?>
                            <?php $classmenu = 'rh-header-icon rh_login_icon_n_btn mobileinmenu floatright ';?>
                            <?php echo wpsm_user_modal_shortcode(array('class' =>$classmenu, 'loginurl'=>$loginurl, 'icon'=> 'fal fa-user-alt'));?>                   
                        </div>   
                        <?php 
                            echo '<div class="celldisplay mobileinmenu rh-comparemenu-link rh-header-icon">';
                            echo rh_compare_icon(array());
                            echo '</div>';                        
                            global $woocommerce;
                            if ($woocommerce){
                            echo '<div class="celldisplay rh_woocartmenu_cell"><a class="rh-header-icon rh-flex-center-align rh_woocartmenu-link cart-contents cart_count_'.$woocommerce->cart->cart_contents_count.'" href="'.wc_get_cart_url().'"><span class="rh_woocartmenu-icon"><span class="rh-icon-notice">'.$woocommerce->cart->cart_contents_count.'</span></span><span class="rh_woocartmenu-amount">'.$woocommerce->cart->get_cart_total().'</span></a></div>';
                            }                              
                        ?>
                    </div>                     
                </div>  
            </div>                        
        </div>
    </div>
</div>
<!-- /Logo section -->  