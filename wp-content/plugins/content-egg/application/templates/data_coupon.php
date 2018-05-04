<?php

use ContentEgg\application\helpers\TemplateHelper;
?>

<div class="egg-container egg-list egg-list-coupons">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <div class="egg-listcontainer">
        <?php foreach ($items as $item): ?>
            <div class="row-products">
                <div class="col-md-9 col-sm-9 col-xs-12 cegg-desc-cell">
                    <h4 class="cegg-no-top-margin">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">
                            <?php echo esc_html($item['title']); ?>
                        </a>
                    </h4>   
                    <?php if ($item['description']): ?>
                        <div class="small text-muted cegg-lineh-20"><?php echo esc_html($item['description']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item['extra']['discount'])): ?>
                        <span class="label label-success">
                            <?php echo esc_html($item['extra']['discount']); ?> <?php _e('OFF', 'content-egg-tpl') ?>
                        </span>&nbsp;
                    <?php endif; ?>

                    <?php if ($module_id == 'TradedoublerCoupons' && $item['extra']['discountAmount']): ?>
                        <span class="label label-success">
                            <?php if (!(bool) $item['extra']['isPercentage']) echo TemplateHelper::currencyTyping($item['extra']['currencyId']); ?><?php echo esc_html($item['extra']['discountAmount']); ?><?php if ((bool) $item['extra']['isPercentage']) echo '%'; ?> 
                            <?php _e('OFF', 'content-egg-tpl') ?>
                        </span>
                    <?php endif; ?>

                    <?php if ($item['startDate']): ?>
                        <span class="text-muted small text-center"><em><?php _e('Start date:', 'content-egg-tpl'); ?> <?php echo TemplateHelper::formatDate($item['startDate']); ?></em></span>
                    <?php endif; ?>    
                    <?php if ($item['endDate']): ?>
                        <span class="text-muted small text-center"><em><?php _e('End date:', 'content-egg-tpl'); ?> <?php echo TemplateHelper::formatDate($item['endDate']); ?></em></span>
                    <?php endif; ?>    
                </div>
                <div class="col-md-3 col-sm-3 col-xs-12 offer_price cegg-price-cell">
                    <?php if ($item['img']): ?>
                        <?php $item['img'] = str_replace('http://', '//', $item['img']); ?>
                        <div class="cegg-thumb">
                            <img src="<?php echo $item['img']; ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                        </div>
                    <?php endif; ?>    
                    <?php if ($item['code']): ?>
                        <div class="cegg-coupon-row cegg-mb10">
                            <span class="cegg-couponcode"><?php echo esc_html($item['code']); ?></span>       
                        </div>
                    <?php endif; ?>         
                    <div class="cegg-btn-row cegg-mb10">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>" class="btn btn-success"><?php TemplateHelper::couponBtnText(); ?></a> 

                        <?php if ($merhant = TemplateHelper::getMerhantName($item)): ?>
                            <div class="cegg-mb5">
                                <?php if (!empty($item['domain'])): ?><img src="<?php echo esc_attr(TemplateHelper::getMerhantIconUrl($item, true)); ?>" /> <?php endif; ?><small class="text-muted title-case"><?php echo esc_html($merhant); ?></small>
                            </div>
                        <?php endif; ?>

                    </div> 
                </div>
            </div>        
        <?php endforeach; ?>
    </div>           
</div>



