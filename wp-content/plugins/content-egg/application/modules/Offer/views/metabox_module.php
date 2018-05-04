<?php $is_woo = (\get_post_type($GLOBALS['post']->ID) == 'product') ? true : false; ?>
<div ng-controllerTMP="<?php echo $module_id; ?>Controller">

    <input type="hidden" name="cegg_data[<?php echo $module_id; ?>]" ng-value="models.<?php echo $module_id; ?>.added | json" /> 
    <input type="hidden" name="cegg_updateKeywords[<?php echo $module_id; ?>]" ng-value="updateKeywords.<?php echo $module_id; ?>" /> 

    <uib-tabset active='0'>
        <uib-tab>
            <uib-tab-heading>
                <strong><?php echo $module->getName(); ?></strong> 
                <span ng-show="models.<?php echo $module_id; ?>.added.length" class="label" ng-class="{'label-danger':models.<?php echo $module_id; ?>.added_changed, 'label-default':!models.<?php echo $module_id; ?>.added_changed}">{{models.<?php echo $module_id; ?>.added.length}}</span>
            </uib-tab-heading>

            <div class="data_panel">

                <div clas="row">
                    <div class="col-md-6 col-lg-6">
                        <input class="input-sm col-sm-6 shortcode-input" ng-model="shortcodes.<?php echo $module_id; ?>" select-on-click readonly type="text" />
                        <?php
                        $tpl_manager = ContentEgg\application\components\ModuleTemplateManager::getInstance($module_id);
                        $templates = $tpl_manager->getTemplatesList(true);
                        ?>
                        <?php if ($templates): ?>
                            <select class="input-sm col-sm-6" ng-model="selectedTemplate_<?php echo $module_id; ?>" ng-change="buildShortcode('<?php echo $module_id; ?>', selectedTemplate_<?php echo $module_id; ?>);">
                                <option value="">&larr; <?php _e('Shortcode Template', 'content-egg'); ?></option>
                                <?php foreach ($templates as $id => $name): ?>
                                    <option value="<?php echo esc_attr($id); ?>"><?php echo esc_html($name); ?></option>
                                <?php endforeach; ?>
                            </select>                        
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 col-lg-6 text-right">
                        <a class='btn btn-default btn-sm' ng-click="addBlank('<?php echo $module_id; ?>')"><i class="glyphicon glyphicon-plus"></i> <?php _e('Add offer', 'content-egg'); ?></a>
                        <a class='btn btn-default btn-sm' ng-click="deleteAll('<?php echo $module_id; ?>')" ng-confirm-click="<?php _e('Are you sure you want to delete all results?', 'content-egg'); ?>" ng-disabled='!models.<?php echo $module_id; ?>.added.length'><?php _e('Remove all', 'content-egg'); ?></a>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>

            <?php // [RESULTS] ?>

            <div ng-init="activeResultTabs['<?php echo $module_id; ?>'] = true" ui-sortable="{ 'ui-floating': true }" ng-model="models.<?php echo $module_id; ?>.added" class="row">
                <div class="col-md-12 added_data" ng-repeat="data in models.<?php echo $module_id; ?>.added">
                    <div class="row" style="padding: 5px;">
                        <div class="col-md-1" ng-if="data.img">
                            <img ng-if="data.img" ng-src="{{data.img}}" class="img-responsive" style="max-height: 100px;" />
                        </div>
                        <div ng-class="data.img ? 'col-md-9' : 'col-md-10'">
                            <div class="row" style="margin:0px;">
                                <div class="col-md-10" style="padding:0px;">
                                    <input type="text" placeholder="<?php _e('Title', 'content-egg'); ?> (<?php _e('required', 'content-egg'); ?>)" ng-model="data.title" class="form-control" style="margin-bottom: 5px;">                                    
                                </div>
                                <div class="col-md-2" style="padding-right:0px;">
                                    <select class="form-control" ng-model="data.rating" convert-to-number>
                                        <option value="1"><?php _e('Rating', 'content-egg'); ?> - 1</option>
                                        <option value="2"><?php _e('Rating', 'content-egg'); ?> - 2</option>
                                        <option value="3"><?php _e('Rating', 'content-egg'); ?> - 3</option>
                                        <option value="4"><?php _e('Rating', 'content-egg'); ?> - 4</option>
                                        <option value="5"><?php _e('Rating', 'content-egg'); ?> - 5</option>
                                    </select>                                
                                </div>
                            </div>                            

                            <div class="row" style="margin:0px;">
                                <div class="col-md-6" style="padding:0px;">
                                    <input type="text" placeholder="<?php _e('Offer URL', 'content-egg'); ?> (<?php _e('required', 'content-egg'); ?>)" ng-model="data.orig_url" class="form-control" style="margin-bottom: 5px;">
                                </div>
                                <div class="col-md-6" style="padding-right:0px;">
                                    <input type="text" placeholder="<?php _e('Deeplink', 'content-egg'); ?>" ng-model="data.extra.deeplink" class="form-control" style="margin-bottom: 5px;">
                                </div>
                            </div>                            
                            <input type="text" placeholder="<?php _e('Image URL', 'content-egg'); ?>" ng-model="data.img" class="form-control" style="margin-bottom: 5px;">
                            <div class="row" style="margin:0px;">
                                <div class="col-md-3" style="padding:0px;">
                                    <input type="text" placeholder="<?php _e('Price', 'content-egg'); ?>" ng-model="data.price" class="form-control">
                                </div>
                                <div class="col-md-3" style="padding:0px;">
                                    <input type="text" placeholder="<?php _e('Old price', 'content-egg'); ?>" ng-model="data.priceOld" class="form-control">
                                </div>
                                <div class="col-md-1" style="padding-right:0px;">
                                    <select class="form-control" ng-model="data.currencyCode">
                                        <?php foreach (\ContentEgg\application\helpers\CurrencyHelper::getCurrenciesList() as $currency): ?>
                                            <option value="<?php echo esc_attr($currency); ?>"><?php echo esc_html($currency); ?></option>
                                        <?php endforeach; ?>
                                    </select>                                
                                </div>
                                <div class="col-md-5" style="padding-right:0px;">
                                    <input type="text" placeholder="<?php _e('XPath Price Selector', 'content-egg'); ?>" ng-model="data.extra.priceXpath" class="form-control">

                                </div>                                
                            </div>                               

                            <textarea type="text" placeholder="<?php _e('Description', 'content-egg'); ?>" rows="1" ng-model="data.description" class="col-sm-12" style="margin-top: 5px;"></textarea>
                            <?php if ($is_woo && $module->isAffiliateParser()): ?>
                                <label><input ng-true-value="'true'" type="checkbox" ng-model="data.woo_sync" name="woo_sync" ng-change="wooRadioChange(data.unique_id, 'woo_sync')"> <?php _e('Woo synchronization', 'content-egg'); ?></label>
                            <?php endif; ?>
                           
                        </div>
                        <div class="col-md-2">
                            <span ng-if="data.orig_url"><a href="{{data.orig_url}}" target="_blank"><?php _e('Go to ', 'content-egg'); ?></a><br><br></span>
                            <div style="padding:0xp;margin:0px;padding-top:10px;"><a style="color:#D03300;" ng-click="delete(data, '<?php echo $module_id; ?>')"><i class="glyphicon glyphicon-remove"></i> <?php _e('Remove', 'content-egg'); ?></a></div>

                            <span ng-show="data.last_update"><?php _e('Last update: '); ?>{{data.last_update * 1000| date:'shortDate'}}</span>
                        </div>  
                    </div>
                </div>
            </div>
            <?php // [/RESULTS] ?>
        </uib-tab>        
    </uib-tabset>

</div>