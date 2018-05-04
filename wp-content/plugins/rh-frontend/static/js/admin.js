jQuery(document).ready(function ($) {
	$('#wpfepp-dismiss-nag').click(function (e) {
		e.preventDefault();
		$(this).closest('.updated').hide();
		$.ajax({ type:'POST', url: ajaxurl, data: { action: 'wpfepp_dismiss_nag' } });
	});
	
	$(".wpfepp-op-sidebar .wpfepp-rehub-fields").on("click", "li", function(e){
		e.preventDefault();
		var clickLi = $(this).data("field");
		var targetContainer = $(this).parent();
		var targetParent = targetContainer.parent().prev().find("form input[name=meta_key]");
		targetParent.val(clickLi);
	});
});