<div class="search_results" ng-show="models.<?php echo $module_id; ?>.results.length > 0 && !models.<?php echo $module_id; ?>.processing">
    <div justified-gallery="{rowHeight: 160}">
        <a ng-class="{'result_added' : result.added}" ng-click="add(result, '<?php echo $module_id; ?>')" repeat-done ng-repeat="result in models.<?php echo $module_id; ?>.results">
            <img alt="{{result.title}}" ng-src="{{result.img}}"/>
        </a>                
    </div>
</div>
