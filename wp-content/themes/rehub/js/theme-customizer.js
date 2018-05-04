/**
 * Rehub Live Customizer
 */
( function( $ ) {

	wp.customize('rehub_body_block', function(value) {
		value.bind(function(newval) {
			if( newval == 1 ) {
				$('body').addClass('rh-boxed-container');
			}else{
				$('body').removeClass('rh-boxed-container');
			}
		});
	});
	wp.customize('rehub_content_shadow', function(value) {
		value.bind(function(newval) {
			if( newval == 1 ) {
				$('body').addClass('no_bg_wrap');
			}else{
				$('body').removeClass('no_bg_wrap');
			}
		});
	});
	wp.customize('rehub_logo', function(value) {
		var LogoSection = $('.logo-section').html();
		value.bind(function(newval) {
			if(newval){
				var LogoHTML = '<a href="/" class="logo_image"><img src="'+newval+'" /></a>';
				$('.logo-section .logo').html(LogoHTML);
			}else{
				$('.logo-section').html(LogoSection);
			}
		});
	});
	wp.customize('rehub_text_logo', function(value) {
		Logo = $('.logo').html();
		value.bind(function(newval) {
			if(newval){
				$('.logo-section .textlogo').text(newval);
			}else{
				$('.logo').html(Logo);
			}
		});
	});
	wp.customize('rehub_text_slogan', function(value) {
		Logo = $('.logo').html();
		value.bind(function(newval) {
			if(newval){
				$('.logo-section .sloganlogo').text(newval);
			}else{
				$('.logo').html(Logo);
			}
		});
	});
	wp.customize('rehub_sticky_nav', function(value) {
		var MainNav = $('#main_header').html();
		value.bind(function(newval) {
			if( newval == 1 ) {
				$('.main-nav').addClass('rh-stickme');
				$('.main-nav .rh-container').addClass('rh-flex-center-align logo_insticky_enabled');
				$(".rh-stickme").sticky({topSpacing:0, wrapperClassName: 'sticky-wrapper re-stickyheader', getWidthFrom: '.header_wrap', responsiveWidth : true});
			}else{
				$('#main_header').html(MainNav);
			}
		});
	});
	wp.customize('rehub_logo_sticky_url', function(value) {
		value.bind(function(newval) {
			if(newval){
				var LogoSticky = '<a href="/" class="logo_image_insticky"><img src="'+newval+'" /></a>';
				$('.main-nav .rh-container').prepend(LogoSticky);
			}else{
				$('.logo_image_insticky').replaceWith('');
			}
		});
	});
	wp.customize('header_logoline_style', function(value) {
		value.bind(function(newval) {
			if( newval == 1 ) {
				$('#main_header').removeClass('white_style');
				$('#main_header').addClass('dark_style');
			}else{
				$('#main_header').removeClass('dark_style');
				$('#main_header').addClass('white_style');
			}
		});
	});
	wp.customize('header_menuline_style', function(value) {
		value.bind(function(newval) {
			if( newval == 1 ) {
				$('div.main-nav').removeClass('white_style');
				$('div.main-nav').addClass('dark_style');
			}else{
				$('div.main-nav').removeClass('dark_style');
				$('div.main-nav').addClass('white_style');
			}
		});
	});
	wp.customize('header_topline_style', function(value) {
		value.bind(function(newval) {
			if( newval == 1 ) {
				$('.header_top_wrap').removeClass('white_style');
				$('.header_top_wrap').addClass('dark_style');
			}else{
				$('.header_top_wrap').removeClass('dark_style');
				$('.header_top_wrap').addClass('white_style');
			}
		});
	});
	wp.customize('rehub_header_top', function(value) {
		value.bind(function(newval) {
			if( newval == 1 ) {
				$('.header_top_wrap').fadeOut();
			}else{
				$('.header_top_wrap').fadeIn();
			}
		});
	});
	
} )( jQuery );