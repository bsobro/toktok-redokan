<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $post;
?>
<?php $related = wc_get_related_products($product->get_id());
    if ( sizeof( $related ) == 0 ){
    }else{
        $related = implode(',',$related);
        $related_array = array('ids'=>$related, 'columns'=>'3_col', 'data_source'=>'ids', 'show'=> 3, 'show_coupons_only'=>2);
        if (rehub_option('rehub_wcv_related') == '1'){
            $artist = $post->post_author;
            $related_array['user_id'] = $artist;
        }
        if(rehub_option('width_layout') =='extended'){
            $related_array['columns'] = '4_col';
        }        
        echo '<div class="clearfix"></div><h3>'.__( 'Related Products', 'rehub_framework' ).'</h3>';
        if (rehub_option('woo_design') == 'grid') { 
            echo wpsm_woogrid_shortcode($related_array);                  
        }
        elseif (rehub_option('woo_design') == 'gridtwo') { 
            $related_array['gridtype'] = 'compact';
            echo wpsm_woogrid_shortcode($related_array);                  
        }         
        else{
            echo wpsm_woocolumns_shortcode($related_array);           
        }           
    }
?>