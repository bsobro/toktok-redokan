<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<script data-cfasync="false">

// executes this when the DOM is ready
jQuery(document).ready(function() { 

	jQuery('#submit').click(function(){
		var options = { 
			'taxonomy'       : '',
			'child_of'       : '',
			'type'       : 'compactbig',
			'limit'       : '',
			'imageheight'       : '50',
			'classcol' : 'col_wrap_fifth',					
			};
			
		var shortcode = '[wpsm_tax_archive';
		
		for( var index in options) {
			var value = jQuery('#form').find('#taxarchive-' + index).val();
			
			if ( value !== '' )
				shortcode += ' ' + index + '="' + value + '"';
			else
				shortcode += ' ' + index + '="' + options[index] + '"'; 	
		}
		if(jQuery('#taxarchive-random').is(":checked")) {
			shortcode += ' random=1';
		}		
		
		shortcode += ']<br />';
		
		
		// inserts the shortcode into the active editor
		window.send_to_editor(shortcode);
		
		
		// closes Thickbox
		tb_remove();
	});
}); 
</script>
<form action="/" method="get" id="form" name="form" accept-charset="utf-8">
	<p>
        <label><?php _e('Taxonomy', 'rehub_framework') ;?></label>
        <input type="text" name="taxarchive-taxonomy" value="" id="taxarchive-taxonomy" /><br />
        <small>Set taxonomy. Default is Brand for woocommerce. Taxonomy for woo attribute starts from "pa_"</small>
    </p>
	<p>
        <label><?php _e('Child of', 'rehub_framework') ;?></label>
        <input type="text" name="taxarchive-child_of" value="" id="taxarchive-child_of" /><br />
        <small>Set ID of parent category if you want to show only child Items</small>        
    </p>     	
	<p>
		<label><?php _e('Type', 'rehub_framework') ;?></label>
		<select name="taxarchive-type" id="taxarchive-type" size="1">
            <option value="compactbig" selected="selected"><?php _e('Compact Blocks', 'rehub_framework') ;?></option>	
            <option value="compact"><?php _e('Compact small Blocks', 'rehub_framework') ;?></option>
            <option value="logo"><?php _e('Logo', 'rehub_framework') ;?></option>
            <option value="alpha"><?php _e('Alphabet', 'rehub_framework') ;?></option>            
        </select>
        <small>Logo works only for Brand, Affiliate Store and Category taxonomy. You can add logo when you edit category</small>        
	</p>
	<p>
		<label><?php _e('Columns', 'rehub_framework') ;?></label>
		<select name="taxarchive-classcol" id="taxarchive-classcol" size="1">
            <option value="col_wrap_fifth" selected="selected">5</option>
            <option value="col_wrap_fourth">4</option>
            <option value="col_wrap_three">3</option>
            <option value="col_wrap_two">2</option>  
            <option value="col_wrap_six">6</option> 
            <option value="col_wrap_one">1</option>                                 
        </select>
        <small>Choose this if you want to divide all list in Compact Blocks. This parameter is not working for Logo and Alphabet Type</small>        
	</p>	
	<p>
        <label><?php _e('Limit (Number)', 'rehub_framework') ;?></label>
        <input type="text" name="taxarchive-limit" value="" id="taxarchive-limit" /><br />
    </p>		

	<p>
        <label><?php _e('Image height', 'rehub_framework') ;?></label>
        <input type="text" name="taxarchive-imageheight" value="" id="taxarchive-imageheight" /><br />
        <small>use with Logo or Alphabet type. Default is 50</small>        
    </p>      
	<p>
		<label><?php _e('Random order', 'rehub_framework') ;?></label>
        <input id="taxarchive-random" name="taxarchive-random" type="checkbox" class="checks" value="false" />
        <small>Show value as "-" if value is empty</small>        
	</p>     
	
	 <p>
        <label>&nbsp;</label>
        <input type="button" id="submit" class="button" value="<?php _e('Insert', 'rehub_framework') ;?>" name="submit" />
    </p>

</form>