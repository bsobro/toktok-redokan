<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php global $product; global $post;?>
<?php if (empty( $product ) || ! $product->is_visible() ) {return;}?>
<?php $classes = array('product', 'col_item', 'offer_grid', 'woo_compact_grid', 'mobile_compact_grid', 'offer_act_enabled', 'no_btn_enabled');?>
<?php $woolinktype = (isset($woolinktype)) ? $woolinktype : '';?>
<?php $woolink = ($woolinktype == 'aff' && $product->get_type() =='external') ? $product->add_to_cart_url() : get_post_permalink($post->ID) ;?>
<?php $wootarget = ($woolinktype == 'aff' && $product->get_type() =='external') ? ' target="_blank" rel="nofollow"' : '' ;?>
<?php $offer_coupon_date = get_post_meta( $post->ID, 'rehub_woo_coupon_date', true ) ?>
<?php $custom_img_width = (isset($custom_img_width)) ? $custom_img_width : '';?>
<?php $custom_img_height = (isset($custom_img_height)) ? $custom_img_height : '';?>
<?php $custom_col = (isset($custom_col)) ? $custom_col : '';?>
<?php $coupon_style = $expired = ''; if(!empty($offer_coupon_date)) : ?>
    <?php 
    $timestamp1 = strtotime($offer_coupon_date) + 86399; 
    $seconds = $timestamp1 - (int)current_time('timestamp',0); 
    $days = floor($seconds / 86400);
    $seconds %= 86400;
    if ($days > 0) {
        $coupon_text = $days.' '.__('days left', 'rehub_framework');
        $coupon_style = '';
        $expired = 'no';      
    }
    elseif ($days == 0){
        $coupon_text = __('Last day', 'rehub_framework');
        $coupon_style = '';
        $expired = 'no';
    }
    else {
        $coupon_text = __('Expired', 'rehub_framework');
        $coupon_style = ' expired';
        $expired = '1';
    }                 
    ?>
<?php endif ;?>
<?php do_action('woo_change_expired', $expired); //Here we update our expired?>
<?php $classes[] = $coupon_style;?>
<?php $classes[] = rh_expired_or_not($post->ID, 'class');?>
<?php $syncitem = '';?>
<?php if (rh_is_plugin_active('content-egg/content-egg.php')):?>
    <?php $itemsync = \ContentEgg\application\WooIntegrator::getSyncItem($post->ID);?>
    <?php if(!empty($itemsync)):?>
        <?php                            
            $syncitem = $itemsync;                            
        ?>
    <?php endif;?>
<?php endif;?>
<div <?php post_class( $classes ); ?>>
    
    <div class="info_in_dealgrid">
        <?php  $badge = get_post_meta($post->ID, 'is_editor_choice', true); ?>
        <?php if ($badge !='' && $badge !='0') :?> 
            <?php echo re_badge_create('ribbonleft'); ?>
        <?php elseif ( $product->is_featured() ) : ?>
            <?php echo apply_filters( 'woocommerce_featured_flash', '<span class="re-ribbon-badge left-badge badge_2"><span>' . __( 'Featured!', 'rehub_framework' ) . '</span></span>', $post, $product ); ?>
        <?php endif; ?>         
        <figure class="mb15"> 
            <?php if ( $product->is_on_sale()) : ?>
                <?php 
                $percentage=0;
                if ($product->get_regular_price()) {
                    $percentage = round( ( ( $product->get_regular_price() - $product->get_price() ) / $product->get_regular_price() ) * 100 );
                }
                if ($percentage && $percentage>0  && !$product->is_type( 'variable' )) {
                    $sales_html = apply_filters( 'woocommerce_sale_flash', '<span class="grid_onsale"><span>- ' . $percentage . '%</span></span>', $post, $product );
                } else {
                    $sales_html = apply_filters( 'woocommerce_sale_flash', '<span class="grid_onsale">' . esc_html__( 'Sale!', 'rehub_framework' ) . '</span>', $post, $product );
                }
                ?>
                <?php echo $sales_html; ?>
            <?php endif; ?> 
        
            <a class="img-centered-flex rh-flex-center-align rh-flex-justify-center" href="<?php echo $woolink ;?>"<?php echo $wootarget ;?>>
                <?php 
                $showimg = new WPSM_image_resizer();
                $showimg->use_thumb = true;   
                $showimg->no_thumb = rehub_woocommerce_placeholder_img_src('');                                 
                ?>
                <?php if(isset($custom_col)) : ?>
                    <?php $showimg->width = (int)$custom_img_width;?>
                    <?php $showimg->height = (int)$custom_img_height;?>                                 
                <?php else : ?>
                    <?php $showimg->width = '250';?> 
                    <?php $showimg->height = '180';?>   
                    <?php $showimg->crop = false;?>                                   
                <?php endif ; ?>           
                <?php $showimg->show_resized_image(); ?>
            </a>
        </figure>
        <?php do_action( 'rehub_after_compact_grid_figure' ); ?>
        <div class="grid_desc_and_btn">

            <div class="grid_row_info">
                <div class="flowhidden mb15">
                    <div class="price_for_grid floatleft fontbold">
                        <?php wc_get_template( 'loop/price.php' ); ?>
                        <?php if($syncitem):?>
                            <?php $countoffers = rh_ce_found_total_offers($post->ID);?>
                            <?php if ($countoffers > 1) :?>
                                <a class="font70 greycolor displayblock" href="<?php the_permalink();?>">+ <?php echo $countoffers - 1; ?> <?php _e('more', 'rehub_framework');?></a>
                            <?php endif;?>                             
                        <?php endif ;?>    
                    </div>
                    
                    <div class="floatright vendor_for_grid lineheight15">
                        <?php if($syncitem):?>
                            <div class="aff_tag"> 
                                <?php $celogo = \ContentEgg\application\helpers\TemplateHelper::getMerhantLogoUrl($syncitem, true);?>
                                <?php if($celogo) :?>
                                    <img src="<?php echo $celogo; ?>" alt="<?php echo esc_attr($syncitem['title']); ?>" height="30" />
                                <?php endif ;?>  
                            </div>                                               
                        <?php endif ;?>
                    </div>
                </div>        
            </div> 
            <h3 class="<?php if(rehub_option('wishlist_disable') !='1') :?><?php echo getHotIconclass($post->ID, true); ?><?php endif ;?>"><?php echo rh_expired_or_not($post->ID, 'span');?><a href="<?php echo $woolink ;?>"<?php echo $wootarget ;?>><?php the_title();?></a></h3> 
            <?php wc_get_template( 'loop/rating.php' );?>              
            
        </div>                                       
    </div>
    <?php $loop_code_zone = rehub_option('woo_code_zone_loop');?>        
    <?php if ($loop_code_zone):?>
        <div class="woo_code_zone_loop clearbox">
            <?php echo do_shortcode($loop_code_zone);?>
        </div>
    <?php endif;?>
    <?php if ( isset( $gmw['your_lat'] ) && !empty( $gmw['your_lat'] ) ) { ?>
        <span class="radius-dis">(<?php gmw_distance_to_location( $post, $gmw ); ?>)</span>
        <div class="wppl-address">
            <?php echo $post->address; ?>
        </div>        
    <?php } ?>                                     
    <?php if ( isset( $gmw['search_results']['get_directions'] ) ) { ?>
        <!-- Get directions -->
        <div class="get-directions-link">
            <?php gmw_directions_link( $post, $gmw, $gmw['labels']['search_results']['directions'] ); ?>
        </div>
    <?php } ?>    
    <?php do_action('woocommerce_before_shop_loop_item');?>

    <div class="re_actions_for_grid two_col_btn_for_grid">
        <div class="btn_act_for_grid">
            <?php $wishlistadded = __('Added to wishlist', 'rehub_framework');?>
            <?php $wishlistremoved = __('Removed from wishlist', 'rehub_framework');?>
            <?php echo RH_get_wishlist($post->ID, '', $wishlistadded, $wishlistremoved);?>  
        </div>

        <div class="btn_act_for_grid">
            <?php if(rehub_option('woo_rhcompare') == true) :?>
            <span class="compare_for_grid">            
                <?php $cmp_btn_args = array(); $cmp_btn_args['class']= 'comparecompact';?>
                <?php echo wpsm_comparison_button($cmp_btn_args); ?> 
            </span>
            <?php else:?>
                <span class="comm_number_for_grid"><?php echo get_comments_number(); ?></span>
            <?php endif;?>
        </div>      
    </div> 
    <?php do_action( 'woocommerce_after_shop_loop_item' );?>      
</div>