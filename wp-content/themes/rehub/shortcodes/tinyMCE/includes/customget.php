<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<script data-cfasync="false">

// executes this when the DOM is ready
jQuery(document).ready(function() { 

	jQuery('#submit').click(function(){
		var options = { 
			'post_id'       : '',
			'field'       : '',
			'type'       : 'custom',
			'label'       : '',
			'posttext'       : '',						
			};
			
		var shortcode = '[wpsm_custom_meta';
		
		for( var index in options) {
			var value = jQuery('#form').find('#customget-' + index).val();
			
			if ( value !== '' )
				shortcode += ' ' + index + '="' + value + '"';
			else
				shortcode += ' ' + index + '="' + options[index] + '"'; 	
		}
		if(jQuery('#customget-showempty').is(":checked")) {
			shortcode += ' showempty=1';
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
        <label><?php _e('Post ID', 'rehub_framework') ;?></label>
        <input type="text" name="customget-post_id" value="" id="customget-post_id" /><br />
        <small><?php _e('Leave Blank to get value from current post', 'rehub_framework') ;?></small>
    </p>	
	<p>
		<label><?php _e('Type', 'rehub_framework') ;?></label>
		<select name="customget-type" id="customget-type" size="1">
            <option value="custom" selected="selected"><?php _e('Custom', 'rehub_framework') ;?></option>			
            <option value="attribute"><?php _e('Woocommerce Attribute', 'rehub_framework') ;?></option>
            <option value="attributelink"><?php _e('Woocommerce Attribute with Link', 'rehub_framework') ;?></option>
        </select>
	</p>
	<p>
        <label><?php _e('Field Key', 'rehub_framework') ;?></label>
        <input type="text" name="customget-field" value="" id="customget-field" /><br />
        <small>Required. Set custom field key or attribute slug which you want to get. Woocommerce attribute can start from pa_. For example, if you have "mobilesize" attribute key, place here "pa_mobilesize"</small>
    </p>	
	<p>
        <label><?php _e('Label', 'rehub_framework') ;?></label>
        <input type="text" name="customget-label" value="" id="customget-label" /><br />
    </p>
	<p>
        <label><?php _e('After value label', 'rehub_framework') ;?></label>
        <input type="text" name="customget-posttext" value="" id="customget-posttext" /><br />
    </p>   
	<p>
		<label><?php _e('Show Empty', 'rehub_framework') ;?></label>
        <input id="customget-showempty" name="customget-showempty" type="checkbox" class="checks" value="false" />
        <small>Show value as "-" if value is empty</small>        
	</p>     
	
	 <p>
        <label>&nbsp;</label>
        <input type="button" id="submit" class="button" value="<?php _e('Insert', 'rehub_framework') ;?>" name="submit" />
    </p>

</form>