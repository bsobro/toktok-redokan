<?php
/*
  Name: Simple list
 */

?>
<?php if (isset($title) && $title): ?>
    <h3 class="cegg-shortcode-title"><?php echo esc_html($title); ?></h3>
<?php endif; ?>
<?php include(rh_locate_template('inc/ce_common/data_simple_list.php')); ?>