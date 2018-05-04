<?php

use ContentEgg\application\helpers\TemplateHelper;
?>
<?php if (!TemplateHelper::isPriceAlertAllowed($item['unique_id'], $module_id)) return; ?>

<?php
$desired_price = '';
//if ($item['price'])
    //$desired_price = ceil($item['price'] * 90 / 100); // -10%

if (empty($input_class))
    $input_class = '';
if (empty($btn_class))
    $btn_class = '';
?>


<div class="cegg-price-alert-wrap">
    <?php if ($title): ?>
        <div class="price-alert-title cegg-mb10"><?php echo \esc_html($title); ?></div>
    <?php else: ?>
        <div class="price-alert-title cegg-mb5"><?php _e('Wait For A Price Drop', 'content-egg-tpl'); ?></div>
    <?php endif; ?>
    <div class="row cegg-no-bottom-margin">
        <form class="navbar-form">
            <input type="hidden" name="module_id" value="<?php echo esc_attr($module_id); ?>">
            <input type="hidden" name="unique_id" value="<?php echo esc_attr($item['unique_id']); ?>">
            <input type="hidden" name="post_id" value="<?php echo esc_attr(get_the_ID()); ?>">                                
            <div class="col-md-6">
                <label class="sr-only" for="cegg-email-<?php echo esc_attr($item['unique_id']); ?>"><?php _e('Your Email', 'content-egg-tpl'); ?></label>
                <input value="<?php echo esc_attr(TemplateHelper::getCurrentUserEmail()); ?>" type="email" class="<?php echo esc_attr($input_class);?> form-control" name="email" id="cegg-email-<?php echo esc_attr($item['unique_id']); ?>" placeholder="<?php _e('Your Email', 'content-egg-tpl'); ?>" required>
            </div>     
            <div class="col-md-6">
                <label class="sr-only" for="cegg-price-<?php echo esc_attr($item['unique_id']); ?>"><?php _e('Desired Price', 'content-egg-tpl'); ?></label>
                <div class="input-group">
                    <?php $cur_position = TemplateHelper::getCurrencyPos($item['currencyCode']); ?>
                    <?php if ($cur_position == 'left' || $cur_position == 'left_space'): ?>
                        <div class="input-group-addon"><?php echo TemplateHelper::getCurrencySymbol($item['currencyCode']); ?></div>
                    <?php endif; ?>
                    <input value="<?php echo $desired_price; ?>" type="number" class="<?php echo esc_attr($input_class);?> form-control" name="price" id="cegg-price-<?php echo esc_attr($item['unique_id']); ?>" placeholder="<?php _e('Desired Price', 'content-egg-tpl'); ?>" step="any" required>
                    <?php if ($cur_position == 'right' || $cur_position == 'right_space'): ?>
                        <div class="input-group-addon"><?php echo TemplateHelper::getCurrencySymbol($item['currencyCode']); ?></div>
                    <?php endif; ?>
                    <span class="input-group-btn">
                        <button class="btn btn-warning <?php echo esc_attr($btn_class);?>" type="submit"><?php _e('SET ALERT', 'content-egg-tpl'); ?></button>
                    </span>

                </div>                                          
            </div>
            <div class="col-md-12">            
                <div class="text-muted small cegg-mt5"><?php _e('You will receive a notification when the price drops.', 'content-egg-tpl'); ?></div>        
            </div>
        </form>
    </div>
        

    <div class="cegg-price-loading-image" style="display: none;"><img src="<?php echo \ContentEgg\PLUGIN_RES . '/img/ajax-loader.gif' ?>" /></div>
    <div class="cegg-price-alert-result-succcess text-success" style="display: none;"></div>
    <div class="cegg-price-alert-result-error text-danger" style="display: none;"></div>
</div>
