    <select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.license">
        <option value="any"><?php _e('Any license', 'content-egg'); ?></option>
        <option value="creativeCommon"><?php _e('Creative Commons', 'content-egg'); ?></option>
        <option value="youtube"><?php _e('Standard license', 'content-egg'); ?></option>
    </select>
    <select class="col-md-4 input-sm" ng-model="query_params.<?php echo $module_id; ?>.order">
        <option value="date"><?php _e('Date', 'content-egg'); ?></option>
        <option value="rating"><?php _e('Rating', 'content-egg'); ?></option>
        <option value="relevance"><?php _e('Relevance', 'content-egg'); ?></option>
        <option value="title"><?php _e('Title', 'content-egg'); ?></option>
        <option value="viewCount"><?php _e('Views', 'content-egg'); ?></option>
    </select>