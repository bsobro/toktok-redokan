<?php
/*
 * Name: Price alert for lowest price product
 * Modules:
 * Module Types: PRODUCT
 * 
 */

__('Price alert for lowest price product', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
use ContentEgg\application\helpers\TextHelper;
?>

<?php
$all_items = TemplateHelper::sortAllByPrice($data);
$item = $all_items[0];
$module_id = $item['module_id'];
if (!$title)
    $title = __('Set Alert for', 'content-egg-tpl') . ' ' . TextHelper::truncate($item['title'], 100) . ' - ' . TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode']);
?>

<div class="egg-container cegg-price-tracker-item">
    <div class="panel panel-default cegg-price-tracker-panel panel-warning">
        <div class="panel-heading"><i class="fa fa-bell-o" aria-hidden="true"></i> <?php _e('Create Your Free Price Drop Alert!', 'content-egg-tpl');?></div>                        
        <div class="panel-body">
            <?php $this->renderBlock('price_alert_inline', array('item' => $item, 'module_id' => $module_id, 'title' => $title)); ?>
        </div>
    </div>
</div>