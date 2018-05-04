jQuery(document).ready(function($){
	
	$( "#wpfepp-sortable" ).sortable({helper : 'clone'});
	
	$('body').on('click', ".wpfepp-widget-head", function(e){
		$(this).find('.wpfepp-expand span.dashicons').first().toggleClass('dashicons-arrow-down').toggleClass('dashicons-arrow-up');
		$(this).siblings('.wpfepp-widget-body').first().slideToggle();
	});
	$('body').on('click', ".wpfepp-custom-field-delete span.dashicons-trash", function(e){
		var confirmation = confirm(objectL10n.areyousure);
		if(confirmation)
			$(this).closest('.wpfepp-widget-container').remove();
		e.stopPropagation();
	});
	
	function wpfepp_ajax_form_submit(click_event, clicked, callback){
		click_event.preventDefault();
		loading_img = $('#wpfepp-loading');
		var parent_form = clicked.closest('.wpfepp-ajax-form');
		parent_form.find('input[type="text"].wpfepp-required').each(function(){
			if(!$.trim(this.value).length) $(this).addClass('error'); else $(this).removeClass('error');
		});
		if($('.wpfepp-ajax-form .error').length)
			return;
		loading_img.show();
		$.ajax({
			type:'POST',
			dataType: 'json',
			url: ajaxurl,
			data: parent_form.serialize(),
			success: function(data, textStatus, XMLHttpRequest){
				loading_img.hide();
				if(data.success){
					parent_form.find('input[type="text"], textarea').val('');
					callback(data);
				}
				else{
					loading_img.hide();
					alert(data.error);
				}
			},
			error:function(MLHttpRequest,textStatus,errorThrown){
				alert(errorThrown);
			}
		});
	}

	$('body').on('focus', '.wpfepp-ajax-form input[type="text"].error', function(){
		$(this).removeClass('error');
	});

	$('#wpfepp-add-custom-field').click(function(e){
		wpfepp_ajax_form_submit(e, $(this), function(data){
			$('#wpfepp-sortable').append(data.widget_html);

			  var element = $('.wpfepp-widget-container:not(.ui-sortable-handle) select.wpfepp_element');
			  selectTrigger('input', element);
			  $('.wpfepp-widget-container:not(.ui-sortable-handle)').on('change', element, function(){
				  var selectType = element.val();
				  selectTrigger(selectType, element);
			});

		});				
	});
	
	$('#wpfepp-create-form').click(function(e){
		wpfepp_ajax_form_submit(e, $(this), function(data){
			$('#wpfepp-form-list-table-container').html(data.table_html);
		});
	});

	// Downlaodable product options
	if($('input.wpfepp_down_product').is(':checked')) {
		$('input.wpfepp_extern_prod').prop("disabled", true);
	} else {
		$('input.wpfepp_extern_prod').prop("disabled", false);
	}
	$('input.wpfepp_down_product').change(function() {
		if(this.checked) {
			$('input.wpfepp_extern_prod').prop("disabled", true);
		} else {
			$('input.wpfepp_extern_prod').prop("disabled", false);
		}
	});
	if($('.wpfepp_down_product').attr('checked')=="checked"){
		selectTrigger('down_type_on', $('.wpfepp_down_product'));
	}else{
		selectTrigger('off', $('.wpfepp_down_product'));
	}
	$('.wpfepp_down_product').click(function(){
	  var element = $(this);
	  if(element.attr('checked')){
		selectTrigger('down_type_on', element);
	  }else{
		selectTrigger('off', element);
	  }
	});
	
	// External product options
	if($('input.wpfepp_extern_prod').is(':checked')) {
		$('input.wpfepp_virtual_prod').prop("disabled", true);
		$('input.wpfepp_down_product').prop("disabled", true);
	} else {
		$('input.wpfepp_virtual_prod').prop("disabled", false);
		$('input.wpfepp_down_product').prop("disabled", false);
	}
	$('input.wpfepp_extern_prod').change(function() {
		if(this.checked) {
			$('input.wpfepp_virtual_prod').prop("disabled", true);
			$('input.wpfepp_down_product').prop("disabled", true);
		} else {
			$('input.wpfepp_virtual_prod').prop("disabled", false);
			$('input.wpfepp_down_product').prop("disabled", false);
		}
	});
	if($('.wpfepp_parser').attr('checked')=="checked"){
		selectTrigger('down_type_on', $('.wpfepp_parser'));
	}else{
		selectTrigger('off', $('.wpfepp_parser'));
	}
	$('.wpfepp_parser').click(function(){
	  var element = $(this);
	  if(element.attr('checked')){
		selectTrigger('parser_on', element);
	  }else{
		selectTrigger('off', element);
	  }
	});

	// Manage additional fields
 	$('select.wpfepp_element').each(function(){
	  var element = $(this);
	  var selectType = element.val();
	  selectTrigger(selectType, element);
	}); 
	
	$('.wpfepp-widget-container.ui-sortable-handle').on('change', 'select.wpfepp_element',function(){
	  var element = $(this);
	  var selectType = element.val();
	  selectTrigger(selectType, element);
	});
	
}); // End Document

function selectTrigger(selectType, element){
	var formTable = element.closest('table.form-table');
	var numbersRow = formTable.find('.wpfepp_step_count, .wpfepp_min_number, .wpfepp_max_number').closest('tr');
	var wordsRow = formTable.find('.wpfepp_min_words, .wpfepp_max_words').closest('tr');
	var symbolsRow = formTable.find('.wpfepp_min_symbols, .wpfepp_max_symbols').closest('tr');
	var multipleRow = formTable.find('.wpfepp_multiple').closest('tr');
	var choicesRow = formTable.find('.wpfepp_choices').closest('tr');
	var nofollowRow = formTable.find('.wpfepp_nofollow').closest('tr');
	var timestampRow = formTable.find('.wpfepp_unixtime').closest('tr');
	var attachdataRow = formTable.find('.wpfepp_attachdata').closest('tr');
	var parserWidthRow = formTable.find('.wpfepp_parser_width').closest('tr');
	var parserDowmtypeRow = formTable.find('.wpfepp_down_type').closest('tr');

		switch(selectType) {
			case "richtext":
				numbersRow.hide();
				wordsRow.show();
				symbolsRow.hide();
				choicesRow.hide();
				nofollowRow.show();
			break;
			case "plaintext":
				numbersRow.hide();
				wordsRow.show();
				symbolsRow.hide();
				choicesRow.hide();
				nofollowRow.show();
			break;
			case "input":
				numbersRow.hide();
				wordsRow.hide();
				symbolsRow.show();
				choicesRow.hide();
				nofollowRow.hide();
				attachdataRow.hide();
				timestampRow.hide();
				break;
			case "textarea":
				numbersRow.hide();
				wordsRow.show();
				symbolsRow.hide();
				choicesRow.hide();
				nofollowRow.show();
				attachdataRow.hide();
				timestampRow.hide();
				break;
			case "checkbox":
				numbersRow.hide();
				wordsRow.hide();
				symbolsRow.hide();
				choicesRow.show();
				nofollowRow.hide();
				attachdataRow.hide();
				timestampRow.hide();
				break;
			case "select":
				numbersRow.hide();
				wordsRow.hide();
				symbolsRow.hide();
				choicesRow.show();
				multipleRow.show();
				nofollowRow.hide();
				attachdataRow.hide();
				timestampRow.hide();
				break;
			case "radio":
				numbersRow.hide();
				wordsRow.hide();
				symbolsRow.hide();
				choicesRow.show();
				nofollowRow.hide();
				attachdataRow.hide();
				timestampRow.hide();
				break;
			case "image_url":
				numbersRow.hide();
				wordsRow.hide();
				symbolsRow.hide();
				choicesRow.hide();
				nofollowRow.hide();
				attachdataRow.show();
				timestampRow.hide();		
			break;
			case "inputdate":
				numbersRow.hide();
				wordsRow.hide();
				symbolsRow.hide();
				choicesRow.hide();
				nofollowRow.hide();
				attachdataRow.hide();
				timestampRow.show();
				break;
			case "inputnumb":
				numbersRow.show();
				wordsRow.hide();
				symbolsRow.hide();
				choicesRow.hide();
				nofollowRow.hide();
				attachdataRow.hide();
				timestampRow.hide();
				break;
			case "parser_on":
				parserWidthRow.show();
				break;
			case "down_type_on":
				parserDowmtypeRow.show();
				break;
			default:
				numbersRow.hide();
				wordsRow.hide();
				symbolsRow.hide();
				choicesRow.hide();
				multipleRow.hide();
				nofollowRow.hide();
				attachdataRow.hide();
				timestampRow.hide();
				parserWidthRow.hide();
				parserDowmtypeRow.hide();
		}	
}

