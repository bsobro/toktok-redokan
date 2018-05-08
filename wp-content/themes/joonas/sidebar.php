<?php
/**
 * sidebar.php
 *
 * @package theme/joonas
 **/

if( is_active_sidebar('main-sidebar') ): ?>
    <div class="main-sidebar" style="border:2px solid #f00;">
        <?php dynamic_sidebar('main-sidebar'); ?>
    </div>
<?php endif; ?>
