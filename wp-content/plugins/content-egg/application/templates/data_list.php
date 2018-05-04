<?php

use ContentEgg\application\helpers\TemplateHelper;
use ContentEgg\application\helpers\TextHelper;
?>

<div class="egg-container egg-list">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <div class="egg-listcontainer">
        <?php foreach ($items as $item): ?>
            <div class="row-products">
                <div class="col-md-2 col-sm-2 col-xs-12 cegg-image-cell">
                    <?php if ($item['img']): ?>
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                            <img src="<?php echo $item['img']; ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-5 col-sm-5 col-xs-12 cegg-desc-cell">
                    <div class="cegg-no-top-margin cegg-list-logo-title">
                        <b><a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                                <?php echo esc_html(TextHelper::truncate($item['title'], 100)); ?>
                            </a></b>
                    </div>

                    <?php if ((int) $item['rating'] > 0 && (int) $item['rating'] <= 5): ?>
                        <div class="cegg-title-rating">
                            <span class="rating_default"><?php
                                echo str_repeat("<span>&#x2605;</span>", (int) $item['rating']);
                                echo str_repeat("<span>☆</span>", 5 - (int) $item['rating']);
                                ?></span>
                            <?php if (!empty($item['reviewsCount'])): ?><small class="cegg-reviews-count small-text">(<?php echo (int) $item['reviewsCount']; ?>)</small><?php endif; ?>
                        </div>
                    <?php endif; ?>                    
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

                    </div> 
                </div>                    
                <div class="col-md-2 col-sm-2 col-xs-12 cegg-btn-cell">        
                    <div class="cegg-btn-row cegg-mb5">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>" class="btn btn-success"><?php TemplateHelper::buyNowBtnText(); ?></a> 
                    </div>  

                    <?php if ($merhant = TemplateHelper::getMerhantName($item)): ?>
                        <div class="cegg-mb5">
                            <?php if (!empty($item['domain'])): ?><img src="<?php echo esc_attr(TemplateHelper::getMerhantIconUrl($item, true)); ?>" /> <?php endif; ?><small class="text-muted title-case"><?php echo esc_html($merhant); ?></small>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item['extra']['IsEligibleForSuperSaverShipping'])): ?>
                        <div class="text-success cegg-font60 cegg-lineheight15"><?php _e('Free shipping', 'content-egg-tpl'); ?></div>
                    <?php endif; ?>    
                </div>                

            </div>        
        <?php endforeach; ?>

    </div>   
    <?php if ($module_id == 'Amazon'): ?>
        <div class="row">
            <div class="col-md-12 text-right text-muted">
                <small>
                    <?php _e('Last updated on', 'content-egg-tpl'); ?> <?php echo TemplateHelper::getLastUpdateFormatted($module_id, $post_id); ?>
                    <?php TemplateHelper::printAmazonDisclaimer(); ?>                    
                </small>
            </div>
        </div>        
    <?php endif; ?>

</div>