<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php global $product, $post;?>
<?php $itemsync = $syncitem = $youtubecontent = '';?>
<?php if (rh_is_plugin_active('content-egg/content-egg.php')):?>
    <?php $itemsync = \ContentEgg\application\WooIntegrator::getSyncItem($post->ID);?>
    <?php $unique_id = $module_id = $domain = $merchant = $syncitem = '';?>
    <?php if(!empty($itemsync)):?>
        <?php 
            $unique_id = $itemsync['unique_id']; 
            $module_id = $itemsync['module_id'];
            $domain = $itemsync['domain']; 
            $merchant = $itemsync['merchant'];                            
            $syncitem = $itemsync;                            
        ?>
    <?php endif;?>
    <?php $postid = $post->ID;?>
    <?php $youtubecontent = \ContentEgg\application\components\ContentManager::getViewData('Youtube', $postid);?>
<?php endif;?>

<!-- CONTENT -->
<div class="rh-container"> 
    <div class="rh-content-wrap clearfix">
        <div id="contents-section-woo-area" class="rh-stickysidebar-wrapper">                      
            <div class="ce_woo_auto_sections ce_woo_blocks main-side rh-sticky-container clearfix <?php echo (is_active_sidebar( 'sidebarwooinner' )) ? 'woo_default_w_sidebar' : 'full_width woo_default_no_sidebar'; ?>" id="content">
                <div class="post">
                    <?php do_action( 'woocommerce_before_main_content' );?>
                    <?php if (rh_is_plugin_active('content-egg/content-egg.php')):?>
                        <?php $amazonupdate = get_post_meta($postid, \ContentEgg\application\components\ContentManager::META_PREFIX_LAST_ITEMS_UPDATE.'Amazon', true);?>
                        <div class="floatright pl20">
                            <?php $product_update = \ContentEgg\application\helpers\TemplateHelper::getLastUpdateFormatted('Amazon', $postid);?>
                            <?php if($amazonupdate && $product_update):?>
                                <div class="font60 lineheight20"><?php _e('Last price update was:', 'rehub_framework');?> <?php echo $product_update;?> <span class="csspopuptrigger" data-popup="ceblocks-amazon-disclaimer"><i class="far fa-question-circle greycolor font90"></i></span></div>
                                <div class="csspopup" id="ceblocks-amazon-disclaimer">
                                    <div class="csspopupinner">
                                        <span class="cpopupclose">×</span>
                                        <?php _e('Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on Amazon at the time of purchase will apply to the purchase of this product.', 'rehub_framework');?>
                                    </div>
                                </div>
                            <?php endif;?>                        
                        </div>
                    <?php endif;?>                    
                    <?php woocommerce_breadcrumb();?>

                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php
                            do_action( 'woocommerce_before_single_product' );
                            if ( post_password_required() ) {
                                echo get_the_password_form();
                                return;
                            }
                        ?>     
                        <div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>                         
                            <div class="ce_woo_block_top_holder">
                                <div class="woo_bl_title flowhidden mb10">
                                    <h1 class="floatleft tabletblockdisplay pr20 <?php if(rehub_option('wishlist_disable') !='1') :?><?php echo getHotIconclass($post->ID, true); ?><?php endif ;?>"><?php the_title(); ?></h1>
                                    <div class="woo-top-actions tabletblockdisplay floatright">
                                        <div class="woo-button-actions-area pl5 pb5 pr5">
                                            <?php $wishlistadd = __('Add to wishlist', 'rehub_framework');?>
                                            <?php $wishlistadded = __('Added to wishlist', 'rehub_framework');?>
                                            <?php $wishlistremoved = __('Removed from wishlist', 'rehub_framework');?>
                                            <?php echo RH_get_wishlist($post->ID, $wishlistadd, $wishlistadded, $wishlistremoved);?>  
                                            <?php if(rehub_option('woo_rhcompare') == 1) {echo wpsm_comparison_button(array('class'=>'rhwoosinglecompare'));} ?>                                      
                                        </div> 
                                    </div>                                
                                </div>  
                                <div class="border-grey-bottom clearfix mb15"></div>

                                <div class="wpsm-one-third wpsm-column-first tabletblockdisplay compare-full-images modulo-lightbox mb30">
                                    <?php 
                                        wp_enqueue_script('modulobox');
                                        wp_enqueue_style('modulobox');
                                    ?>                                                         
                                    <figure class="text-center">
                                        <?php  $badge = get_post_meta($post->ID, 'is_editor_choice', true); ?>
                                        <?php if ($badge !='' && $badge !='0') :?> 
                                            <?php echo re_badge_create('ribbon'); ?>
                                        <?php else:?>                                        
                                            <?php woocommerce_show_product_sale_flash();?>
                                        <?php endif;?>
                                        <?php           
                                            $image_id = get_post_thumbnail_id($post->ID);  
                                            $image_url = wp_get_attachment_image_src($image_id,'full');
                                            $image_url = $image_url[0]; 
                                        ?> 
                                        <a data-rel="rh_top_gallery" href="<?php echo $image_url;?>" target="_blank" data-thumb="<?php echo $image_url;?>">            
                                            <?php WPSM_image_resizer::show_static_resized_image(array('lazy'=>true, 'thumb'=> true, 'crop'=> false, 'height'=> 350, 'no_thumb_url' => get_template_directory_uri() . '/images/default/noimage_500_500.png'));?>
                                        </a>
                                    </figure>
                                    <?php $post_image_gallery = $product->get_gallery_image_ids();?>
                                    <?php if(!empty($post_image_gallery)) :?> 
                                        <div class="rh-flex-eq-height rh_mini_thumbs compare-full-thumbnails mt15 mb15">
                                            <?php foreach($post_image_gallery as $key=>$image_gallery):?>
                                                <?php if(!$image_gallery) continue;?>
                                                <a data-rel="rh_top_gallery" data-thumb="<?php echo wp_get_attachment_url($image_gallery);?>" href="<?php echo wp_get_attachment_url($image_gallery);?>" target="_blank" class="rh-flex-center-align mb10" data-title="<?php echo esc_attr(get_post_field( 'post_excerpt', $image_gallery));?>"> 
                                                    <?php WPSM_image_resizer::show_static_resized_image(array('lazy'=>false, 'src'=> wp_get_attachment_url($image_gallery), 'crop'=> false, 'height'=> 60));?>
                                                </a>                               
                                            <?php endforeach;?> 
                                            <?php if(!empty($youtubecontent)):?>
                                                <?php foreach($youtubecontent as $videoitem):?>
                                                    <a href="<?php echo $videoitem['url'];?>" data-rel="rh_top_gallery" target="_blank" class="rh-flex-center-align mb10 rh_videothumb_link" data-poster="<?php echo parse_video_url($videoitem['url'], 'hqthumb'); ?>" data-thumb="<?php echo $videoitem['img']?>"> 
                                                        <img src="<?php echo $videoitem['img']?>" alt="<?php echo $videoitem['title']?>" />
                                                    </a>                                                    
                                                <?php endforeach;?> 
                                            <?php endif;?>                        
                                        </div>                                      
                                    <?php else :?>
                                        <?php if (rh_is_plugin_active('content-egg/content-egg.php')):?>
                                            <?php if (!empty($itemsync['extra']['imageSet'])){
                                                $ceimages = $itemsync['extra']['imageSet'];
                                            }elseif (!empty($itemsync['extra']['images'])){
                                                $ceimages = $itemsync['extra']['images'];
                                            }
                                            else {
                                                $qwantimages = \ContentEgg\application\components\ContentManager::getViewData('GoogleImages', $post->ID);
                                                if(!empty($qwantimages)) {
                                                    $ceimages = wp_list_pluck( $qwantimages, 'img' );
                                                }else{
                                                    $ceimages = '';                                                
                                                }                                           
                                            } ?> 
                                            <?php if(!empty($ceimages)):?>
                                                <div class="rh_mini_thumbs compare-full-thumbnails limited-thumb-number mt15 mb15">
                                                    <?php foreach ($ceimages as $key => $gallery_img) :?>
                                                        <?php if (isset($gallery_img['LargeImage'])){
                                                            $image = $gallery_img['LargeImage'];
                                                        }else{
                                                            $image = $gallery_img;
                                                        }?>                                               
                                                        <a data-thumb="<?php echo $image?>" data-rel="rh_top_gallery" href="<?php echo $image; ?>" data-title="<?php echo  $itemsync['title'];?>" class="rh-flex-center-align mb10"> 
                                                            <?php WPSM_image_resizer::show_static_resized_image(array('src'=> $image, 'height'=> 65, 'title' => $itemsync['title'], 'no_thumb_url' => get_template_directory_uri().'/images/default/noimage_100_70.png'));?>  
                                                        </a>
                                                    <?php endforeach;?>  
                                                    <?php if(!empty($youtubecontent)):?>
                                                        <?php foreach($youtubecontent as $videoitem):?>
                                                            <a href="<?php echo $videoitem['url'];?>" data-rel="rh_top_gallery" target="_blank" class="mb10 rh-flex-center-align rh_videothumb_link" data-poster="<?php echo parse_video_url($videoitem['url'], 'hqthumb'); ?>" data-thumb="<?php echo $videoitem['img']?>"> 
                                                                <img src="<?php echo $videoitem['img']?>" alt="<?php echo $videoitem['title']?>" />
                                                            </a>                                                    
                                                        <?php endforeach;?> 
                                                    <?php endif;?>                                                       
                                                </div>
                                            <?php endif;?> 
                                        <?php endif;?>               
                                    <?php endif;?>  
                                </div>
                                <div class="wpsm-two-third tabletblockdisplay wpsm-column-last mb30">

                                    <div class="rh-flex-center-align">
                                        <?php if ( 'no' !== get_option( 'woocommerce_enable_review_rating' ) ):?> 
                                            <div class="floatleft mr15">
                                                <?php $rating_count = $product->get_rating_count();?>
                                                <?php if ($rating_count < 1):?>
                                                    <span data-scrollto="#reviews" class="rehub_scroll cursorpointer font80 greycolor"><?php _e("Add your review", "rehub_framework");?></span>
                                                <?php else:?>
                                                    <?php woocommerce_template_single_rating();?>
                                                <?php endif;?>
                                            </div>
                                        <?php endif;?>
                                        <span class="floatleft meta post-meta mt0 mb0">
                                            <?php
                                            if(rehub_option('post_view_disable') != 1){ 
                                                $rehub_views = get_post_meta ($post->ID,'rehub_views',true); 
                                                echo '<span class="greycolor postview_meta mr10">'.$rehub_views.'</span>';
                                            } 
                                            $categories = wp_get_post_terms($post->ID, 'product_cat', array("fields" => "all"));
                                            $separator = '';
                                            $output = '';
                                            if ( ! empty( $categories ) ) {
                                                foreach( $categories as $category ) {
                                                    $output .= '<a class="mr5 ml5 rh-cat-'.$category->term_id.'" href="' . esc_url( get_term_link( $category->term_id, 'product_cat' ) ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'rehub_framework' ), $category->name ) ) . '">' . esc_html( $category->name ) . '</a>' . $separator;
                                                }
                                                echo trim( $output, $separator );
                                            }
                                            ?>                                     
                                        </span>                                        
                                    </div> 
                                    <div class="rh-line mb20 mt10"></div> 
                                    <div class="rh_post_layout_rev_price_holder">
                                        <div class="floatright mobileblockdisplay"> 
                                            <?php if ($unique_id && $module_id && !empty($syncitem)) :?>
                                                <?php include(rh_locate_template( 'inc/parts/pricealertpopup.php' ) ); ?>
                                            <?php endif;?>                                                                 
                                        </div>                                         
                                        <div class="floatleft mr20 mb15 rtlml20 rtlmr0 mobileblockdisplay">
                                            <?php echo wpsm_reviewbox(array('compact'=>'circle', 'id'=> $post->ID, 'scrollid'=>'tab-title-description'));?> 
                                        </div>                     
                                        <div class="compare-button-holder">
                                                                                
                                            <?php woocommerce_template_single_price();?>
                                            <?php if(!empty($itemsync)):?>
                                                <?php echo rh_best_syncpost_deal($itemsync, 'mb10 compare-domain-icon lineheight20', true);?>
                                                <?php $offer_post_url = $itemsync['url'] ;?>
                                                <?php $afflink = apply_filters('rh_post_offer_url_filter', $offer_post_url );?> 
                                                <?php $aff_btn_text = get_post_meta($post->ID, '_button_text', true);?>
                                                <?php 
                                                    if($aff_btn_text) {
                                                        $buy_best_text = $aff_btn_text;
                                                    } 
                                                    elseif(rehub_option('buy_best_text') !=''){
                                                        $buy_best_text = rehub_option('buy_best_text');
                                                    } 
                                                    else{
                                                        $buy_best_text = __('Buy for best price', 'rehub_framework'); 
                                                    } 
                                                ?>                                                 
                                                <a href="<?php echo esc_url($afflink);?>" class="re_track_btn wpsm-button rehub_main_btn btn_offer_block" target="_blank" rel="nofollow"><?php echo $buy_best_text;?>
                                                </a> 
                                            <?php else:?>
                                                <div class="woo-button-area mb30"><?php woocommerce_template_single_add_to_cart();?>
                                                </div>
                                            <?php endif;?>                           
                                        </div>                                                                               
                                    </div>
                                    <?php rh_woo_code_zone('button');?>
                                    <div class="rh-line mt30 mb25"></div>
                                    <?php $thecriteria = get_post_meta((int)$id, '_review_post_criteria', true);?>
                                    <?php 
                                        if (!empty($thecriteria[0]['review_post_name'])) {
                                            $review = true;
                                        }else{
                                             $review = false;
                                        }
                                    ?>                                    
                                    <div<?php if ($review){echo ' class="woo-desc-w-review"';}?>>
                                        <?php 
                                            if ($review)  {
                                                echo '<div class="review_score_min mb15 mr30 rtlml30 font70 pr20 rtlpl20 rh-line-right floatleft"><table><tbody>';
                                                    foreach ($thecriteria as $criteria) {
                                                        if(!empty($criteria)){
                                                            $criteriascore = $criteria['review_post_score'];
                                                            $criterianame = $criteria['review_post_name'];
                                                            echo '<tr><th class="pr10 rtlpl10">'. $criterianame .'</th>';
                                                            echo '<td><strong>'.$criteriascore.'</strong></td>';
                                                            echo '</tr>';                           
                                                        }
                                                    }   
                                                echo '</tbody></table></div>';    
                                            }                                          
                                        ?>                                
                                        <div class="font90 lineheight20 woo_desc_part<?php if ($review){echo ' floatleft';}?>">
                                            <?php if(has_excerpt($post->ID)):?>
                                                <?php woocommerce_template_single_excerpt();?>
                                            <?php else :?>
                                                <?php if(!empty($itemsync['extra']['itemAttributes']['Feature'])){
                                                    $features = $itemsync['extra']['itemAttributes']['Feature'];
                                                }
                                                elseif(!empty($itemsync['extra']['keySpecs'])){
                                                    $features = $itemsync['extra']['keySpecs'];
                                                }
                                                ?> 
                                                <?php if (!empty ($features)) :?>
                                                    <ul class="featured_list mt0">
                                                        <?php $length = $maxlength = 0;?>
                                                        <?php foreach ($features as $k => $feature): ?>
                                                            <?php if(is_array($feature)){continue;}?>
                                                            <?php $length = strlen($feature); $maxlength += $length; ?> 
                                                            <li><?php echo $feature; ?></li>
                                                            <?php if($k >= 5 || $maxlength > 200) break; ?>                             
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else:?>
                                                    <?php echo do_shortcode('[content-egg-block template=price_statistics]');?>
                                                <?php endif ;?> 
                                                <div class="clearfix"></div>                               
                                            <?php endif;?>
                                            <?php rh_woo_code_zone('content');?>
                                        </div>                                   
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="woo-single-meta font80">
                                        <?php do_action( 'woocommerce_product_meta_start' ); ?>
                                        <?php $term_ids =  wp_get_post_terms(get_the_ID(), 'store', array("fields" => "ids")); ?>
                                        <?php if (!empty($term_ids) && ! is_wp_error($term_ids)) :?>
                                            <div class="woostorewrap mb10">
                                                <div class="brand_logo_small">       
                                                    <?php WPSM_Woohelper::re_show_brand_tax('logo'); //show brand logo?>
                                                </div>          
                                                <div class="store_tax">       
                                                    <?php WPSM_Woohelper::re_show_brand_tax(); //show brand taxonomy?>
                                                </div>  
                                            </div>
                                        <?php endif;?>                               
                                        <?php do_action( 'woocommerce_product_meta_end' ); ?>
                                    </div> 
                                    <div class="top_share notextshare">
                                        <?php woocommerce_template_single_sharing();?>
                                    </div>                                                                      
                                    <?php
                                        /**
                                         * woocommerce_single_product_summary hook. was removed in theme and added as functions directly in layout
                                         *
                                         * @dehooked woocommerce_template_single_title - 5
                                         * @dehooked woocommerce_template_single_rating - 10
                                         * @dehooked woocommerce_template_single_price - 10
                                         * @dehooked woocommerce_template_single_excerpt - 20
                                         * @dehooked woocommerce_template_single_add_to_cart - 30
                                         * @dehooked woocommerce_template_single_meta - 40
                                         * @dehooked woocommerce_template_single_sharing - 50
                                         * @hooked WC_Structured_Data::generate_product_data() - 60
                                         */
                                        do_action( 'woocommerce_single_product_summary' );
                                    ?>  
                                </div>
                            </div>

                            <?php $tabs = apply_filters( 'woocommerce_product_tabs', array() );

                            if ( ! empty( $tabs ) ) : ?>

                                <?php if (rh_is_plugin_active('content-egg/content-egg.php')):?>

                                    <?php 
                                        $replacetitle = apply_filters('woo_product_section_title', get_the_title().' ');
                                        if(!empty($syncitem)) {
                                            $tabs['woo-ce-pricelist'] = array(
                                                'title' => $replacetitle.__('Prices', 'rehub_framework'),
                                                'priority' => '8',
                                                'callback' => 'woo_ce_pricelist_output'
                                            );                                 
                                            $tabs['woo-ce-pricehistory'] = array(
                                                'title' => __('Price History', 'rehub_framework'),
                                                'priority' => '9',
                                                'callback' => 'woo_ce_history_output'
                                            ); 
                                        }                               
                                        if(!empty($youtubecontent)){
                                            $tabs['woo-ce-videos'] = array(
                                                'title' => $replacetitle.__('Video Reviews', 'rehub_framework'),
                                                'priority' => '21',
                                                'callback' => 'woo_ce_video_output'
                                            );
                                        }
                                        $googlenews = get_post_meta($post->ID, '_cegg_data_GoogleNews', true);
                                        if(!empty($googlenews)){
                                            $tabs['woo-ce-news'] = array(
                                                'title' => __('World News', 'rehub_framework'),
                                                'priority' => '23',
                                                'callback' => 'woo_ce_news_output'
                                            );
                                        }                                                                                 
                                        uasort( $tabs, '_sort_priority_callback' );                                 
                                    ?>

                                <?php endif;?>

                                <?php wp_enqueue_script('customfloatpanel');?> 
                                <div class="flowhidden rh-float-panel" id="float-panel-woo-area">
                                    <div class="rh-container rh-flex-center-align pt10 pb10">
                                        <div class="float-panel-woo-image">
                                            <?php WPSM_image_resizer::show_static_resized_image(array('lazy'=>false, 'thumb'=> true, 'width'=> 50, 'height'=> 50));?>
                                        </div>
                                        <div class="float-panel-woo-info wpsm_pretty_colored rh-line-left pl15 ml15">
                                            <div class="float-panel-woo-title rehub-main-font mb5 font110">
                                                <?php the_title();?>
                                            </div>
                                            <ul class="float-panel-woo-links list-unstyled list-line-style font80 fontbold lineheight15">
                                                <?php foreach ( $tabs as $key => $tab ) : ?>
                                                    <li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>">
                                                        <?php $tab_title = str_replace($replacetitle, '', $tab['title']);?>
                                                        <a href="#section-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html($tab_title), $key ); ?></a>
                                                    </li>                                                
                                                <?php endforeach; ?>                                        
                                            </ul>                                  
                                        </div>
                                        <div class="float-panel-woo-btn rh-flex-columns rh-flex-right-align">
                                            <div class="float-panel-woo-price rh-flex-center-align font120 rh-flex-right-align">
                                                <?php woocommerce_template_single_price();?>
                                            </div>
                                            <div class="float-panel-woo-button rh-flex-center-align rh-flex-right-align">
                                                <?php if(!empty($itemsync)):?>
                                                    <a href="#section-woo-ce-pricelist" class="single_add_to_cart_button rehub_scroll">
                                                        <?php if(rehub_option('rehub_btn_text_aff_links') !='') :?>
                                                            <?php echo rehub_option('rehub_btn_text_aff_links') ; ?>
                                                        <?php else :?>
                                                            <?php _e('Choose offer', 'rehub_framework') ?>
                                                        <?php endif ;?>
                                                    </a> 
                                                <?php else:?>
                                                    <?php if ( $product->add_to_cart_url() !='') : ?>
                                                        <?php if($product->get_type() == 'variable') {
                                                            $url = '#top_ankor';
                                                        }else{
                                                            $url = esc_url( $product->add_to_cart_url() );
                                                        }

                                                        ?>
                                                        <?php  echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                                                            sprintf( '<a href="%s" data-product_id="%s" data-product_sku="%s" class="re_track_btn btn_offer_block single_add_to_cart_button %s %s product_type_%s"%s %s>%s</a>',
                                                            $url,
                                                            esc_attr( $product->get_id() ),
                                                            esc_attr( $product->get_sku() ),
                                                            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                                                            $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
                                                            esc_attr( $product->get_type() ),
                                                            $product->get_type() =='external' ? ' target="_blank"' : '',
                                                            $product->get_type() =='external' ? ' rel="nofollow"' : '',
                                                            esc_html( $product->add_to_cart_text() )
                                                            ),
                                                        $product );?>
                                                    <?php endif; ?>
                                                <?php endif;?>                                                             
                                            </div>                                        
                                        </div>                                    
                                    </div>                           
                                </div>                                    

                                <div class="content-woo-area">
                                    <?php foreach ( $tabs as $key => $tab ) : ?>
                                        <div class="rh-tabletext-block rh-tabletext-wooblock" id="section-<?php echo esc_attr( $key ); ?>">
                                            <div class="rh-tabletext-block-heading">
                                                <span class="toggle-this-table"></span>
                                                <h4 class="rh-heading-icon"><?php echo $tab['title'];?></h4>
                                            </div>
                                            <div class="rh-tabletext-block-wrapper">
                                                <?php call_user_func( $tab['callback'], $key, $tab ); ?>
                                            </div>
                                        </div>                                            
                                    <?php endforeach; ?>
                                </div>

                            <?php endif; ?>

                            <!-- Related -->
                            <?php $sidebar = (is_active_sidebar( 'sidebarwooinner' ) ) ? true : false; ?>
                            <?php include(rh_locate_template( 'woocommerce/single-product/related-compact.php' ) ); ?>
                            <!-- /Related --> 
                            <!-- Upsell -->
                            <?php include(rh_locate_template( 'woocommerce/single-product/upsell-compact.php' ) ); ?>
                            <!-- /Upsell -->                             

                            <div class="other-woo-area">
                                <div class="rh-container mt30">
                                    <?php
                                        /**
                                         * woocommerce_after_single_product_summary hook.
                                         *
                                         * @hooked woocommerce_output_product_data_tabs - 10
                                         * @hooked woocommerce_upsell_display - 15
                                         * @hooked woocommerce_output_related_products - 20
                                         */
                                        do_action( 'woocommerce_after_single_product_summary' );
                                    ?>
                                </div>  
                            </div> 

                        </div><!-- #product-<?php the_ID(); ?> -->
                        <?php do_action( 'woocommerce_after_single_product' ); ?>
                    <?php endwhile; // end of the loop. ?>
                    <?php do_action( 'woocommerce_after_main_content' ); ?>                                   

                </div>

            </div>
            <?php if ( is_active_sidebar( 'sidebarwooinner' ) ) : ?>
                <?php wp_enqueue_script('stickysidebar');?>
                <aside class="sidebar rh-sticky-container">            
                    <?php dynamic_sidebar( 'sidebarwooinner' ); ?>      
                </aside> 
            <?php endif; ?>                           
        </div>    
    </div>
</div>
<!-- /CONTENT --> 

<?php rh_woo_code_zone('bottom');?>