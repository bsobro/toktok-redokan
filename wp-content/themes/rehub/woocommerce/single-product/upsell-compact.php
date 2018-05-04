<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $post;
?>
<?php $upsells = $product->get_upsell_ids();?> 
<?php if( sizeof( $upsells ) == 0 ):?>
<?php else:?>
    <div class="rh-tabletext-block rh-tabletext-wooblock" id="section-woo-related">
        <div class="rh-tabletext-block-heading">
            <span class="toggle-this-table"></span>
            <h4 class="rh-heading-icon"><?php _e( 'Related Products', 'rehub_framework' );?></h4>
        </div>
        <div class="rh-tabletext-block-wrapper">        
            <div class="<?php echo ($sidebar) ? 'col_wrap_two' : 'col_wrap_three';?> rh-flex-eq-height woorelatedgrid compact_rel_grid">
                <?php foreach ($upsells as $item): ?>
                    <?php 
                        $title = get_the_title($item);
                        $url = get_the_permalink($item);
                        if ( has_post_thumbnail($item) ){
                            $image_id = get_post_thumbnail_id($item);  
                            $image_url = wp_get_attachment_image_src($image_id, 'full');  
                            $image_url = $image_url[0];
                            $image_url = apply_filters('rh_thumb_url', $image_url );
                        }
                        else {
                            $image_url = get_template_directory_uri() . '/images/default/noimage_123_90.png' ;
                            $image_url = apply_filters('rh_no_thumb_url', $image_url, $item);
                        }

                    ?>
                    <div class="col_item border-lightgrey pb10 pl10 pr10 pt10">
                        <div class="medianews-img floatleft mr20 rtlml20">
                            <a href="<?php echo $url;?>">
                            <?php WPSM_image_resizer::show_static_resized_image(array('src'=> $image_url, 'width'=> 80, 'title' => $title));?> 
                            </a>                    
                        </div>
                        <div class="medianews-body floatright">
                            <h5 class="font90 lineheight20 mb10 mt0 fontnormal">
                                <a href="<?php echo $url;?>"><?php echo $title;?></a>
                            </h5>
                            <div class="font80 lineheight15 greencolor">
                                <?php $the_price = get_post_meta( $item, '_price', true);  
                                    if ( '' != $the_price ) {
                                        $the_price = strip_tags( wc_price( $the_price ) );
                                        echo $the_price;
                                    } 
                                ?>
                            </div>
                            <?php if(rehub_option('woo_rhcompare') == 1) {echo'<div class="woo-btn-actions-notext mt10">';echo wpsm_comparison_button(array('class'=>'rhwoosinglecompare', 'id'=>$item)); echo '</div>';} ?>                            
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif;?>