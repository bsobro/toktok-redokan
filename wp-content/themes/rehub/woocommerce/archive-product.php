<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>

<?php do_action('rh_woo_args_query');?>

<?php 
    $vendor_id = $vendor_pro = '';
    if (defined('wcv_plugin_dir')){
        $vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
        $vendor_id   = WCV_Vendors::get_vendor_id( $vendor_shop );
    }
    if ($vendor_id){
        return include(rh_locate_template('inc/wcvendor/storepage.php'));
    }
?>

<?php if (is_tax('store')):?>  
      <?php include(rh_locate_template('woocommerce/brandarchive.php')); ?>                                     
<?php else :?> 

<!-- CONTENT -->
<div class="rh-container"> 
    <div class="rh-content-wrap clearfix">
        <!-- Main Side -->
        <div class="main-side woocommerce page clearfix<?php if(rehub_option('woo_columns') =='4_col') {echo ' full_width';}?>" id="content">
            <article class="post" id="page-<?php the_ID(); ?>">
                <?php do_action( 'woocommerce_before_main_content' );?>
                <?php woocommerce_breadcrumb();?>   
                <?php if(is_product_taxonomy()) {
                    $term = get_queried_object();
                    $page_title = apply_filters( 'woocommerce_page_title', $term->name );
                    echo'<h1>'.$page_title.'</h1>';
                }  ?>       
                <?php if(empty($_GET)):?><?php do_action( 'woocommerce_archive_description' ); ?><?php endif;?>
                <?php if ( have_posts() ) : ?>
                    <?php
                        /**
                         * woocommerce_before_shop_loop hook
                         *
                         * @hooked woocommerce_result_count - 20
                         * @hooked woocommerce_catalog_ordering - 30
                         */
                        do_action( 'woocommerce_before_shop_loop' );
                    ?> 
                    <?php $display_type = '';?>
                    <?php if (function_exists('woocommerce_get_loop_display_mode')):?>
                        <?php $display_type = woocommerce_get_loop_display_mode();?>
                        <?php if ( 'subcategories' === $display_type || 'both' === $display_type ):?>
                            <?php woocommerce_output_product_categories(array( 'before' => '<div class="col_wrap_fifth products_category_box column_woo">', 'after' => '</div>', 'parent_id' => is_product_category() ? get_queried_object_id() : 0)); ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php woocommerce_product_loop_start(); ?>   
                        <?php if ($display_type != 'subcategories'):?>                      
                            <?php while ( have_posts() ) : the_post(); ?>
                                <?php do_action( 'woocommerce_shop_loop' ); wc_get_template_part( 'content', 'product' ); ?>
                            <?php endwhile; // end of the loop. ?>
                        <?php endif; ?>
                    <?php woocommerce_product_loop_end(); ?>
                    <?php
                        /**
                         * woocommerce_after_shop_loop hook
                         *
                         * @hooked woocommerce_pagination - 10
                         */
                        do_action( 'woocommerce_after_shop_loop' );
                    ?>
                <?php else : ?>
                    <?php wc_get_template( 'loop/no-products-found.php' ); ?>
                <?php endif; ?>
                <?php
                if ( is_product_category() && 0 === absint( get_query_var( 'paged' )) && empty($_GET) ) {
                    if($term){
                        $tagid = $term->term_id;
                        $cat_sec_desc = get_term_meta( $tagid, 'brand_second_description', true );
                        if($cat_sec_desc){
                            echo '<div class="woo_cat_sec_description">' . wc_format_content( $cat_sec_desc ) . '</div>';
                        }
                    }
                }
                ?>                
                <?php
                    /**
                     * woocommerce_after_main_content hook.
                     *
                     * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
                     */
                    do_action( 'woocommerce_after_main_content' );
                ?>                
            </article>
        </div>
    <!-- /Main Side --> 

    <!-- Sidebar -->
    <?php if(rehub_option('woo_columns') =='4_col'):?>
    <?php else :?>
        <?php get_sidebar('shop'); ?>
    <?php endif;?>
    <!-- /Sidebar --> 

    </div>
</div>
<!-- /CONTENT -->

<?php endif;?>  
<?php get_footer(); ?>