<?php
/*
  Name: Large
 */
__('Large', 'content-egg-tpl');
?>

<?php \wp_enqueue_style('egg-bootstrap'); ?>

<div class="egg-container egg-video">
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>    

    <?php foreach ($items as $item): ?>
        <h4><?php echo esc_html($item['title']); ?></h4>
        <div class="embed-responsive embed-responsive-16by9">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $item['extra']['guid']; ?>" frameborder="0" allowfullscreen></iframe>
        </div>
        <?php if ($item['description']): ?>
            <p><?php echo $item['description']; ?></p>
        <?php endif; ?>    
    <?php endforeach; ?>
</div>