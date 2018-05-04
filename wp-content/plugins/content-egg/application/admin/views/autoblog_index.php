<div id="cegg_waiting_products" style="display:none; text-align: center;"> 
    <h2><?php _e('Work of autoblogging', 'content-egg'); ?></h2> 
    <p>
        <img src="<?php echo \ContentEgg\PLUGIN_RES; ?>/img/egg_waiting.gif" />
        <br>
        <?php _e('Please, wait for end of autoblogging work', 'content-egg'); ?>

    </p>
</div>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(document).ready(function() {
        $j('.run_avtoblogging').click(function() {
            $j.blockUI({message: $j('#cegg_waiting_products')});
            test();
        });
    });
</script>
<?php
$table->prepare_items();

$message = '';
if ($table->current_action() == 'delete' && !empty($_GET['id']))
    $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Deleted tasks for autoblogging: ', 'content-egg') . ' %d', count($_GET['id'])) . '</p></div>';
if ($table->current_action() == 'run')
    $message = '<div class="updated below-h2" id="message"><p>' . __('Autoblogging finished tasks', 'content-egg') . '</p></div>';
?>

<?php if (\ContentEgg\application\Plugin::isFree() || \ContentEgg\application\Plugin::isInactiveEnvato()): ?>
    <div class="cegg-maincol">
    <?php endif; ?>


    <div class="wrap">

        <h2>
            <?php _e('Autoblogging', 'content-egg'); ?>
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=content-egg-autoblog-edit'); ?>"><?php _e('Add autoblogging', 'content-egg'); ?></a>
        </h2>
        <?php echo $message; ?>

        <div id="poststuff">    
            <p>
                <?php _e('You can create automatic creating of posts with autoblogging', 'content-egg'); ?>
            </p>        
        </div>    

        <form id="eggs-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display() ?>
        </form>
    </div>

    <?php if (\ContentEgg\application\Plugin::isFree() || \ContentEgg\application\Plugin::isInactiveEnvato()): ?>
    </div>    
    <?php include('_promo_box.php'); ?>
<?php endif; ?>        