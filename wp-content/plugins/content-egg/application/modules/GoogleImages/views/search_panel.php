<select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.rights">
    <option value=""><?php _e('Any license', 'content-egg'); ?></option>
    <option value="(cc_publicdomain|cc_attribute|cc_sharealike|cc_noncommercial|cc_nonderived)"><?php _e('Any Creative Commons', 'content-egg'); ?></option>
    <option value="(cc_publicdomain|cc_attribute|cc_sharealike|cc_nonderived).-(cc_noncommercial)"><?php _e('With Allow of commercial use', 'content-egg'); ?></option>
    <option value="(cc_publicdomain|cc_attribute|cc_sharealike|cc_noncommercial).-(cc_nonderived)"><?php _e('Allowed change', 'content-egg'); ?></option>
    <option value="(cc_publicdomain|cc_attribute|cc_sharealike).-(cc_noncommercial|cc_nonderived)"><?php _e('Commercial use and change', 'content-egg'); ?></option>
</select>
