<?php

use ContentEgg\application\components\ModuleManager;

$module = ModuleManager::factory($module_id);
$is_woo = (\get_post_type($GLOBALS['post']->ID) == 'product') ? true : false;
?>

<div class="data_results" ng-if="models.<?php echo $module_id; ?>.added.length">
    <div ui-sortable="{ 'ui-floating': true }" ng-model="models.<?php echo $module_id; ?>.added" class="row">
        <div class="col-md-12 added_data" ng-repeat="data in models.<?php echo $module_id; ?>.added">
            <div class="row" style="padding:0xp;margin:0px;padding-bottom:10px;">
                <div class="col-md-1 text-center" ng-if="data.img">
                    <img ng-src="{{data.img}}" class="img-responsive" style="max-height: 100px;" />
                    <small ng-show="data.price"><b>{{data.currencyCode}} {{data.price}}</b></small>
                </div>
                <div ng-class="data.img ? 'col-md-9' : 'col-md-10'">
                    <input type="text" placeholder="<?php _e('Title', 'content-egg'); ?>" ng-model="data.title" class="col-md-10" style="margin-bottom: 5px;">
                    <input type="text" placeholder="<?php _e('Domain', 'content-egg'); ?>" ng-model="data.domain" class="col-md-2" style="margin-bottom: 5px;">
                    <textarea type="text" placeholder="<?php _e('Description', 'content-egg'); ?>" rows="1" ng-model="data.description" class="col-sm-12"></textarea>

                    <?php if ($is_woo && $module->isAffiliateParser()): ?>
                        <label><input ng-true-value="'true'" type="checkbox" ng-model="data.woo_sync" name="woo_sync" ng-change="wooRadioChange(data.unique_id, 'woo_sync')"> <?php _e('Woo synchronization', 'content-egg'); ?></label>
                        &nbsp;&nbsp;&nbsp;
                        <label ng-show="data.features.length"><input ng-true-value="'true'" type="checkbox" ng-model="data.woo_attr" name="woo_attr" ng-change="wooRadioChange(data.unique_id, 'woo_attr')"> <?php _e('Woo attributes', 'content-egg'); ?> ({{data.features.length}})</label>
                    <?php elseif ($module->isAffiliateParser()): ?>
                        <small class="text-muted" ng-show="data.features.length"><?php _e('Attributes:', 'content-egg'); ?> {{data.features.length}}</small>
                    <?php endif; ?>

                    <a ng-show="data.features.length" ng-init="isFeaturesCollapsed = true" ng-click="isFeaturesCollapsed = !isFeaturesCollapsed" aria-label="Edit">
                        <span class="glyphicon glyphicon-edit"></span>
                    </a>

                    <div class="row features_wrap" uib-collapse="isFeaturesCollapsed">
                        <div class="col-md-12" ng-repeat="feature in data.features">
                            <div class="col-md-5">
                                <input type="text" ng-model="feature.name" class="input-sm form-control">
                            </div>
                            <div class="col-md-6">
                                <input type="text" ng-model="feature.value" class="input-sm form-control">                            
                            </div>
                            <div class="col-md-1">
                                <a ng-click="data.features.splice($index, 1)" aria-label="Delete">
                                    <span class="glyphicon glyphicon-remove-circle text-danger"></span>
                                </a>
                            </div>
                        </div>           
                    </div>

                </div>
                <div class="col-md-2">
                    <div>                        
                        <span ng-show="data.domain"><img src="https://www.google.com/s2/favicons?domain={{data.domain}}"> {{data.domain}}</span><span ng-hide="data.domain"><?php _e('Go to ', 'content-egg'); ?></span> 
                        <a title="Go to" href="{{data.url}}" target="_blank">
                            <i class="glyphicon glyphicon-share"></i>
                        </a>
                    </div>
                    <div style="padding:0xp;margin:0px;padding-top:10px;"><a style="color:#D03300;" ng-click="delete(data, '<?php echo $module_id; ?>')"><i class="glyphicon glyphicon-remove"></i> <?php _e('Remove', 'content-egg'); ?></a></div>
                    <div style="padding:0xp;margin:0px;">
                        <?php /* <small class="text-muted" ng-show="data.ean"><br><?php _e('EAN:'); ?> {{data.ean}}</small> */ ?>
                        <small class="text-muted" ng-show="data.last_update">
                            <br><i class="glyphicon glyphicon-time"></i> <?php _e('Last update:'); ?> {{data.last_update * 1000| date:'shortDate'}}
                        </small>
                    </div>
                </div>  
            </div>

        </div>
    </div>
</div>