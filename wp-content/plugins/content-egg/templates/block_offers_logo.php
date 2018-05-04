<?php
/*
 * Name: Sorted offers list with store logos
 * Modules:
 * Module Types: PRODUCT
 * 
 */

__('Sorted offers list with store logos', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
use ContentEgg\application\helpers\TextHelper;
?>

<?php
$all_items = TemplateHelper::sortAllByPrice($data, $order);
$amazon_last_updated = TemplateHelper::getLastUpdateFormattedAmazon($data); 
?>

<div class="egg-container">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <div class="egg-listcontainer cegg-list-withlogos">
        <?php foreach ($all_items as $key => $item): ?>           
            <div class="row-products">
                <div class="col-md-2 col-sm-2 col-xs-12 cegg-image-cell">
                    <?php if ($logo = TemplateHelper::getMerhantLogoUrl($item, true)): ?>
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                            <img class="cegg-merhant-logo" src="<?php echo esc_attr($logo); ?>" alt="<?php echo esc_attr($item['domain']); ?>" />
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-5 col-sm-5 col-xs-12 cegg-desc-cell">
                    <div class="cegg-no-top-margin cegg-list-logo-title">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                            <?php echo esc_html(TextHelper::truncate($item['title'], 100)); ?>
                        </a>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12 cegg-price-cell text-center">
                    <div class="cegg-price-row">

                        <?php if ($item['price']): ?>
                            <div class="cegg-price"><?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']); ?></div>
                        <?php elseif (!empty($item['extra']['toLowToDisplay'])): ?>
                            <div class="text-muted"><?php _e('Too low to display', 'content-egg-tpl'); ?></div>
                        <?php endif; ?> 
                        <?php if ($item['priceOld']): ?>
                            <div class="text-muted"><strike><?php echo TemplateHelper::formatPriceCurrency($item['priceOld'], $item['currencyCode'], '<small>', '</small>'); ?></strike></div>
                        <?php endif; ?>

                        <?php if (!empty($item['extra']['totalNew'])): ?>
                            <div class="cegg-font60 cegg-lineheight15">
                                <?php echo $item['extra']['totalNew']; ?>
                                <?php _e('new', 'content-egg-tpl'); ?> 
                                <?php if ($item['extra']['lowestNewPrice']): ?>
                                    <?php _e('from', 'content-egg-tpl'); ?> <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestNewPrice'], $item['currencyCode']); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($item['extra']['totalUsed'])): ?>
                            <div class="cegg-font60 cegg-lineheight15">
                                <?php echo $item['extra']['totalUsed']; ?>
                                <?php _e('used', 'content-egg-tpl'); ?> <?php _e('from', 'content-egg-tpl'); ?>
                                <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestUsedPrice'], $item['currencyCode']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($item['module_id'] == 'Amazon' && $amazon_last_updated): ?>
                            <div class="cegg-font60 cegg-lineheight15">
                                <?php _e('as of', 'content-egg-tpl'); ?> <?php echo $amazon_last_updated; ?>
                                <?php TemplateHelper::printAmazonDisclaimer(); ?>                                
                            </div>
                        <?php endif; ?>

                    </div> 
                </div>                    
                <div class="col-md-2 col-sm-2 col-xs-12 cegg-btn-cell">        
                    <div class="cegg-btn-row cegg-mb5">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>" class="btn btn-success"><?php TemplateHelper::buyNowBtnText(); ?></a> 
                    </div>  
                    <?php if (!empty($item['extra']['IsEligibleForSuperSaverShipping'])): ?>
                        <div class="text-success cegg-font60 cegg-lineheight15"><?php _e('Free shipping', 'content-egg-tpl'); ?></div>
                    <?php endif; ?>    
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>