<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php global $post;?>
<?php $image = (isset($image)) ? $image : '';?>
<?php $nometa = (isset($nometa)) ? $nometa : '';?>
<?php $border = (isset($border)) ? $border : '';?>
<div class="col_item item-small-news <?php if($image):?> item-small-news-image<?php endif;?><?php if($border):?> border-lightgrey pb10 pl10 pr10 pt10<?php endif;?>">
	<?php if($image):?>
		<figure><a href="<?php the_permalink();?>"><?php WPSM_image_resizer::show_static_resized_image(array('thumb'=> true, 'crop'=> false, 'width'=> 80, 'no_thumb_url' => get_template_directory_uri() . '/images/default/noimage_100_70.jpg'));?></a></figure>
	<?php endif;?>
	<div class="item-small-news-details">
	    <h3><?php do_action('rehub_in_title_post_list');?><a href="<?php the_permalink();?>"><?php the_title();?></a><?php rehub_create_price_for_list($post->ID);?></h3>
	    <?php if(!$nometa):?>
		    <div class="post-meta">
		    	<span class="date_ago">
		            <?php printf( __( '%s ago', 'rehubchild' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
		        </span>
		        <span class="comm_number_for_list"><i class="fa fa-commenting"></i> <?php echo get_comments_number(); ?></span>
		    </div> 
	    <?php endif;?>
	    <?php do_action('rehub_after_meta_post_list');?>
    </div>    
</div>