<?php
/*
 * Name: Price comparison widget
 * Modules:
 * Module Types: PRODUCT
 * 
 */

__('Price comparison widget', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
?>

<?php
$all_items = TemplateHelper::sortAllByPrice($data, $order);
$amazon_last_updated = TemplateHelper::getLastUpdateFormattedAmazon($data);
?>

<div class="egg-container">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <table class="cegg-price-comparison table table-hover table-condensed table-striped">
        <tbody>
            <?php foreach ($all_items as $key => $item): ?>           
                <tr>
                    <td class="cegg-merhant_col">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">                    
                            <?php $merhant_ico = TemplateHelper::getMerhantIconUrl($item, true); ?>
                            <?php if ($merhant_ico): ?><img src="<?php echo esc_attr($merhant_ico); ?>" alt="<?php echo esc_attr($item['domain']); ?>" /><?php endif; ?>
                            <?php if ($merhant = TemplateHelper::getMerhantName($item)): ?>
                                <span class="title-case"> <?php echo esc_html($merhant); ?></span>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td class="cegg-price_col text-center">
                        <?php if ($item['price']): ?>
                            <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">                    
                                <?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']); ?>
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
                            </a>

                        <?php endif; ?>
                    </td>
                    <td class="cegg-buttons_col">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">                    
                            <?php TemplateHelper::buyNowBtnText(); ?>
                        </a>
                    </td>


                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($amazon_last_updated): ?>
        <div class="cegg-font60 cegg-lineheight15 text-right"><?php _e('Last Amazon price update was:', 'content-egg-tpl'); ?> <?php echo $amazon_last_updated; ?></div>
    <?php endif; ?>
    <div class="cegg-mb15"></div>
</div>