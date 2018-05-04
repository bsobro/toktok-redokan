<?php
/*
 * Name: Sorted list with store logo
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
<div class="egg_sort_list re_sort_list simple_sort_list">
    <div class="aff_offer_links">
        <?php  foreach ($all_items as $key => $item): ?>

            <?php $offer_post_url = $item['url'] ;?>
            <?php $afflink = apply_filters('rh_post_offer_url_filter', $offer_post_url );?>
            <?php $aff_thumb = $item['img'] ;?>
            <?php $offer_title = wp_trim_words( $item['title'], 10, '...' ); ?>
            <?php $merchant = (!empty($item['merchant'])) ? $item['merchant'] : ''; ?>
            <?php $manufacturer = (!empty($item['manufacturer'])) ? $item['manufacturer'] : ''; ?>
            <?php $modulecode = (!empty($item['module_id'])) ? $item['module_id'] : ''; ?>            
            <?php if (!empty($item['domain'])):?>
                <?php $domain = $item['domain'];?>
            <?php elseif (!empty($item['extra']['domain'])):?>
                <?php $domain = $item['extra']['domain'];?>
            <?php else:?>
                <?php $domain = '';?>        
            <?php endif;?>      
            <?php $domain = rh_fix_domain($merchant, $domain);?> 
            <?php $offer_price = (!empty($item['price'])) ? $item['price'] : ''; ?>
            <?php $offer_price_old = (!empty($item['priceOld'])) ? $item['priceOld'] : ''; ?>            
            <?php $currency_code = (!empty($item['currencyCode'])) ? $item['currencyCode'] : ''; ?>            
            <?php if(empty($merchant) && !empty($domain)) {
                $merchant = $domain;
            }
            ?>
            <?php $logo = TemplateHelper::getMerhantLogoUrl($item, true);?>
            <?php if(rehub_option('rehub_btn_text') !='') :?><?php $btn_txt = rehub_option('rehub_btn_text') ; ?><?php else :?><?php $btn_txt = __('Buy Now', 'rehub_framework') ;?><?php endif ;?>  
            <div class="rehub_feat_block table_view_block module_class_<?php echo $modulecode;?>">               
                <div class="logo_offer_thumb offer_thumb<?php if(!$logo) {echo ' nologo_thumb';}?>">   
                    <a rel="nofollow" target="_blank" href="<?php echo esc_url($afflink) ?>" class="re_track_btn">
                        <?php if($logo) :?>
                            <img src="<?php echo $logo; ?>" alt="<?php echo esc_attr($offer_title); ?>" height="30" />
                        <?php endif ;?>                                                           
                    </a>
                </div>
                <div class="desc_col desc_simple_col">
                    <a rel="nofollow" target="_blank" class="re_track_btn no-color-link rehub-main-font font90 lineheight20 blockstyle" href="<?php echo esc_url($afflink) ?>">
                        <?php echo esc_attr($offer_title); ?>
                    </a>                               
                </div>                    
                <div class="desc_col price_simple_col">
                    <?php if($offer_price) : ?>
                        <span class="price_count">
                            <?php echo TemplateHelper::formatPriceCurrency($offer_price, $currency_code); ?>
                            <?php if($offer_price_old) : ?>
                            <strike>
                                <span class="amount">
                                    <?php echo TemplateHelper::formatPriceCurrency($offer_price_old, $currency_code); ?>
                                </span>
                            </strike>
                            <?php endif ;?>                                      
                        </span> 
                        <?php if($modulecode == 'Amazon'):?>
                            <?php $pop = uniqid();?>
                            <div class="font60 lineheight15"><?php echo TemplateHelper::getLastUpdateFormattedAmazon($data);?>
                                
                                <span class="csspopuptrigger" data-popup="ce-amazon-disclaimer<?php echo $pop;?>"><i class="far fa-question-circle greycolor font100"></i></span></div>
                                <div class="csspopup" id="ce-amazon-disclaimer<?php echo $pop;?>">
                                    <div class="csspopupinner">
                                        <span class="cpopupclose">×</span>
                                        <?php _e('Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on Amazon.com (Amazon.in, Amazon.co.uk, Amazon.de, etc) at the time of purchase will apply to the purchase of this product.', 'rehub_framework');?>
                                    </div>                                
                                </div>
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
                                <div class="font60 lineheight15">
                                <?php echo $item['extra']['totalUsed']; ?>
                                <?php _e('used', 'rehub_framework'); ?> <?php _e('from', 'rehub_framework'); ?>
                                    <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestUsedPrice'], $item['currencyCode']); ?>
                                </div>
                            <?php endif; ?>                            
                        <?php endif;?>                        
                    <?php endif ;?>                        
                </div>
                <div class="buttons_col">
                    <div class="priced_block clearfix">
                        <a class="re_track_btn btn_offer_block" href="<?php echo esc_url($afflink) ?>" target="_blank" rel="nofollow">
                            <?php echo $btn_txt ; ?>
                        </a>                                                        
                    </div>
                </div>                                                                         
            </div>
        <?php endforeach; ?>               
    </div>    
</div>
<div class="clearfix"></div>