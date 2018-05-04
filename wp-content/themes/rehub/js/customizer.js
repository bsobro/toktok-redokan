/* 
 * Сustomizer Script
 * @package rehub
 */
 
 jQuery(document).ready(function($) {
   'use strict';
   
   ShowHideFunc(
   $('#_customize-input-rehub_sticky_nav-radio-0'),
   $('#_customize-input-rehub_sticky_nav-radio-1'),
   $('#customize-control-rehub_logo_sticky_url')
   );
   
   function ShowHideFunc(button0,button1,container){
		if(button1.is(":checked")){
			container.show();
		}else{
			container.hide();
		}
		button1.click(function(){
			container.fadeIn();
		});
		button0.click(function(){
			container.fadeOut();
		});
   }
		
		
}); //END Document.ready