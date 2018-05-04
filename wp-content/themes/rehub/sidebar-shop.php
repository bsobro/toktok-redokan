<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<aside class="sidebar">            
    <!-- SIDEBAR WIDGET AREA -->
	<?php if ( is_active_sidebar( 'wooshopsidebar' ) ) : ?>
		<?php dynamic_sidebar( 'wooshopsidebar' ); ?>
	<?php else : ?>
		<p><?php _e('No woocommerce widgets added', 'rehub_framework'); ?></p>
	<?php endif; ?>        
</aside>