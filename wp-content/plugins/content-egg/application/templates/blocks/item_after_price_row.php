<?php

use ContentEgg\application\helpers\TemplateHelper;
?>
<div class="after-price-row cegg-mb20 cegg-lineh-20">
    <span class="text-muted">
        <?php if (!empty($item['extra']['totalNew'])): ?>
            <?php echo $item['extra']['totalNew']; ?>
            <?php _e('new', 'content-egg-tpl'); ?> 
            <?php if ($item['extra']['lowestNewPrice']): ?>
                <?php _e('from', 'content-egg-tpl'); ?> <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestNewPrice'], $item['currencyCode']); ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($item['extra']['totalUsed'])): ?>
            <br><?php echo $item['extra']['totalUsed']; ?>
            <?php _e('used', 'content-egg-tpl'); ?> <?php _e('from', 'content-egg-tpl'); ?>
            <?php echo TemplateHelper::formatPriceCurrency($item['extra']['lowestUsedPrice'], $item['currency']); ?>
        <?php endif; ?>
        <?php if (!empty($item['extra']['IsEligibleForSuperSaverShipping'])): ?>
            <br><small class="text-muted text-success"><?php _e('Free shipping', 'content-egg-tpl'); ?></small>
        <?php endif; ?>                            
    </span>
    <?php if ($module_id == 'LomadeeProducts' && !empty($item['extra']['product']['hasOffer']) && $item['extra']['product']['hasOffer'] > 1): ?>

        <span class="cegg-price">
            <?php echo TemplateHelper::formatPriceCurrency($item['extra']['product']['priceMin'], $item['currencyCode'], '<span class="cegg-currency">', '</span>'); ?>
            -
            <?php echo TemplateHelper::formatPriceCurrency($item['extra']['product']['priceMax'], $item['currencyCode'], '<span class="cegg-currency">', '</span>'); ?>
        </span>
        (<a href="<?php echo esc_attr($item['extra']['product']['link']); ?>" target="_blank" rel="nofollow">
            <?php echo \esc_html($item['extra']['product']['hasOffer']) ?> <?php echo _e('offers', 'content-egg-tpl'); ?>
        </a>)
    <?php endif; ?>    
</div>
