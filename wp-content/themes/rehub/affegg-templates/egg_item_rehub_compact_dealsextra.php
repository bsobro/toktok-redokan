<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
/*
  Name: Compact product cart with extra and deals
 */
use Keywordrush\AffiliateEgg\TemplateHelper;   
?>
<?php if (isset($title) && $title): ?>
    <h3 class="cegg-shortcode-title"><?php echo esc_html($title); ?></h3>
<?php endif; ?>
<?php
// sort items by price
usort($items, function($a, $b) {
    if (!$a['price_raw']) return 1;
    if (!$b['price_raw']) return -1;
    return $a['price_raw'] - $b['price_raw'];
});
$product_price_update = $items[0]['last_update'];
//Check if post has meta for first product
$aff_thumb_overwrite = get_post_meta( get_the_ID(), 'affegg_image_over', true );
$offer_desc_overwrite = get_post_meta( get_the_ID(), 'affegg_desc_over', true );
//Data for main product
$aff_thumb_first = (!empty ($aff_thumb_overwrite)) ? $aff_thumb_overwrite : $items[0]['img'];
$offer_title_first = wp_trim_words( $items[0]['title'], 20, '...' );
$offer_url_first = $items[0]['url'];
$offer_url_first = apply_filters('rh_post_offer_url_filter', $offer_url_first );
$offer_desc_first = (!empty ($aff_thumb_overwrite)) ? $aff_thumb_overwrite : $items[0]['description'] ;
$best_price_value = str_replace(' ', '', $items[0]['price']);
if($best_price_value =='0') {$best_price_value = '';}
$best_price_currency = $items[0]['currency'];
$best_price_text = (rehub_option('rehub_btn_text_best') !='') ? esc_html(rehub_option('rehub_btn_text_best')) : __('Best price', 'rehub_framework');
if (!empty ($items[0]['features'])) {$attributes = $items[0]['features'];}
if (!empty ($items[0]['extra']['images'])) {$gallery_images = $items[0]['extra']['images'];}
if (!empty($items[0]['extra']['comments'])) {$import_comments = $items[0]['extra']['comments'];} 
?>
<div class="rehub_woo_review product_egg_extra">
        <ul class="rehub_woo_tabs_menu">
            <li><?php _e('Product', 'rehub_framework') ?></li>
            <li class="dealslist"><?php _e('Deals', 'rehub_framework') ?></li>
            <?php if (!empty ($attributes)) :?><li><?php _e('Specification', 'rehub_framework') ?></li><?php endif ;?>
            <?php if (!empty ($gallery_images)) :?><li><?php _e('Photos', 'rehub_framework') ?></li><?php endif ;?>
            <?php if (!empty ($import_comments)) :?><li class="affrev"><?php _e('Last reviews', 'rehub_framework') ?></li><?php endif ;?>
        </ul>
    <div class="rehub_feat_block table_view_block">
        <div class="rehub_woo_review_tabs" style="display:table-row">
            <div class="offer_thumb">   
                <?php if (!empty($aff_thumb_first) ) :?>  
                    <img src="<?php $params = array( 'width' => 120 ); echo bfi_thumb( esc_attr($aff_thumb_first), $params ); ?>" alt="<?php echo esc_attr($offer_title_first); ?>" />
                <?php else :?>
                    <?php $image_id = get_post_thumbnail_id(get_the_ID());  $image_offer_url = wp_get_attachment_url($image_id);?>
                    <img src="<?php $params = array( 'width' => 120 ); echo bfi_thumb( $image_offer_url, $params ); ?>" alt="<?php echo esc_attr($offer_title_first); ?>" />
                <?php endif ;?>                                    
            </div>
            <div class="desc_col">
            <?php if ($items[0]['manufacturer']): ?>
                <p class="small_size"><span class="aff_manufactor"><?php echo esc_attr ($items[0]['manufacturer']); ?></span></p>
            <?php endif; ?>                 
            <?php if ($offer_desc_first): ?>
                <p><?php rehub_truncate('maxchar=200&text='.$offer_desc_first.''); ?></p>
            <?php endif; ?>                                                
            </div>
            <div class="buttons_col">
                <?php if(!empty($best_price_value)) : ?>
                    <div class="deals-box-pricebest">
                    <span><?php _e('Start from: ', 'rehub_framework');?></span>
                        <?php echo TemplateHelper::formatPriceCurrency($items[0]['price_raw'], $items[0]['currency_code'], '', ''); ?>                       
                    </div>                                                       
                <?php endif ;?> 
                <?php if(!empty($offer_url_first)) : ?> 
                    <div class="priced_block">
                        <div>
                            <a class="btn_offer_block" href="<?php echo esc_url($offer_url_first) ?>"<?php echo $items[0]['ga_event'] ?> target="_blank" rel="nofollow">
                                <?php echo $best_price_text; ?>                                    
                            </a>
                            
                        </div>
                    </div>
                <?php endif ;?>
                <span class="aff_tag mtinside"><?php echo rehub_get_site_favicon($items[0]['orig_url']); ?></span>
            </div>
        </div>

        <div class="rehub_woo_review_tabs dealslist">
            <div class="egg_sort_list simple_sort_list"><a name="aff-link-list"></a>
                <div class="aff_offer_links">
                    <?php $i=0; foreach ($items as $key => $item): ?>
                        <?php $offer_price = (!empty($item['price'])) ? $item['price'] : ''; ?>
                        <?php $offer_price_old = str_replace(' ', '', $item['old_price']); if($offer_price_old =='0') {$offer_price_old = '';}?>                       
                            <?php $offer_post_url = $item['url'] ;?>
    <?php $afflink = apply_filters('rh_post_offer_url_filter', $offer_post_url );?>  
                            <?php $offer_post_url = $item['url'] ;?>
    <?php $afflink = apply_filters('rh_post_offer_url_filter', $offer_post_url );?>
                        <?php $aff_thumb = $item['img'] ;?>
                        <?php $offer_title = wp_trim_words( $item['title'], 10, '...' ); ?>
                        <?php $offer_desc = $item['description'] ;?>
                        <?php  $i++;?>  
                        <?php if(rehub_option('rehub_btn_text') !='') :?><?php $btn_txt = rehub_option('rehub_btn_text') ; ?><?php else :?><?php $btn_txt = __('Buy this item', 'rehub_framework') ;?><?php endif ;?>  
                        <div class="table_view_block<?php if ($i == 1){echo' best_price_item';}?>">
                            <div>
                                <div class="offer_thumb">   
                                    <a rel="nofollow" target="_blank" class="re_track_btn" href="<?php echo esc_url($afflink) ?>"<?php echo $item['ga_event'] ?>>
                                        <?php WPSM_image_resizer::show_static_resized_image(array('src'=> $aff_thumb, 'height'=> 100, 'title' => $offer_title, 'no_thumb_url' => get_template_directory_uri().'/images/default/noimage_100_70.png'));?>                                                                       
                                    </a>
                                </div>
                                <div class="desc_col desc_simple_col">
                                    <div class="simple_title">
                                        <a rel="nofollow" class="re_track_btn" target="_blank" href="<?php echo esc_url($afflink) ?>"<?php echo $item['ga_event'] ?>>
                                            <?php echo esc_attr($offer_title); ?>
                                        </a>
                                    </div>                                
                                </div>                    
                                <div class="desc_col price_simple_col">
                                    <?php if(!empty($offer_price)) : ?>
                                        <div class="rh_price_wrapper">
                                            <span class="price_count">
                                                <?php echo TemplateHelper::formatPriceCurrency($item['price_raw'], $item['currency_code'], '<span class="cegg-currency">', '</span>'); ?>
                                                <?php if(!empty($offer_price_old)) : ?>
                                                    <strike><?php echo TemplateHelper::formatPriceCurrency($item['old_price_raw'], $item['currency_code'], '', ''); ?></strike>
                                                <?php endif ;?>                                      
                                            </span>                        
                                        </div>
                                    <?php endif ;?>                        
                                </div>
                                <div class="buttons_col">
                                    <div class="priced_block clearfix">
                                        <div>
                                            <a class="re_track_btn btn_offer_block" href="<?php echo esc_url($afflink) ?>"<?php echo $item['ga_event'] ?> target="_blank" rel="nofollow">
                                                <?php echo $btn_txt ; ?>
                                            </a>
                                            <?php $offer_coupon_mask = 1 ?>
                                            <?php if(!empty($item['extra']['coupon']['code_date'])) : ?>
                                                <?php 
                                                $timestamp1 = strtotime($item['extra']['coupon']['code_date']); 
                                                $seconds = $timestamp1 - time(); 
                                                $days = floor($seconds / 86400);
                                                $seconds %= 86400;
                                                if ($days > 0) {
                                                  $coupon_text = $days.' '.__('days left', 'rehub_framework');
                                                  $coupon_style = '';
                                                }
                                                elseif ($days == 0){
                                                  $coupon_text = __('Last day', 'rehub_framework');
                                                  $coupon_style = '';
                                                }
                                                else {
                                                  $coupon_text = __('Coupon is Expired', 'rehub_framework');
                                                  $coupon_style = 'expired_coupon';
                                                }                 
                                                ?>
                                            <?php endif ;?>
                                            <?php  if(!empty($item['extra']['coupon']['code'])) : ?>
                                                <?php wp_enqueue_script('zeroclipboard'); ?>
                                                <?php if ($offer_coupon_mask !='1' && $offer_coupon_mask !='on') :?>
                                                    <div class="rehub_offer_coupon not_masked_coupon <?php if(!empty($item['extra']['coupon']['code_date'])) {echo $coupon_style ;} ?>" data-clipboard-text="<?php echo $item['extra']['coupon']['code'] ?>"><i class="fal fa-cut fa-rotate-180"></i><span class="coupon_text"><?php echo $item['extra']['coupon']['code'] ?></span></div>   
                                                <?php else :?>
                                                    <?php wp_enqueue_script('affegg_coupons'); ?>
                                                    <div class="rehub_offer_coupon masked_coupon <?php if(!empty($item['extra']['coupon']['code_date'])) {echo $coupon_style ;} ?>" data-clipboard-text="<?php echo $item['extra']['coupon']['code'] ?>" data-codetext="<?php echo $item['extra']['coupon']['code'] ?>" data-dest="<?php echo esc_url($item['url']) ?>"<?php echo $item['ga_event'] ?>><?php if(rehub_option('rehub_mask_text') !='') :?><?php echo rehub_option('rehub_mask_text') ; ?><?php else :?><?php _e('Reveal coupon', 'rehub_framework') ?><?php endif ;?><i class="far fa-external-link-square"></i></div>   
                                                <?php endif;?>
                                                <?php if(!empty($item['extra']['coupon']['code_date'])) {echo '<div class="time_offer">'.$coupon_text.'</div>';} ?>    
                                            <?php endif ;?> 
                                                                        
                                        </div>
                                    </div>
                                   <div class="aff_tag"><?php echo rehub_get_site_favicon($item['orig_url']); ?></div> 
                                   <small class="small_size available_stock"><?php if ($item['in_stock']): ?><span class="yes_available"><?php _e('In stock', 'rehub_framework') ;?></span><?php endif; ?></small>                                    
                                </div>
                            </div>                                                          
                        </div>
                    <?php endforeach; ?>                    
                    <?php if (!empty($product_price_update)) :?>
                        <div class="last_update"><?php _e('Last price update: ', 'rehub_framework'); ?><?php echo $product_price_update ;?></div>
                    <?php endif ;?>
                    <?php if ($see_more_uri): ?>
                            <div class="text-center see-more-cat"> 
                                <a rel="nofollow" target="_blank" href="<?php echo $see_more_uri; ?>"><?php _e('See more from category', 'rehub_framework');?></a>
                            </div>
                    <?php endif; ?>                
                </div>
            </div>
            <div class="clearfix"></div>                
        </div>

        <?php if (!empty ($attributes)) :?>
            <div class="rehub_woo_review_tabs">
                <div>
                    <table class="shop_attributes">
                        <tbody>
                        <?php foreach ($attributes as $feature): ?>
                            <tr>
                                <th><?php echo esc_html($feature['name']) ?></th>
                                <td><p><?php echo esc_html($feature['value']) ?></p></td>
                            </tr>
                        <?php endforeach; ?>                                        
                        </tbody>
                    </table>
                </div>                               
            </div>
        <?php endif ;?> 
        <?php if (!empty ($gallery_images)) :?>
            <div class="rehub_woo_review_tabs pretty_woo modulo-lightbox">
                <?php $randomgallery = 'rh_ceam_gallery'.rand(1, 50);?>            
                <?php wp_enqueue_script('modulobox'); wp_enqueue_style('modulobox');
                    foreach ($gallery_images as $gallery_img) {
                        ?> 
                        <a data-rel="<?php echo $randomgallery;?>" href="<?php echo esc_attr($gallery_img) ;?>" data-thumb="<?php echo $gallery_img;?>" data-title="<?php echo $offer_title;?>">                        
                            <?php WPSM_image_resizer::show_static_resized_image(array('src'=> $gallery_img, 'width'=> 100, 'height'=> 100, 'title' => $offer_title_first, 'no_thumb_url' => get_template_directory_uri().'/images/default/noimage_100_70.png'));?> 
                        </a>
                        <?php
                    }
                ?>
            </div>           
        <?php endif ;?>
        <?php if (!empty ($import_comments)) :?>
            <div class="rehub_woo_review_tabs affrev">
                <?php foreach ($import_comments as $key => $comment): ?>
                    <div class="helpful-review black">
                        <div class="quote-top"><i class="fas fa-quote-left"></i></div>
                        <div class="quote-bottom"><i class="fas fa-quote-right"></i></div>
                        <div class="text-elips">
                            <span><?php echo $comment['comment']; ?></span>
                        </div>
                        <?php if (!empty($comment['date'])): ?>
                            <span class="helpful-date"><?php echo gmdate("F j, Y", $comment['date']); ?></span>
                        <?php endif ;?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif ;?>                                                         
    </div>
</div>
<div class="clearfix"></div>