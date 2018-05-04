<?php

use ContentEgg\application\helpers\TemplateHelper;
?>
<?php if (!empty($item['extra']['customerReviews'])): ?>
    <?php if (!empty($item['extra']['customerReviews']['reviews'])): ?>
        <h4>
            <?php _e('Customer reviews', 'content-egg-tpl'); ?>
            <?php if (!empty($item['extra']['customerReviews']['TotalReviews'])): ?>

                <?php if ($link = TemplateHelper::getAmazonLink($item['extra']['itemLinks'], 'All Customer Reviews')): ?>
                    <small>(<a rel="nofollow" target="_blank" href="<?php echo $link; ?>">
                            <?php echo $item['extra']['customerReviews']['TotalReviews']; ?> <?php _e('customer reviews', 'content-egg-tpl'); ?>
                        </a>)</small>
                <?php endif; ?>

            <?php endif; ?>
        </h4>
        <?php foreach ($item['extra']['customerReviews']['reviews'] as $review): ?>
            <div class="cegg-review-block">
                <em><?php echo esc_html($review['Summary']); ?>, <small><?php echo TemplateHelper::formatDate($review['Date']); ?></small></em>
                <span class="rating_small">
                    <?php echo str_repeat("<span>&#x2605;</span>", (int) $review['Rating']); ?><?php echo str_repeat("<span>☆</span>", 5 - (int) $review['Rating']); ?>
                </span>
            </div>
            <blockquote><?php echo esc_html($review['Content']); ?></blockquote>
        <?php endforeach; ?>
    <?php elseif ($item['extra']['customerReviews']['HasReviews'] == 'true'): ?>
        <iframe src='<?php echo $item['extra']['customerReviews']['IFrameURL']; ?>' width='100%' height='500'></iframe>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($item['extra']['editorialReviews'])): ?>
    <?php foreach ($item['extra']['editorialReviews'] as $review): ?>
        <h4><?php echo esc_html($review['Source']); ?></h4>
        <p><?php echo $review['Content']; ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($item['extra']['comments'])): ?>
    <h4><?php _e('User reviews', 'content-egg-tpl'); ?></h4>
    <?php foreach ($item['extra']['comments'] as $key => $comment): ?>
        <div class="cegg-review-block">
            <blockquote>
                <?php if (!empty($comment['rating'])): ?>
                    <span class="rating_small">
                        <?php echo str_repeat("<span>&#x2605;</span>", (int) $comment['rating']); ?><?php echo str_repeat("<span>☆</span>", 5 - (int) $comment['rating']); ?>
                    </span>
                <?php endif; ?>
                <?php echo $comment['comment']; ?>
            </blockquote>
        </div>
    <?php endforeach; ?>
    <p class="text-right">
        <a class="btn btn-info" rel="nofollow" target="_blank" href="<?php echo esc_url($item['url']) ?>"><?php _e('View all reviews', 'content-egg-tpl'); ?></a>
    </p>
<?php endif; ?> 

<?php if (!empty($item['extra']['Reviews'])): ?>                
    <h4>
        <?php _e('Customer reviews', 'content-egg-tpl'); ?>
    </h4>                               
    <?php foreach ($item['extra']['Reviews'] as $review): ?>
        <div class="cegg-review-block">
            <em><?php if ($review['Title']): ?><?php echo esc_html($review['Title']); ?>,<?php endif; ?> <small><?php echo TemplateHelper::formatDate($review['Date']); ?></small></em>
            <span class="rating_small">
                <?php echo str_repeat("<span>&#x2605;</span>", (int) $review['Rate']); ?><?php echo str_repeat("<span>☆</span>", 5 - (int) $review['Rate']); ?>
            </span>
        </div>
        <blockquote><?php echo esc_html($review['Comment']); ?></blockquote>
    <?php endforeach; ?>
<?php endif; ?> 
