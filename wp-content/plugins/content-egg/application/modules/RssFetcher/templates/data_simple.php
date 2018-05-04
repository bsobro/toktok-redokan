<?php
/*
  Name: Simple
 */
__('Simple', 'content-egg');
?>
<?php \wp_enqueue_style('egg-bootstrap'); ?>

<div class="egg-container egg-media">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>    

    <?php foreach ($items as $item): ?>
        <div class="media">
            <div class="media-body">
                <h4 class="media-heading">
                    <a target="_blank" rel="nofollow" href="<?php echo $item['url']; ?>"><?php echo esc_html($item['title']); ?></a>
                </h4>
                <p><?php echo $item['description']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>