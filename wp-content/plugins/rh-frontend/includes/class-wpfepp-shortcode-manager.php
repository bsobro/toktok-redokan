<?php 

/**
 * Creates the two shortcodes offered by the plugin: [wpfepp_submission_form] and [wpfepp_post_table]. Uses WPFEPP_Post_List and WPFEPP_Form.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Shortcode_Manager
{
	/**
	 * Plugin version. This is used in wp_enqueue_style and wp_enqueue_script to make sure that the end user doesn't get outdated scripts and styles because of browser caching.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;

	/**
	 * Class constructor. Includes the files for WPFEPP_Post_List and WPFEPP_Form and initializes the $version attribute.
	 **/
	public function __construct($version)
	{
		$this->version 		= $version;
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-form.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-post-list.php';
	}

	/**
	 * Adds the actions of the class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_shortcode( 'wpfepp_submission_form', array($this, 'submission_form_shortcode') );
		add_shortcode( 'wpfepp_post_table', array($this, 'post_table_shortcode') );
	}

	/**
	 * Callback function for the [wpfepp_submission_form] shortcode registered in add_actions() with 'paid' argument.
	 **/
	public function submission_form_shortcode($_args) {
		wp_enqueue_style( 'wpfepp-style' );
		wp_enqueue_script( 'wpfepp-script' );
		wp_enqueue_media();

	    $args = shortcode_atts( array( 'form' => -1, 'paid' => 0 ), $_args );
		
		ob_start();
		$form_obj = new WPFEPP_Form( $this->version, $args['form'], $args['paid'] );
		
		if ( $args['paid'] && $args['paid'] == 1 ) {
			$data_settings = get_option('wpfepp_payment_settings');
			
			if ( $data_settings && $data_settings['turn_on_payment'] ) {
				
				$current_number_post_package = $this->check_ordered_number_post( $args['form'] );
				$current_expire_post_package = $this->check_expire_post_package( $args['form'] );
				
				if (empty($current_expire_post_package)) {
					$current_date_expire = 0;
					$remained_days = 1;
				} else {
					$current_date_expire = $current_expire_post_package;
					$remained_days = $current_expire_post_package - time();
				}
				
				if ( $current_number_post_package <= 0 or $remained_days <= 0 ) {
					_e( "<p>You must have one of post submit package before submission. After purchase, please, login to get access to form. You can buy package below:<p>", "wpfepp-plugin" );

					$query_args = array(
						'post_type' => 'product',
						'post_status' => 'publish',
						'meta_key' => '_form_id_package', 
						'meta_value' => $args['form'],
						'tax_query' => array(
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => 'rh-submit-package', 
						) ) );
					 
					$wp_query_paid = new WP_Query($query_args);
					if ( $wp_query_paid->have_posts() ) : 
						echo '<div class="woo_offer_list">';
						while ( $wp_query_paid->have_posts() ) : $wp_query_paid->the_post(); 
						global $product;
						?>
						<div class="rehub_feat_block table_view_block">
							<div class="block_with_coupon">
								<div class="offer_thumb">
									<div class="deal_img_wrap">
									<a href="<?php echo $product->get_permalink(); ?>">
									<?php if ( !has_post_thumbnail() && $product->is_on_sale() && $product->get_regular_price() && $product->get_price() > 0) :?>
										<span class="sale_tag_inwoolist">
											<h5>
											<?php   
												$offer_price_calc = (float) $product->get_price();
												$offer_price_old_calc = (float) $product->get_regular_price();
												$sale_proc = 0 -(100 - ($offer_price_calc / $offer_price_old_calc) * 100); 
												$sale_proc = round($sale_proc); 
												echo $sale_proc; echo '%';
											;?>
											</h5>
										</span>
									<?php else :?>
										<?php if ($product->is_on_sale() && $product->get_regular_price() && $product->get_price() > 0) : ?>
										<span class="sale_a_proc">
											<?php   
												$offer_price_calc = (float) $product->get_price();
												$offer_price_old_calc = (float) $product->get_regular_price();
												$sale_proc = 0 -(100 - ($offer_price_calc / $offer_price_old_calc) * 100); 
												$sale_proc = round($sale_proc); 
												echo $sale_proc; echo '%';
											;?>
										</span> 
										<?php endif ?>              
										<?php echo woocommerce_get_product_thumbnail();?>
									<?php endif;?>
									</a>
									</div>

								</div>					    
								<div class="desc_col">             
									<h3><a href="<?php echo $product->get_permalink() ?>"><?php the_title(); ?></a></h3>
									<p>
										<?php kama_excerpt('maxchar=150'); ?>
									</p>
								</div>
									<div class="price_col">
										<?php if ($product->get_price() !='') : ?>
										<p><span class="price_count"><?php echo $product->get_price_html(); ?></span></p>
										<?php endif ;?>                                          
									</div>            
									<div class="buttons_col">
										<div class="priced_block">
											<?php if ( $product->is_in_stock() && $product->add_to_cart_url() !='' ) : ?>
												<?php $loop_add_to_cart_link = sprintf( '<a href="%s" target="_blank" data-product_id="%s"%sclass="woo_loop_btn btn_offer_block%s product_type_%s">%s</a>',
													esc_url( $product->get_permalink() ),
													esc_attr( $product->get_id() ),
													$product->get_sku() ? ' data-product_sku="'. esc_attr( $product->get_sku() ) .'"' : '',
													$product->is_purchasable() && $product->is_in_stock() ? ' add_to_cart_button' : '',
													esc_attr( $product->get_type() ),
													__( 'Get package', 'wpfepp-plugin' )
													); ?>
												<?php echo apply_filters( 'woocommerce_loop_add_to_cart_link', $loop_add_to_cart_link, $product ); ?>
											<?php endif; ?>                                            
										</div>
									</div>
							</div>
						</div>
						<?php					
						endwhile;
						echo '</div>';
					else:
						_e( "<p>Currently, there is no packages, if you are admin, please, add payment packages. Check instructions in Settings - Payment.<p>", "wpfepp-plugin" );
					endif; wp_reset_query();
					
				} else {
					$date_format = get_option( 'date_format' );
					$expire_post_package = ($current_date_expire == 0) ? __("No limit", "wpfepp-plugin") : date( $date_format, $current_date_expire );
					printf( __( "<p>You have %d posts left. Your package expires: %s.</p>", "wpfepp-plugin" ), $current_number_post_package, $expire_post_package );
					
					$form_obj->display();
				}
			
			} else {
				if(current_user_can('install_plugins'))
					printf( '<div class="wpfepp wpfepp wpfepp-posts"><div class="wpfepp-message error display">%s</div></div>', __( "Please check Payment Settings of the plugin to enable paid submit function.", "wpfepp-plugin" ));
			}
			
		} else {
			$form_obj->display();
		}
		
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	/**
	 * Callback function for the [wpfepp_post_table] shortcode registered in add_actions()
	 **/
	public function post_table_shortcode($_args){
		wp_enqueue_style('wpfepp-style');
		wp_enqueue_script('wpfepp-script');
		wp_enqueue_media();

		$args = shortcode_atts( array( 'form' => -1, 'show_all' => 1 ), $_args );
		ob_start();
		$post_list = new WPFEPP_Post_List($this->version, $args['form'], $args['show_all']);
		$post_list->display();
		return ob_get_clean();
	}
	
	/**
	* Get current user ID
	*/
	public function current_user_id() {
	  
	  	if ( is_user_logged_in() ) {
	   		$user_obj = wp_get_current_user();
		   	if(!is_wp_error($user_obj)){
		    	return $user_obj->ID;
		   	}
		  	else {
		  		return;
		  	}   	
		}
		else{
			return;
		}
	}
	
	/**
	* Get current post number that user paid before
	*/
	public function check_ordered_number_post( $paid_form_id ) {
		$user_numb_post_meta = '_numb_post_package_'. $paid_form_id;
		$current_number_post_package = get_user_meta( $this->current_user_id(), $user_numb_post_meta, true );
		
		if ( $current_number_post_package )
			return $current_number_post_package;
	}
	
	/**
	* Get current expire date that user paid before
	*/
	public function check_expire_post_package( $paid_form_id ) {
		$user_post_package_meta = '_exp_post_package_'. $paid_form_id;
		$current_expire_post_package = get_user_meta( $this->current_user_id(), $user_post_package_meta, true );
		
		if ( $current_expire_post_package )
			return $current_expire_post_package;

	}
}

?>