<?php 
	$form = $this->db->get($_GET['form']);
	$form_post_type = $form['post_type'];
	$form_fields = $form['fields'];
	$form_settings = $form['settings'];
	$form_extended = $form['extended'];
?>
<div class="wrap">
	<h2><?php echo stripslashes($form['name']); ?> <?php _e( "Settings", "wpfepp-plugin" ); ?> <img id="wpfepp-loading" src="<?php echo plugins_url( 'static/img/loading.gif', dirname( dirname (__FILE__ ) ) ); ?>" /></h2>
	<p><code>[wpfepp_submission_form form="<?php echo $_GET['form']; ?>"]</code> - <span class="description"><?php _e( "Use this shortcode to include submit form on page", "wpfepp-plugin" ) ?></span></p>
	<p><code>[wpfepp_post_table form="<?php echo $_GET['form']; ?>" show_all=0]</code> -  
	<span class="description"><?php _e( "Use this shortcode to include full post list for author with edit, delete links. Delete 'show_all=0' if you want to show all posts of user (including posts submitted with another forms)", "wpfepp-plugin" ) ?></span></p>
	<?php $this->tabs->display(); ?>
</div>