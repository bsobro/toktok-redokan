<?php

use ContentEgg\application\helpers\TemplateHelper;

?>

<?php if ($title): ?>
    <h3 class="cegg-shortcode-title"><?php echo esc_html($title); ?></h3>
<?php endif; ?>
<?php foreach ($items as $item): ?>

    <div class="egg-container egg-item">
        <div class="products">
            <div class="row">
                <div class="col-md-6 text-center cegg-image-container cegg-mb20">
                    <?php if ($item['img']): ?>
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>">                    
                            <img src="<?php echo $item['img']; ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h2 class="cegg-no-top-margin"><?php echo $item['title'];  ?></h2>
                    <?php  if ((int) $item['rating'] > 0 && (int) $item['rating'] <= 5): ?>
                        <div class="cegg-mb10">
                            <span class="rating"><?php
                                echo str_repeat("<span>&#x2605;</span>", (int) $item['rating']);
                                echo str_repeat("<span>☆</span>", 5 - (int) $item['rating']);
                                ?>
                            </span>
                        </div>
                    <?php elseif (!empty($item['extra']['data']['rating'])): ?>
                        <div class="cegg-mb10">
                            <span class="rating"><?php
                                echo str_repeat("<span>&#x2605;</span>", $item['extra']['data']['rating']);
                                echo str_repeat("<span>☆</span>", 5 - $item['extra']['data']['rating']);
                                ?></span>   
                        </div>  
                    <?php endif; ?>

                    <div class="cegg-price-row cegg-mb10">
                        <?php if ($item['priceOld']): ?>
                            <span class="text-muted"><strike><?php echo TemplateHelper::formatPriceCurrency($item['priceOld'], $item['currencyCode'], '<small>', '</small>'); ?></strike></span><br>
                        <?php endif; ?>

                        <?php if ($item['price']): ?>
                            <span class="cegg-price"><?php echo TemplateHelper::formatPriceCurrency($item['price'], $item['currencyCode'], '<span class="cegg-currency">', '</span>'); ?></span>
                        <?php elseif (!empty($item['extra']['toLowToDisplay'])): ?>
                            <span class="text-muted"><?php _e('Too low to display', 'content-egg-tpl'); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php $this->renderBlock('item_after_price_row', array('item' => $item)); ?>

                    <div class="cegg-btn-row cegg-mb20">
                        <a rel="nofollow" target="_blank" href="<?php echo $item['url']; ?>" class="btn btn-success cegg-btn-big cegg-mb5"><?php TemplateHelper::buyNowBtnText(); ?></a>
                        <br/>
                        <img src="<?php echo esc_attr(TemplateHelper::getMerhantIconUrl($item, true)); ?>" /> <small class="title-case"><?php TemplateHelper::getMerhantName($item, true); ?></small>
                    </div>
                    <div class="cegg-last-update-row cegg-mb15">
                        <span class="text-muted">
                            <small>
                                <?php _e('as of', 'content-egg-tpl'); ?> <?php echo TemplateHelper::getLastUpdateFormatted($module_id, $post_id); ?>
                                <?php if ($module_id == 'Amazon') TemplateHelper::printAmazonDisclaimer(); ?>
                            </small>
                        </span>                    
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="cegg-mb25">
                        <?php $this->renderPartialModule('_item_details_top', array('Flipkart'), array('item' => $item)); ?>                            
                        
                        <?php $this->renderBlock('item_features', array('item' => $item)); ?>
                        <?php if ($item['description']): ?>
                            <p><?php echo $item['description']; ?></p>
                        <?php endif; ?>                    
                        <?php $this->renderPartialModule('_item_details_bottom', array('Envato', 'Udemy'), array('item' => $item)); ?>                            
                        <?php $this->renderBlock('item_reviews', array('item' => $item)); ?>
                    </div>
                </div>
            </div>    
        </div>
    </div>
<?php endforeach; ?>