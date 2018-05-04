<?php
/*
 * Name: Price statistics
 * Modules:
 * Module Types: PRODUCT
 * Shortcoded: FALSE
 * 
 */

__('Price statistics', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
?>
<?php
$data = TemplateHelper::mergeData($data);
$data = TemplateHelper::sortByPrice($data);
$min_price_item = TemplateHelper::getMinPriceItem($data);
$max_price_item = TemplateHelper::getMaxPriceItem($data);
if (!$min_price_item)
    return;
$shops_list = TemplateHelper::getShopsList($data);

$perc_difference = round(($max_price_item['price'] - $min_price_item['price']) * 100 / $max_price_item['price']);
?>

<div class="egg-container">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <div class="well-lg">
        <ul>
            <?php if ($commonCurrency = TemplateHelper::getCommonCurrencyCode($data)): ?>
                <li><?php echo sprintf(__('All prices mentioned above are in %s.', 'content-egg-tpl'), __(TemplateHelper::getCurrencyName($commonCurrency), 'content-egg-tpl')); ?></li>
            <?php endif; ?>
			<?php if($min_price_item['price']): ?>
				<li><?php echo sprintf(__('This product is available in %s.', 'content-egg-tpl'), join(', ', $shops_list)); ?></li>
            <?php endif; ?>				
            <li><?php echo sprintf(__('At %s you can purchase %s for only %s', 'content-egg-tpl'), $min_price_item['domain'], $min_price_item['title'], TemplateHelper::formatPriceCurrency($min_price_item['price'], $min_price_item['currencyCode'])); ?><?php if ($perc_difference && $min_price_item['domain'] != $max_price_item['domain']): ?>, <?php echo sprintf(__('which is %s%% less than the cost in %s (%s).', 'content-egg-tpl'), $perc_difference, $shops_list[$max_price_item['domain']], TemplateHelper::formatPriceCurrency($max_price_item['price'], $max_price_item['currencyCode'])); ?><?php endif; ?></li>
            <li><?php echo sprintf(__('The lowest price of %s was obtained on %s.', 'content-egg-tpl'), $max_price_item['title'], TemplateHelper::getLastUpdateFormatted($min_price_item['module_id'], $post_id)); ?></li>
        </ul>

    </div>
</div>