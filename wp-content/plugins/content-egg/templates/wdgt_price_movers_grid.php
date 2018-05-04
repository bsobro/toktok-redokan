<?php
/*
 * Name: Grid
 * 
 */

__('Grid', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;

if ($is_shortcode)
    $cols = 2;
else
    $cols = 1;
?>


<div class="egg-container egg-grid egg-grid-wdgt">
    <div class="row egg-grid-wdgt-row">
        <?php $i = 0; ?>        
        <?php foreach ($items as $key => $item): ?>      

            <div class="col-md-<?php echo 12 / $cols; ?> cegg-wdgt-gridbox">
                <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                    <div class="cegg-thumb cegg-mb15">
                        <div class="cegg-position-container">
                            <span class="cegg-position-text"><?php echo $key + 1; ?></span>
                        </div>

                        <?php if ($item['img']): ?>
                            <img src="<?php echo \esc_attr($item['img']) ?>" alt="<?php echo \esc_attr($item['title']); ?>" />
                        <?php endif; ?>
                    </div>                            
                    <h5 title="<?php echo \esc_html($item['title']); ?>"><?php echo esc_html(TemplateHelper::truncate($item['title'], 100)); ?></a></h5>                
                </a>
                <div class="row cegg-mb15">
                    <?php if ($item['_price_movers']['discount_percent'] > 0): ?>
                        <div class="col-xs-2 cegg-product-discount">
                            <span class="product-discount-value"><?php echo $item['_price_movers']['discount_percent']; ?><span class="product-discount-symbol">%</span></span>                
                            <div class="product-discount-off">
                                <?php _e('OFF', 'content-egg-tpl'); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-xs-7">
                        <?php if ($item['_price_movers']['discount_value']): ?>
                            <span class="product-price-old text-muted" title="<?php echo \esc_attr(__('as of', 'content-egg-tpl') . ' ' . TemplateHelper::formatDate($item['_price_movers']['price_old_date'])); ?>">
                                <?php echo TemplateHelper::formatPriceCurrency($item['_price_movers']['price_old'], $item['currencyCode']); ?>
                            </span> 
                        <?php endif; ?>
                        <?php if ($item['price']): ?>
                            <div class="product-price-new">
                                <?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']); ?>
                                <?php if ($item['_price_movers']['discount_value'] > 0): ?>
                                    <i class="fa fa-arrow-down text-success"></i>
                                <?php endif; ?>
                                <?php if ($item['_price_movers']['discount_value'] < 0): ?>
                                    <i class="fa fa-arrow-up text-danger"></i>
                                <?php endif; ?>
                                <i class="fa fa-question-circle-o text-muted" title="<?php echo \esc_attr(__('as of', 'content-egg-tpl') . ' ' . TemplateHelper::formatDatetime($item['_price_movers']['create_date'])); ?>"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-xs-3 cegg-image-cell">
                        <?php if ($logo = TemplateHelper::getMerhantLogoUrl($item, true)): ?>
                            <img class="cegg-merhant-logo" src="<?php echo esc_attr($logo); ?>" title="<?php echo esc_attr($item['domain']); ?>" alt="<?php echo esc_attr($item['domain']); ?>" />
                        <?php endif; ?>
                    </div>                            
                </div>                        
            </div>
            <?php $i++; ?>
            <?php if ($i % $cols == 0 && $i < count($items) - 1): ?>
                <div class="clearfix"></div>
            <?php endif; ?>             

        <?php endforeach; ?>
    </div>
</div>
