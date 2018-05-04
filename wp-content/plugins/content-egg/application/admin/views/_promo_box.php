<div class="cegg-rightcol">
    <?php if (\ContentEgg\application\Plugin::isFree()): ?>
        <div class="cegg-box" style="margin-top: 95px;">
            <h2><?php _e('Maximum profit with minimum efforts', 'content-egg'); ?></h2>

            <a href="<?php echo ContentEgg\application\Plugin::pluginSiteUrl(); ?>">
                <img src="<?php echo ContentEgg\PLUGIN_RES; ?>/img/ce_pro_header.png" class="cegg-imgcenter" />        
            </a>
            <?php
            /*
            <a href="<?php echo ContentEgg\application\Plugin::pluginSiteUrl(); ?>">
                <img src="<?php echo ContentEgg\PLUGIN_RES; ?>/img/ce_pro_coupon.png" class="cegg-imgcenter" />
            </a>
             *
             */
            ?>
            <h4><?php _e('Many additional modules and extended functions.', 'content-egg'); ?></h4>
            <p>
                <a target="_blank" class="button-cegg-banner" href="<?php echo ContentEgg\application\Plugin::pluginSiteUrl(); ?>">Get it now!</a>
            </p>
        </div>
    

        <?php /*
        <div class="cegg-box" style="margin-top: 15px;">
            <?php _e('Thanks for use this plugin!', 'content-egg'); ?><br>
            <?php _e('If you like it and want to thank, you can write a 5 star review on Wordpress.', 'content-egg'); ?>
            <p>
                <a class="button button-primary" href="https://wordpress.org/support/plugin/content-egg/reviews/#new-post"><?php _e('Rate', 'content-egg'); ?> &#x2605; &#x2605; &#x2605; &#x2605; &#x2605;</a>
            </p>
        </div>
         * 
         */
        ?>
    <?php endif; ?>
    <?php if (\ContentEgg\application\Plugin::isEnvato()): ?>
        <div class="cegg-box" style="margin-top: 95px;">
            <h2><?php _e('Activate plugin', 'content-egg'); ?></h2>
            <p><?php _e('In order to receive all benefits of Contennt Egg, you need to activate your copy of the plugin.', 'content-egg'); ?></p>
            <p><?php _e('By activating Contennt Egg license you will unlock premium options - direct plugin updates, access to user panel and official support.', 'content-egg'); ?></p>
            <p>
                <a class="button-cegg-banner" href="<?php echo get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-lic'); ?>"><?php _e('Go to ', 'content-egg'); ?></a>
            </p>
        </div>
    <?php endif; ?>
</div>
