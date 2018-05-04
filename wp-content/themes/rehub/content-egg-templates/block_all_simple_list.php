<?php
/*
 * Name: Simple 4 List item
 * Modules:
 * Module Types: PRODUCT
 * 
 */
?>
<?php
use ContentEgg\application\helpers\TemplateHelper;
// sort items by price
?> 
<?php 
$all_items = TemplateHelper::sortAllByPrice($data);            
?>
<div class=" clearfix"></div>
<div class="ce_common_simple_list">
    
    <?php  foreach ($all_items as $key => $item): ?>
        <?php if ($key > 3){break;}?>
        <?php $offer_post_url = $item['url'] ;?>
        <?php $afflink = apply_filters('rh_post_offer_url_filter', $offer_post_url );?>
        <?php $aff_thumb = $item['img'] ;?>
        <?php $offer_title = wp_trim_words( $item['title'], 10, '...' ); ?>
        <?php $merchant = (!empty($item['merchant'])) ? $item['merchant'] : ''; ?>
        <?php $manufacturer = (!empty($item['manufacturer'])) ? $item['manufacturer'] : ''; ?>
        <?php $offer_price = (!empty($item['price'])) ? $item['price'] : ''; ?>
        <?php $offer_price_old = (!empty($item['priceOld'])) ? $item['priceOld'] : ''; ?> 
        <?php $currency_code = (!empty($item['currencyCode'])) ? $item['currencyCode'] : ''; ?>
        <?php $modulecode = (!empty($item['module_id'])) ? $item['module_id'] : ''; ?>        
        <?php if (!empty($item['domain'])):?>
            <?php $domain = $item['domain'];?>
        <?php elseif (!empty($item['extra']['domain'])):?>
            <?php $domain = $item['extra']['domain'];?>
        <?php else:?>
            <?php $domain = '';?>        
        <?php endif;?>    
        <?php $domain = rh_fix_domain($merchant, $domain);?> 
        <?php if(empty($merchant) && !empty($domain)) {
            $merchant = $domain;
        }
        ?>
        <?php if(rehub_option('rehub_btn_text') !='') :?><?php $btn_txt = rehub_option('rehub_btn_text') ; ?><?php else :?><?php $btn_txt = __('Buy Now', 'rehub_framework') ;?><?php endif ;?>        
        <?php $logo = TemplateHelper::getMerhantLogoUrl($item, true);?>    
        <div class="flowhidden pb10 pt15 border-grey-bottom module_class_<?php echo $modulecode;?>">               
            <div class="floatleft mobileblockdisplay mb15 offer_thumb<?php if(!$logo) {echo ' nologo_thumb';}?>">   
                <?php if($logo) :?>
                    <a rel="nofollow" target="_blank" href="<?php echo esc_url($afflink) ?>" class="re_track_btn">
                    <img src="<?php echo esc_attr(TemplateHelper::getMerhantLogoUrl($item, true)); ?>" alt="<?php echo esc_attr($offer_title); ?>" height="30" style="max-height: 30px" />
                    </a>
                    <?php if (!empty($item['extra']['estimatedDeliveryTime'])): ?>
                        <small class="font70 blockstyle lineheight15">
                            <span class="yes_available"><?php echo $item['extra']['estimatedDeliveryTime'] ;?></span>
                        <br />
                        </small>
                    <?php endif; ?>  
                    <?php if (!empty($item['extra']['IsEligibleForSuperSaverShipping'])): ?>
                        <small class="font70 blockstyle lineheight15">
                            <?php _e('& Free shipping', 'rehub_framework'); ?>
                        </small> 
                    <?php endif; ?>                     
                <?php endif ;?>                                                           
            </div>
            <div class="floatright buttons_col pl20 rtlpr20 wpsm-one-half-mobile wpsm-column-last">
                <div class="priced_block clearfix mt0 floatright">
                    <a class="re_track_btn btn_offer_block" href="<?php echo esc_url($afflink) ?>" target="_blank" rel="nofollow">
                        <?php echo $btn_txt;?>
                    </a>                                                        
                </div>                                  
            </div>                                  
            <div class="floatright text-right-align disablemobilealign wpsm-one-half-mobile">
                <?php if(!empty($item['price'])) : ?>
                    <span class="font120 rehub-main-font fontbold">
                        <a rel="nofollow" target="_blank" href="<?php echo esc_url($afflink) ?>" class="re_track_btn blackcolor blockstyle lineheight20">
                            <span><?php echo TemplateHelper::formatPriceCurrency($offer_price, $currency_code); ?></span>
                            <?php if($offer_price_old) : ?>
                            <strike class="blockstyle">
                                <span class="amount font70 rh_opacity_3">
                                    <?php echo TemplateHelper::formatPriceCurrency($offer_price_old, $currency_code); ?>
                                </span>
                            </strike>
                            <?php endif ;?>                                     
                        </a>
                    </span>
                    <?php if (!empty($item['extra']['totalNew'])): ?>
                        <div class="font60 lineheight15">
                            <?php echo $item['extra']['totalNew']; ?>
                            <?php _e('new', 'rehub_framework'); ?> 
                            <?php if ($item['extra']['lowestNewPrice']): ?>
                                 <?php _e('from', 'rehub_framework'); ?> <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestNewPrice'], $item['currency']); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>                            
                    <?php if (!empty($item['extra']['totalUsed'])): ?>
                        <span class="val_sim_price_used_merchant">
                        <?php _e('Used', 'rehub_framework'); ?> - <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestUsedPrice'], $item['currencyCode']); ?>
                        </span>
                    <?php endif; ?>                                                                       
                <?php else:?>
                    -
                <?php endif ;?>                       
            </div> 
                                                              
        </div>
    <?php endforeach; ?>                   
</div>
<div class="clearfix"></div>