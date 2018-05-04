<?php
/*
 * Name: List (for shortcode)
 * 
 */

__('List (for shortcode)', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
?>
<div class="egg-container cegg-list-withlogos egg-list-wdgt">
    <div class="egg-listcontainer">
        <?php foreach ($items as $key => $item): ?>           
            <div class="row-products">
                <div class="col-md-2 col-sm-2 col-xs-12 cegg-image-cell">
                    <?php if ($item['img']): ?>
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                            <img src="<?php echo $item['img']; ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12 cegg-desc-cell">
                    <div class="cegg-no-top-margin cegg-list-logo-title">
                        <b><a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                                <?php echo esc_html(TemplateHelper::truncate($item['title'], 100)); ?>
                            </a></b>
                    </div>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-12 cegg-price-cell text-center">
                    <?php if ($item['_price_movers']['discount_value']): ?>
                        <div class="text-muted" title="<?php echo \esc_attr(__('as of', 'content-egg-tpl') . ' ' . TemplateHelper::formatDate($item['_price_movers']['price_old_date'])); ?>">
                            <strike class="egg-text-bold"><?php echo TemplateHelper::formatPriceCurrency($item['_price_movers']['price_old'], $item['currencyCode']); ?></strike>
                        </div> 
                        <small class="text-muted"><?php echo TemplateHelper::getDaysAgo($item['_price_movers']['price_old_date']); ?></small>

                    <?php endif; ?>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-12 cegg-price-cell text-center">
                    <div class="cegg-price-row">

                        <?php if ($item['price']): ?>
                            <div class="cegg-price">
                                <?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']); ?>
                                <i class="fa fa-question-circle-o text-muted" title="<?php echo \esc_attr(__('as of', 'content-egg-tpl') . ' ' . TemplateHelper::formatDatetime($item['last_update'])); ?>"></i>
                            </div>
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
                <div class="col-md-1 col-sm-1 col-xs-12 text-center">
                    <span class="cegg-discount-off">
                        <?php if ($item['_price_movers']['discount_value'] > 0): ?>
                            <i class="fa fa-arrow-down text-success"></i>
                            <span class="text-success">
                            <?php endif; ?>
                            <?php if ($item['_price_movers']['discount_value'] < 0): ?>
                                <i class="fa fa-arrow-up text-danger"></i>
                                <span class="text-danger">                            
                                <?php endif; ?>
                                <?php if ($item['_price_movers']['discount_percent'] != 0): ?>
                                    <?php echo $item['_price_movers']['discount_percent']; ?>%
                                <?php endif; ?>     
                            </span>
                        </span>
                        <?php if ($item['_price_movers']['discount_value']): ?>
                            <small class="text-muted">
                                <?php
                                if ($item['_price_movers']['discount_value'] > 0)
                                    echo '-';
                                else
                                    echo '+';
                                ?><?php echo TemplateHelper::formatPriceCurrency($item['_price_movers']['discount_value'], $item['currencyCode']); ?>
                            </small>
                        <?php endif; ?>
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
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>