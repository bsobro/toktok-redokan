<?php
/*
 * Name: Content Grid
 * 
 */

use ContentEgg\application\helpers\TemplateHelper;
$cols = (isset($cols)) ? (int)$cols : 4;
if ($cols == 4){
    $col_wrap = 'col_wrap_fourth';
}  
elseif ($cols == 5){
    $col_wrap = 'col_wrap_fifth';
} 
elseif ($cols == 6){
    $col_wrap = 'col_wrap_six';
} 
elseif($cols == 3) {
   $col_wrap = 'col_wrap_three'; 
} 
else{
   $col_wrap = 'col_wrap_fourth'; 
}
?>

<div class="eq_grid post_eq_grid rh-flex-eq-height <?php echo $col_wrap;?>">
      
<?php foreach ($items as $key => $item): ?> 
<?php $aff_thumb = (!empty($item['img'])) ? esc_attr($item['img']) : '' ;?>  
<?php $offer_title = (!empty($item['title'])) ? wp_trim_words( $item['title'], 12, '...' ) : ''; ?>
<?php $offer_post_url = $item['url'] ;?>
<?php $afflink = apply_filters('rh_post_offer_url_filter', $offer_post_url );?>    
<article class="col_item offer_grid mobile_compact_grid offer_grid_com no_btn_enabled"> 
    <div class="info_in_dealgrid">       
        <figure class="mb15">
            <?php if ($item['_price_movers']['discount_percent'] > 0): ?>
                <span class="grid_onsale"><?php echo $item['_price_movers']['discount_percent']; ?>%</span>
            <?php endif ; ?>

            <a class="img-centered-flex rh-flex-center-align rh-flex-justify-center re_track_btn" href="<?php echo $afflink;?>" rel="nofollow" target="_blank">
                <?php WPSM_image_resizer::show_static_resized_image(array('src'=> $aff_thumb, 'crop'=> false, 'width'=> 250, 'height'=> 180, 'no_thumb_url' => get_template_directory_uri() . '/images/default/noimage_250_180.png'));?>                
            </a>
        </figure>
        <div class="grid_desc_and_btn">
            <div class="grid_row_info">
                <div class="flowhidden mb10">
                    <div class="price_for_grid floatleft">
                        <div class="priced_block clearfix mt0">
                            <span class="rh_price_wrapper">
                                <span class="price_count" title="<?php echo \esc_attr(__('as of', 'content-egg-tpl') . ' ' . TemplateHelper::formatDatetime($item['_price_movers']['create_date'])); ?>">
                                    <ins><?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']); ?></ins> 
                                    <?php if ($item['_price_movers']['discount_value'] > 0): ?>
                                        <i class="far fa-arrow-down greencolor"></i>
                                    <?php endif; ?>
                                    <?php if ($item['_price_movers']['discount_value'] < 0): ?>
                                        <i class="far fa-arrow-up redcolor"></i>
                                    <?php endif; ?>                                          
                                    <del>
                                    <?php echo TemplateHelper::formatPriceCurrency($item['_price_movers']['price_old'], $item['currencyCode']); ?>                                     
                                    <i class="far fa-question-circle greycolor"></i> 
                                    </del>                      
                                </span>
                            </span>                                 
                        </div>                        
                    </div>
                    <div class="floatright vendor_for_grid aff_tag">
                        <?php if ($logo = TemplateHelper::getMerhantLogoUrl($item, true)): ?>
                            <img src="<?php echo esc_attr($logo); ?>" title="<?php echo esc_attr($item['domain']); ?>" alt="<?php echo esc_attr($item['domain']); ?>" />
                        <?php endif; ?> 
                    </div>
                </div>     
       
                <h3 class=""><a href="<?php echo $afflink;?>" rel="nofollow" target="_blank" class="re_track_btn"><?php echo esc_attr($offer_title); ?></a></h3> 
            </div>
 
        </div>                                       
    </div>
    <div class="meta_for_grid">
        <div class="date_for_grid floatleft mr5">
            <span class="date_ago">
                <i class="far fa-clock"></i><?php printf( __( '%s ago', 'rehub_framework' ), human_time_diff( $item['_price_movers']['create_date'], current_time( 'timestamp' ) ) ); ?>
            </span>        
        </div>        
        <div class="cat_store_for_grid floatright">
            <div class="cat_for_grid font70"> 
                <?php if (!empty($item['extra']['totalUsed'])): ?>
                    <span class="val_sim_price_used_merchant">
                    <?php echo $item['extra']['totalUsed']; ?>
                    <?php _e('used', 'rehub_framework'); ?> <?php _e('from', 'rehub_framework'); ?>
                        <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestUsedPrice'], $item['currencyCode']); ?>
                    </span>
                <?php endif; ?>             
                <?php if (!empty($item['extra']['conditionDisplayName'])): ?>
                    <small class="font70">
                    <?php _e('Condition: ', 'rehub_framework') ;?><span class="yes_available"><?php echo $item['extra']['conditionDisplayName'] ;?></span>
                    <br />
                    </small>
                <?php endif; ?>
                <?php if (!empty($item['extra']['estimatedDeliveryTime'])): ?>
                    <small class="greencolor">
                        <span class="yes_available"><?php echo $item['extra']['estimatedDeliveryTime'] ;?></span>
                    <br />
                    </small>
                <?php endif; ?>  
                <?php if (!empty($item['extra']['IsEligibleForSuperSaverShipping'])): ?>
                    <small class="greencolor">
                        <?php _e('& Free shipping', 'rehub_framework'); ?>
                    </small> 
                <?php endif; ?>                                    
            </div>          
        </div>   
    </div>     
</article>
<?php endforeach; ?>

</div>