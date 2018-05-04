<?php \wp_nonce_field('contentegg_metabox', 'contentegg_nonce'); ?>

<div clas="row">
    <div class="col-md-11 col-lg-5">
        <div class="input-group">

            <input ng-disabled="processCounter" type="text" ng-model="global_keywords" select-on-click on-enter="global_findAll()" class="form-control col-md-6" placeholder="<?php _e('Keyword to search', 'content-egg'); ?>" aria-label="<?php _e('Keyword to search', 'content-egg'); ?>">
            <div class="input-group-btn">
                <button ng-disabled='processCounter || !global_keywords' ng-click="global_findAll()" type="button" class="btn btn-info" aria-label="Find ">
                    <?php _e('Find all', 'content-egg'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-lg-6">
        <?php
        $tpl_manager = ContentEgg\application\components\BlockTemplateManager::getInstance();
        $templates = $tpl_manager->getTemplatesList(true);
        ?>

        <input class="input-sm col-sm-6 shortcode-input" ng-model="blockShortcode" select-on-click readonly type="text" />                
        <select class="input-sm col-sm-6" ng-init="selectedBlockTemplate = '<?php echo key($templates); ?>'; buildBlockShortcode();" ng-model="selectedBlockTemplate" ng-change="buildBlockShortcode();">
            <?php foreach ($templates as $id => $name): ?>
                <option value="<?php echo esc_attr($id); ?>"><?php echo esc_html($name); ?></option>
            <?php endforeach; ?>
        </select>                        
    </div>

    <div class="col-sm-1 text-right">
        <button ng-show='!processCounter && global_isSearchResults()' ng-click="global_addAll()" type="button" class="btn btn-default btn-sm"><?php _e('Add all', 'content-egg'); ?></button>
        <button ng-show='global_isAddedResults()' ng-click="global_deleteAll()" ng-confirm-click="<?php _e('Are you sure you want to delete the results of all modules?', 'content-egg'); ?>" type="button" class="btn btn-default btn-sm"><?php _e('Remove all', 'content-egg'); ?></button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12"><hr></div>
</div>