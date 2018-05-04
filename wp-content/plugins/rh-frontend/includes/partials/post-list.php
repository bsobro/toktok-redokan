<?php
if ( !defined( 'ABSPATH' ) ) die( 'No direct access allowed' );
	$current_user = wp_get_current_user();
    $list_settings = get_option('wpfepp_post_list_settings');
	$status = isset( $_GET['wpfepp_type']) ? $_GET['wpfepp_type'] : 'publish';
	$paged = isset( $_GET['wpfepp_page']) ? $_GET['wpfepp_page'] : 1;
	$per_page = isset($list_settings['post_list_page_len']) ? $list_settings['post_list_page_len'] : 10 ;
	$tabs = isset($list_settings['post_list_tabs']) ? $list_settings['post_list_tabs'] : array('live' => '1', 'pending' => '1', 'draft' => '1') ;
	$columns = isset($list_settings['post_list_cols']) ? $list_settings['post_list_cols'] : array('link' => '1', 'edit' => '1', 'delete' => '1') ;
	$args = array( 'post_type' => $this->post_type, 'posts_per_page' => $per_page, 'paged'=>$paged, 'orderby'=> 'DESC', 'author' => $current_user->ID, 'post_status' => $status);
	if( $show_all != 1 ) {
		$args['meta_key'] = 'wpfepp_submit_with_form_id';
		$args['meta_value'] = $check_form_id;
	}
	$author_posts = new WP_Query($args);
    $old_exist = ($paged * $per_page) < $author_posts->found_posts;
    $new_exist = $paged > 1;

    if( isset($_GET['p']) )
    	$blog_page 	= '&p='.$_GET['p'];
    elseif( isset($_GET['page_id']) )
    	$blog_page 	= '&page_id='.$_GET['page_id'];
    else
    	$blog_page 	= '';
?>

<div class="wpfepp wpfepp-posts">
	<div class="wpfepp-message"></div>
	<ul class="wpfepp-tabs">
		<?php if( $tabs['live'] ) : ?><li><a <?php if( $status == 'publish' ): ?>class="active"<?php endif; ?> href="?wpfepp_type=publish<?php echo $blog_page; ?>"><?php _e( "Live", "wpfepp-plugin" ); ?></a></li><?php endif; ?>
		<?php if( $tabs['pending'] ) : ?><li><a <?php if( $status == 'pending' ): ?>class="active"<?php endif; ?> href="?wpfepp_type=pending<?php echo $blog_page; ?>"><?php _e( "Pending", "wpfepp-plugin" ); ?></a></li><?php endif; ?>
		<?php if( $tabs['draft'] ) : ?><li><a <?php if( $status == 'draft' ): ?>class="active"<?php endif; ?> href="?wpfepp_type=draft<?php echo $blog_page; ?>"><?php _e( "Draft", "wpfepp-plugin" ); ?></a></li><?php endif; ?>
		<?php do_action( 'wpfepp_editor_tabs' ); ?>
	</ul>
	<?php do_action( 'wpfepp_post_table_container_before' ); ?>
	<div class="wpfepp-post-table-container wpfepp-<?php echo $status; ?>-posts-table-container">
		<?php if( !$author_posts->have_posts() ): ?>
			<?php
				switch ( $status ) {
					case 'publish':
						$printable_status = __('live', 'wpfepp-plugin');
						break;
					case 'pending':
						$printable_status = __('pending', 'wpfepp-plugin');
						break;
					case 'draft':
						$printable_status = __('draft', 'wpfepp-plugin');
						break;
					default:
						break;
				}
			?>
			<?php printf( __( "You don't have any %s articles.", "wpfepp-plugin" ), $printable_status ); ?>
		<?php endif; ?>
		<table>
			<?php
				while($author_posts->have_posts()) : $author_posts->the_post();
					$post_id = get_the_ID();
			?>
				<tr class="wpfepp-row-<?php echo $post_id; ?> wpfepp-row">
					<td>
						<?php the_title(); ?>
					</td>

					<?php if( $status == 'publish' && $columns['link'] ): ?>
						<td class="wpfepp-fixed-td"><a href="<?php the_permalink(); ?>" title="<?php _e( 'View Post', 'wpfepp-plugin' ); ?>" target="_blank"><span class="dashicons dashicons-visibility"></span></a></td>
					<?php endif; ?>

					<?php if( $columns['edit'] ): ?>
						<td class="wpfepp-fixed-td">
							<a href="?wpfepp_action=edit&wpfepp_post=<?php echo $post_id.$blog_page; ?>" title="<?php _e( 'Edit Post', 'wpfepp-plugin' ); ?>"><span class="dashicons dashicons-edit"></span></a>
						</td>
					<?php endif; ?>

					<?php if( $columns['delete'] ): ?>
						<td class="post-delete wpfepp-fixed-td">
							<a href="<?php echo wp_nonce_url('?wpfepp_action=delete&wpfepp_post='.$post_id.$blog_page, 'wpfepp-delete-post-'.$post_id.'-nonce' ); ?>" title="<?php _e( 'Delete Post', 'wpfepp-plugin' ); ?>"><span class="dashicons dashicons-trash"></span></a>
							<?php wp_nonce_field('wpfepp-delete-post-'.$post_id.'-nonce', 'wpfepp-delete-post-'.$post_id.'-nonce'); ?>
							<input type="hidden" class="post-id" value="<?php echo $post_id; ?>">
						</td>
					<?php endif; ?>
				</tr>
			<?php endwhile; ?>
		</table>
		<div class="wpfepp-nav">
			<?php if($new_exist): ?>
				<a class="wpfepp-nav-link wpfepp-nav-link-left" href="?wpfepp_type=<?php echo $status?>&wpfepp_page=<?php echo ($paged-1).$blog_page; ?>"><span class="dashicons dashicons-arrow-left-alt2"></span> <?php _e( 'Newer Posts', 'wpfepp-plugin' ); ?></a>
			<?php endif; ?>
			<?php if($old_exist): ?>
				<a class="wpfepp-nav-link wpfepp-nav-link-right" href="?wpfepp_type=<?php echo $status?>&wpfepp_page=<?php echo ($paged+1).$blog_page; ?>"><?php _e( 'Older Posts', 'wpfepp-plugin' ); ?> <span class="dashicons dashicons-arrow-right-alt2"></span></a>
			<?php endif; ?>
			<div style="clear:both;"></div>
		</div>
		<?php wp_reset_query(); wp_reset_postdata(); ?>
	</div>
	<?php do_action( 'wpfepp_post_table_container_after' ); ?>
</div>