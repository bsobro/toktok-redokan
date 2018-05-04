<?php

use ContentEgg\application\helpers\TemplateHelper;
?>

<?php if ($title): ?>
    <h4><?php echo \esc_html($title); ?></h4>
<?php else: ?>
    <h4><?php _e('Price History', 'content-egg-tpl'); ?></h4>
<?php endif; ?>

<?php TemplateHelper::priceHistoryMorrisChart($item['unique_id'], $module_id, 180, array('lineWidth' => 2, 'postUnits' => ' ' . $item['currencyCode'], 'goals' => array((int) $item['price']), 'fillOpacity' => 0.5), array('style' => 'height: 220px;')); ?>

<?php $prices = TemplateHelper::priceHistoryPrices($item['unique_id'], $module_id, $limit = 5); ?>
<?php if (!$prices) return; ?>
<div class="row">
    <div class='col-md-7'>
        <h4><?php _e('Statistics', 'content-egg-tpl'); ?></h4>
        <table class="table table-hover">
            <tr>
                <td><?php _e('Current Price', 'content-egg-tpl'); ?></td>
                <td >
                    <?php if ($item['price']): ?>
                        <?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']); ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?php echo TemplateHelper::getLastUpdateFormatted($module_id, $post_id, false); ?></td>
            </tr>
            <?php $price = TemplateHelper::priceHistoryMax($item['unique_id'], $module_id); ?>
            <tr>
                <td class="text-danger"><?php _e('Highest Price', 'content-egg-tpl'); ?></td>
                <td><?php echo TemplateHelper::formatPriceCurrency($price['price'], $item['currencyCode']); ?></td>
                <td><?php echo TemplateHelper::formatDate($price['date']); ?></td>
            </tr>
            <?php $price = TemplateHelper::priceHistoryMin($item['unique_id'], $module_id); ?>
            <tr>
                <td class="text-success"><?php _e('Lowest Price', 'content-egg-tpl'); ?></td>
                <td><?php echo TemplateHelper::formatPriceCurrency($price['price'], $item['currencyCode']); ?></td>
                <td><?php echo TemplateHelper::formatDate($price['date']); ?></td>
            </tr>
        </table>
        <?php $since = TemplateHelper::priceHistorySinceDate($item['unique_id'], $module_id); ?>
        <div class='text-right text-muted'><?php _e('Since', 'content-egg-tpl'); ?> <?php echo TemplateHelper::formatDate($since); ?></div>
    </div>
    <div class='col-md-5'>
        <h4><?php _e('Last price changes', 'content-egg-tpl'); ?></h4>
        <table class="table table-hover table-condensed">
            <?php foreach ($prices as $price): ?>
                <tr>
                    <td><?php echo TemplateHelper::formatPriceCurrency($price['price'], $item['currencyCode']); ?></td>
                    <td><?php echo TemplateHelper::formatDate($price['date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>
</div>