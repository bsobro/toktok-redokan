    <select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.offer_type">
        <option value=""><?php _e('All', 'content-egg'); ?></option>
        <option value="coupon"><?php _e('Coupon', 'content-egg'); ?></option>
        <option value="sweepstake"><?php _e('Sweepstake', 'content-egg'); ?></option>
        <option value="hot_product"><?php _e('Hot product', 'content-egg'); ?></option>
        <option value="sale"><?php _e('Sales', 'content-egg'); ?></option>
        <option value="free_shipping"><?php _e('Free shipping', 'content-egg'); ?></option>
        <option value="seasonal"><?php _e('Seasonal', 'content-egg'); ?></option>
    </select>