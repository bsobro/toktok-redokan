<?php
/*
 * Name: Grid without price (4 column)
 * Modules:
 * Module Types: PRODUCT
 * 
 */

__('Grid without price (4 column)', 'content-egg-tpl');

use ContentEgg\application\helpers\TemplateHelper;
?>
<div class="egg-container egg-grid">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>

    <div class="container-fluid">
        <?php $i = 0; ?>
        <div class="row">
            <?php foreach ($data as $module_id => $items): ?>

                <?php foreach ($items as $item): ?>
                    <div class="col-md-3 cegg-gridbox"> 
                        <a rel="nofollow" target="_blank" href="<?php echo esc_url($item['url']) ?>">

                            <div class="cegg-thumb">
                                <?php if ($item['img']): ?>
                                    <img src="<?php echo esc_attr($item['img']) ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                                <?php endif; ?>
                            </div>

                            <div class="producttitle small">
                                <?php echo esc_html(TemplateHelper::truncate($item['title'], 80)); ?>
                            </div>

                        </a>
                    </div>
                    <?php
                    $i++;
                    if ($i % 4 == 0):
                        ?>
                        <div class="clearfix"></div>
                    <?php endif; ?>             
                <?php endforeach; ?>  
            <?php endforeach; ?>  

        </div>
    </div>


</div>
