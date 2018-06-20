jQuery( document ).ready( function( $ ) {

	var scrollTop = 0;
	var tmpViewport;
	var wooshipping_address_popup = $( '#wooshipping-postcode-popup' );
	var wooshipping_address = {
			
		init: function() {
			$( '#billing_country, #shipping_country' ).on( 'change', this.change_country );
			
			$( 'form' ).delegate( '.wooshipping_postcode_field .popup-trigger', 'click touchstart', this.open_postcode );
			$( 'form' ).delegate( 'input[readonly="readonly"]', 'click touchstart', this.open_postcode );
			$( window ).on( 'resize touchmove scroll', this.fluidDialog );
			$( document ).on( 'dialogopen', this.fluidDialog );
		},
		
		change_country: function(e) {
			var prefix	= $( this ).attr( 'id' ).match(/^.*_/);
			var config	= wooshipping_postcode_params;
			var locale	= $( this ).val();
			
			if ( locale == 'KR' && config.use_fullname == 'no' ) {
				$( '#' + prefix + 'first_name_field' ).removeClass( 'form-row-first' ).addClass( 'form-row-wide' );
				$( '#' + prefix + 'last_name_field' ).hide();
				$( '#' + prefix + 'last_name' ).prop( 'disabled', true );
			} else {
				$( '#' + prefix + 'first_name_field' ).removeClass( 'form-row-wide' ).addClass( 'form-row-first' );
				$( '#' + prefix + 'last_name_field' ).show();
				$( '#' + prefix + 'last_name' ).prop( 'disabled', false );
			}
			
			if ( config.use_company == 'no' ) {
				$( '#' + prefix + 'company_field' ).hide();
				$( '#' + prefix + 'company' ).prop( 'disabled', false );
			}
			
			if ( config.use_country == 'no' ) {
				$( '#' + prefix + 'country_field' ).hide();
				$( '#' + prefix + 'country' ).prop( 'disabled', false );
			}
			
			// convert to full-width.	
			$( '#' + prefix + 'email_field' ).removeClass( 'form-row-first form-row-last' ).addClass( 'form-row-wide' );
			$( '#' + prefix + 'phone_field' ).removeClass( 'form-row-first form-row-last' ).addClass( 'form-row-wide' );
			$( '#' + prefix + 'postcode_field' ).removeClass( 'form-row-first form-row-last' ).addClass( 'form-row-wide' );
			
			if ( locale == 'KR' ) {
				setTimeout( function() {
					// move postcode field order
					var postcode = $( '#' + prefix + 'postcode_field' );
					postcode.insertBefore( '#' + prefix + 'address_1_field' );
					postcode.removeClass( 'form-row-first form-row-last' ).addClass( 'form-row-first' );
					postcode.after( '<p class="form-row form-row-last wooshipping_postcode_field" id="' + prefix + 'postcode_trigger_field"><label>&nbsp;</label><input type="button" class="button popup-trigger" value="' + wooshipping_postcode_params.label + '"></p>' );
					
					// prevent direct input
					$( '#' + prefix + 'postcode, #' + prefix + 'address_1' ).attr( 'readonly', 'readonly' );
					
				}, 1 );
			} else {
				$( '#' + prefix + 'postcode_trigger_field' ).remove();
				$( '#' + prefix + 'postcode, #' + prefix + 'address_1' ).removeAttr( 'readonly' );
			}
			
		},

		open_postcode: function() {
			var this_id = $( this ).closest( '.form-row' ).attr( 'id' );
			var prefix = this_id.substring( 0, ( this_id.indexOf( '_' ) + 1 ) );
		
			// Check viewport
			viewport = document.querySelector('meta[name=viewport]');
			if ( typeof viewport == 'object' && viewport != null ) {
				tmpViewport = viewport.getAttribute( 'content' );
				viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0');
			} else {
				$( 'head' ).append( '<meta id="p8viewport" name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">' );
			}

			// Set scrolltop
			scrollTop = $( document ).scrollTop();
			
			$( this ).blur();

			var topIndex = jQuery.topZIndex();
			
			wooshipping_address_popup.dialog( {
				title: $( this ).val(),
				autoOpen: true,
				modal: true,
				resizable: false,
				draggable: false,
				closeOnEscape: true,
				fluid: true,
				width: '100%',
				maxWidth: 500,
				height: '100%',
				maxHeight: 500,
				open: function( event, ui ) {
					$( event.target ).parent().css( '-webkit-overflow-scrolling', 'touch' );
					$( '.ui-dialog' ).css( 'z-index', topIndex + 2 );
					$( '.ui-widget-overlay' ).css( 'z-index', topIndex + 1 );
				},
				close: function( event, ui ) {
					$( document ).scrollTop( scrollTop );
					viewport = document.querySelector('meta[name=viewport]');
					viewport.setAttribute( 'content', tmpViewport );
					$( '#p8viewport' ).remove();
				}
			});

			var postcode_layer = document.getElementById( 'wooshipping-postcode-popup' );
			
			new daum.Postcode({
				oncomplete: function(data) {
					var fullAddr = data.address; // 최종 주소 변수
					var extraAddr = ''; // 조합형 주소 변수 

					// 도로명 주소일때
					if(data.addressType === 'R'){
						if(data.bname !== ''){
							extraAddr += data.bname;
						}
						if(data.buildingName !== ''){
							extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
						}
						fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
					}
					document.getElementById( prefix + 'postcode' ).value = data.zonecode;
					document.getElementById( prefix + 'address_1' ).value = fullAddr;
					
					wooshipping_address_popup.dialog( 'close' );
					$( '#' + prefix + 'address_2' ).focus();
					
				},
				width : '100%',
				height : '100%'
			}).embed( postcode_layer );

			p8FluidDialog();
		},
		
		fluidDialog: function() {
			p8FluidDialog();
		}
		
	}.init();
});

function p8FluidDialog() {
	var wooshipping_address_popup = jQuery( '#wooshipping-postcode-popup' );

	jQuery( '.ui-dialog:visible' ).each( function () {
		var $this = jQuery( this );
		var dialog = $this.find( '.ui-dialog-content' ).data( 'ui-dialog' );

		wooshipping_address_popup.css( 'height', '100%' );
		
		if ( dialog.options.fluid ) {
			var wWidth = jQuery( window ).width();
			var wHeight = jQuery( window ).height();
			
			if ( wWidth < ( parseInt( dialog.options.maxWidth ) + 200 ) || wHeight < parseInt( dialog.options.maxHeight ) )  {
				var tGap = parseInt( jQuery( '.ui-dialog-titlebar' ).height() );

				$this
					.css( 'max-width', '100%' )
					.css( 'max-height', '100%' );
				wooshipping_address_popup.css( 'height', parseInt( wooshipping_address_popup.height() ) - tGap );
			} else {
				$this
					.css( 'max-width', dialog.options.maxWidth + 'px' )
					.css( 'max-height', dialog.options.maxHeight + 'px' );
				wooshipping_address_popup.css( 'height', '100%' );
			}
			dialog.option( 'position', dialog.options.position );
		}

	} );
}


(function ($) {
	$.topZIndex = function (selector) {
		return Math.max(0, Math.max.apply(null, $.map(((selector || "*") === "*")? $.makeArray(document.getElementsByTagName("*")) : $(selector),
			function (v) {
				return parseFloat($(v).css("z-index")) || null;
			}
		)));
	};

	$.fn.topZIndex = function (opt) {
		if (this.length === 0) {
			return this;
		}
		
		opt = $.extend({increment: 1}, opt);

		var zmax = $.topZIndex(opt.selector),
			inc = opt.increment;

		return this.each(function () {
			this.style.zIndex = (zmax += inc);
		});
	};
})(jQuery);