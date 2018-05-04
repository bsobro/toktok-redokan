<?php if (\ContentEgg\application\Plugin::isFree() || \ContentEgg\application\Plugin::isInactiveEnvato()): ?>
    <div class="cegg-maincol">
    <?php endif; ?>
    <div class="wrap">
        <h2>
            <?php _e('Export/Import of settings', 'content-egg'); ?>
        </h2>

        <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif; ?>

        <div id="poststuff">    
            <p>
            </p>    
        </div>    

        <h3><?php _e('Save settings', 'content-egg'); ?></h3>
        <p><?php _e('To import all settings, copy field value (Ctrl + C on Win) and make import on new site', 'content-egg'); ?></p>
        <textarea rows="8" cols="70" onclick="this.focus();
                this.select()" readonly="readonly"><?php echo esc_html($export_str); ?></textarea>

        <br><br>
        <h3><?php _e('Load settings', 'content-egg'); ?></h3>
        <p><?php _e('Copy settings from another site and click on  "Import".', 'content-egg'); ?></p>
        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo $nonce; ?>"/>
            <textarea name="import_str" rows="8" cols="70"></textarea>                        
            <p><input type="submit" value="<?php _e('Import', 'content-egg'); ?>" id="config_submit" class="button-primary" name="submit"></p>
        </form>
    </div>
    <?php if (\ContentEgg\application\Plugin::isFree() || \ContentEgg\application\Plugin::isInactiveEnvato()): ?>
    </div>    
    <?php include('_promo_box.php'); ?>
<?php endif; ?>  