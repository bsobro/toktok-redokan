<?php
/**
 * ReHub Theme Customizer
 *
 * @package rehub
 */

 /* 
 * The fields which is general for both Rehub Theme and Customizer options
 */
 $rh_cross_option_fields = array
 (
    'rehub_custom_color',
    'rehub_sec_color',
    'rehub_btnoffer_color',
    'enable_smooth_btn',
    'rehub_color_link',
    'rehub_sidebar_left',
    'rehub_body_block',
    'rehub_content_shadow',
    'rehub_color_background',
    'rehub_background_image',
    'rehub_background_repeat',
    'rehub_background_position',
    'rehub_background_offset',
    'rehub_background_fixed',
    'rehub_sized_background',
    'rehub_branded_bg_url',
    'rehub_logo',
    'rehub_logo_retina',
    'rehub_logo_retina_width',
    'rehub_logo_retina_height',
    'rehub_text_logo',
    'rehub_text_slogan',
    'rehub_logo_pad',
    'rehub_sticky_nav',
    'rehub_logo_sticky_url',
    'header_logoline_style',
    'rehub_header_color_background',
    'rehub_header_background_image',
    'rehub_header_background_repeat',
    'rehub_header_background_position',
    'header_menuline_style',
    'header_menuline_type',
    'rehub_nav_font_custom',
    'rehub_nav_font_upper',
    'rehub_nav_font_light',
    'rehub_nav_font_border',
    'rehub_enable_menu_shadow',
    'rehub_custom_color_nav',
    'rehub_custom_color_nav_font',
    'header_topline_style',
    'rehub_custom_color_top',
    'rehub_custom_color_top_font',
    'rehub_logged_enable_intop',
    'rehub_header_top',
);

 /* 
 * Adds option fields to the Customizer
 */
function rh_customize_register( $wp_customize ) {

	/* THEME OPTIONS */
	$wp_customize->add_panel( 'panel_id', array(
		'priority' => 121,
		'capability' => 'edit_theme_options',
		'title' => __('Theme Options', 'rehub_framework'),
		'description' => __('ReHub Control Center', 'rehub_framework'),
	));

	/* 
	 * APPEARANCE/COLOR
	*/
	$wp_customize->add_section( 'rh_styling_settings', array(
		'title' => __('Appearance/Color', 'rehub_framework'),
		'priority'  => 122,
		'capability' => 'edit_theme_options',
		'panel' => 'panel_id',
	));

	//Custom color schema
	$wp_customize->add_setting( 'rehub_custom_color', array(
		'default' => '#43c801',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_custom_color', array(
		'label' => __('Custom color schema', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_custom_color',
	)));

	//Custom secondary color
	$wp_customize->add_setting( 'rehub_sec_color', array(
		'default' => '#111',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_sec_color', array(
		'label' => __('Custom secondary color', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_sec_color',
	)));

	//Set offer buttons color
	$wp_customize->add_setting( 'rehub_btnoffer_color', array(
		'default' => '#43c801',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_btnoffer_color', array(
		'label' => __('Set offer buttons color', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_btnoffer_color',
	)));

	//Custom color for links inside posts
	$wp_customize->add_setting( 'rehub_color_link', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_color_link', array(
		'label' => __('Custom color for links inside posts','rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_color_link',
	)));

	//Enable smooth design for inputs
	$wp_customize->add_setting( 'enable_smooth_btn', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'enable_smooth_btn', array(
		'label' => __('Enable smooth design for inputs?', 'rehub_framework'),
		'section'  => 'rh_styling_settings',
		'settings' => 'enable_smooth_btn',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
	
	$wp_customize->add_control('layout_settings', array(
		'settings' => 'layout_settings',
		'label' => __('LAYOUT SETTINGS', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'type' => 'html',
	));
		
	//Set sidebar to left side
	$wp_customize->add_setting( 'rehub_sidebar_left', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_sidebar_left', array(
		'label' => __('Set sidebar to left side?', 'rehub_framework'),
		'section'  => 'rh_styling_settings',
		'settings' => 'rehub_sidebar_left',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
			
	//Enable boxed version
	$wp_customize->add_setting( 'rehub_body_block', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_body_block', array(
		'label' => __('Enable boxed version?', 'rehub_framework'),
		'section'  => 'rh_styling_settings',
		'settings' => 'rehub_body_block',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
		
	//Disable box borders under content box
	$wp_customize->add_setting( 'rehub_content_shadow', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_content_shadow', array(
		'label' => __('Disable box borders under content box?', 'rehub_framework'),
		'section'  => 'rh_styling_settings',
		'settings' => 'rehub_content_shadow',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
			
	//Background Color
	$wp_customize->add_setting( 'rehub_color_background', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_color_background', array(
		'label' => __('Background Color', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_color_background',
	)));
			
	//Background Image
	$wp_customize->add_setting( 'rehub_background_image', array(
		'default' => '',
		'capability' => 'edit_theme_options'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rehub_background_image', array(
		'label' => __('Background Image', 'rehub_framework'),
		'description' => __('Set background color before it', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_background_image',
	)));

	//Background Repeat
	$wp_customize->add_setting('rehub_background_repeat', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => 'repeat',
	));
	$wp_customize->add_control('rehub_background_repeat', array(
		'settings' => 'rehub_background_repeat',
		'label' => __('Background Repeat', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'type' => 'select',
		'choices' => array(
			'repeat' => __('Repeat', 'rehub_framework'),
			'no-repeat' => __('No Repeat', 'rehub_framework'),
			'repeat-x' => __('Repeat X', 'rehub_framework'),
			'repeat-y' => __('Repeat Y', 'rehub_framework'),
		),
	));
		
	//Background Position
	$wp_customize->add_setting('rehub_background_position', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control('rehub_background_position', array(
		'settings' => 'rehub_background_position',
		'label' => __('Background Position', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'type' => 'select',
		'choices' => array(
			'repeat' => __('Left', 'rehub_framework'),
			'center' => __('Center', 'rehub_framework'),
			'right' => __('Right', 'rehub_framework'),
		),
	));
		
	//Set offset
	$wp_customize->add_setting('rehub_background_offset', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_background_offset', array(
		'label' => __('Set offset', 'rehub_framework'),
		'description' => __('Set offset from top for background for avoid header overlap', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_background_offset',
		'type' => 'number',
	));
		
	//Fixed Background Image
	$wp_customize->add_setting( 'rehub_background_fixed', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_background_fixed', array(
		'label' => __('Fixed Background Image?', 'rehub_framework'),
		'section'  => 'rh_styling_settings',
		'settings' => 'rehub_background_fixed',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));

	//Fit size
	$wp_customize->add_setting( 'rehub_sized_background', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_sized_background', array(
		'label' => __('Fit size?', 'rehub_framework'),
		'section'  => 'rh_styling_settings',
		'settings' => 'rehub_sized_background',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
			
	//Url for branded background
 	$wp_customize->add_setting('rehub_branded_bg_url', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_branded_bg_url', array(
		'label' => __('Url for branded background', 'rehub_framework'),
		'description' => __('Insert url that will be display on background', 'rehub_framework'),
		'section' => 'rh_styling_settings',
		'settings' => 'rehub_branded_bg_url',
		'type' => 'url',
	));

	/* 
	 * LOGO & FAVICON 
	 * Site Identity section
	*/
	
	//Upload Logo
	$wp_customize->add_setting( 'rehub_logo', array(
		'default' => '',
		'capability' => 'edit_theme_options'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rehub_logo', array(
		'label' => __('Upload Logo', 'rehub_framework'),
		'description' => __('Upload your logo. Max width is 450px. (1200px for full width, 180px for logo + menu row layout)', 'rehub_framework'),
		'section' => 'title_tagline',
		'settings' => 'rehub_logo',
	)));
		
	//Retina Logo (no live preview)
	$wp_customize->add_setting( 'rehub_logo_retina', array(
		'default' => '',
		'capability' => 'edit_theme_options'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rehub_logo_retina', array(
		'label' => __('Upload Logo (retina version)', 'rehub_framework'),
		'description' => __('Upload retina version of the logo. It should be 2x the size of main logo.', 'rehub_framework'),
		'section' => 'title_tagline',
		'settings' => 'rehub_logo_retina',
	)));
		
	//Logo width (no live preview)
	$wp_customize->add_setting('rehub_logo_retina_width', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_logo_retina_width', array(
		'label' => __('Logo width', 'rehub_framework'),
		'description' => __('Please, enter logo width (without px)', 'rehub_framework'),
		'section' => 'title_tagline',
		'settings' => 'rehub_logo_retina_width',
		'type' => 'number',
	));
		
	//Logo width (no live preview)
	$wp_customize->add_setting('rehub_logo_retina_height', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_logo_retina_height', array(
		'label' => __('Retina logo height', 'rehub_framework'),
		'description' => __('Please, enter logo height (without px)', 'rehub_framework'),
		'section' => 'title_tagline',
		'settings' => 'rehub_logo_retina_height',
		'type' => 'number',
	));
		
	//Text logo
	$wp_customize->add_setting('rehub_text_logo', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_text_logo', array(
		'label' => __('Text logo', 'rehub_framework'),
		'description' => __('You can type text logo. Use this field only if no image logo', 'rehub_framework'),
		'section' => 'title_tagline',
		'settings' => 'rehub_text_logo',
		'type' => 'text',
	));
		
	//Slogan
	$wp_customize->add_setting('rehub_text_slogan', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_text_slogan', array(
		'label' => __('Slogan', 'rehub_framework'),
		'description' => __('You can type slogan below text logo. Use this field only if no image logo', 'rehub_framework'),
		'section' => 'title_tagline',
		'settings' => 'rehub_text_slogan',
		'type' => 'textarea',
	));
		
	/* 
	 * HEADER AND MENU 
	*/
	$wp_customize->add_section( 'rh_header_settings', array(
		'title' => __('Header and Menu', 'rehub_framework'),
		'priority'  => 124,
		'capability' => 'edit_theme_options',
		'panel' => 'panel_id',
	));

	//Set padding from top and bottom
	$wp_customize->add_setting('rehub_logo_pad', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_logo_pad', array(
		'label' => __('Set padding from top and bottom', 'rehub_framework'),
		'description' => __('This will add custom padding from top and bottom for all custom elements in logo section. Default is 15', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_logo_pad',
		'type' => 'number',
	));
		
	//Sticky Menu Bar
	$wp_customize->add_setting( 'rehub_sticky_nav', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_sticky_nav', array(
		'label' => __('Sticky Menu Bar', 'rehub_framework'),
		'description' => __('Enable/Disable Sticky navigation bar.', 'rehub_framework'),
		'section'  => 'rh_header_settings',
		'settings' => 'rehub_sticky_nav',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
		//Upload Logo for sticky menu
		$wp_customize->add_setting( 'rehub_logo_sticky_url', array(
			'default' => '',
			'capability' => 'edit_theme_options'
		));
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rehub_logo_sticky_url', array(
			'label' => __('Upload Logo for sticky menu', 'rehub_framework'),
			'description' => __('Upload your logo. Max height is 40px.', 'rehub_framework'),
			'section' => 'rh_header_settings',
			'settings' => 'rehub_logo_sticky_url',
		)));
		
	//Choose color style of header logo section
	$wp_customize->add_setting('header_logoline_style', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control('header_logoline_style', array(
		'settings' => 'header_logoline_style',
		'label' => __('Color style of header logo section', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'type' => 'select',
		'choices' => array(
			'0' => __('White style and dark fonts', 'rehub_framework'),
			'1' => __('Dark style and white fonts', 'rehub_framework'),
		),
	));

	//Custom Background Color
	$wp_customize->add_setting( 'rehub_header_color_background', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_header_color_background', array(
		'label' => __('Custom Background Color', 'rehub_framework'),
		'description' => __('Choose the background color or leave blank for default', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_header_color_background',
	)));
		
	//Custom Background Image
	$wp_customize->add_setting( 'rehub_header_background_image', array(
		'default' => '',
		'capability' => 'edit_theme_options'
	));
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rehub_header_background_image', array(
		'label' => __('Custom Background Image', 'rehub_framework'),
		'description' => __('Upload a background image or leave blank', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_header_background_image',
	)));
		
	//Background Repeat
	$wp_customize->add_setting('rehub_header_background_repeat', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '',
	));
	$wp_customize->add_control('rehub_header_background_repeat', array(
		'settings' => 'rehub_header_background_repeat',
		'label' => __('Background Repeat', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'type' => 'select',
		'choices' => array(
			'repeat' => __('Repeat', 'rehub_framework'),
			'no-repeat' => __('No Repeat', 'rehub_framework'),
			'repeat-x' => __('Repeat X', 'rehub_framework'),
			'repeat-y' => __('Repeat Y', 'rehub_framework'),
		),
	));
		
	//Background Position
	$wp_customize->add_setting('rehub_header_background_position', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
	));
	$wp_customize->add_control('rehub_header_background_position', array(
		'settings' => 'rehub_header_background_position',
		'label' => __('Background Position', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'type' => 'select',
		'choices' => array(
			'repeat' => __('Left', 'rehub_framework'),
			'center' => __('Center', 'rehub_framework'),
			'right' => __('Right', 'rehub_framework'),
		),
	));
		
	//Choose color style of header menu section
	$wp_customize->add_setting('header_menuline_style', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control('header_menuline_style', array(
		'settings' => 'header_menuline_style',
		'label' => __('Color style of header menu section', 'rehub_framework'),	
		'section' => 'rh_header_settings',
		'type' => 'select',
		'choices' => array(
			'0' => __('White style and dark fonts', 'rehub_framework'),
			'1' => __('Dark style and white fonts', 'rehub_framework'),
		),
	));
		
	//Choose type of font and padding
	$wp_customize->add_setting('header_menuline_type', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control('header_menuline_type', array(
		'settings' => 'header_menuline_type',
		'label' => __('Choose type of font and padding', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'type' => 'select',
		'choices' => array(
			'0' => __('Middle size and padding', 'rehub_framework'),
			'1' => __('Compact size and padding', 'rehub_framework'),
			'2' => __('Big size and padding', 'rehub_framework'),
		),
	));
		
	//Add custom font size
	$wp_customize->add_setting('rehub_nav_font_custom', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses',
	)); 
	$wp_customize->add_control('rehub_nav_font_custom', array(
		'label' => __('Add custom font size', 'rehub_framework'),
		'description' => __('Default is 15. Put just number', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_nav_font_custom',
		'type' => 'number',
	));

	//Enable uppercase font
	$wp_customize->add_setting( 'rehub_nav_font_upper', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_nav_font_upper', array(
		'label' => __('Enable uppercase font?', 'rehub_framework'),
		'section'  => 'rh_header_settings',
		'settings' => 'rehub_nav_font_upper',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
	
	//Enable Light font weight
	$wp_customize->add_setting( 'rehub_nav_font_light', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_nav_font_light', array(
		'label' => __('Enable Light font weight?', 'rehub_framework'),
		'section'  => 'rh_header_settings',
		'settings' => 'rehub_nav_font_light',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
	
	//Disable border of items
	$wp_customize->add_setting( 'rehub_nav_font_border', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_nav_font_border', array(
		'label' => __('Disable border of items?', 'rehub_framework'),
		'section'  => 'rh_header_settings',
		'settings' => 'rehub_nav_font_border',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
	
	//Menu shadow
	$wp_customize->add_setting( 'rehub_enable_menu_shadow', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '1',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_enable_menu_shadow', array(
		'label' => __('Menu shadow', 'rehub_framework'),
		'description' => __('Enable/Disable shadow under menu', 'rehub_framework'),
		'section'  => 'rh_header_settings',
		'settings' => 'rehub_enable_menu_shadow',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
	
	//Custom color of menu background
	$wp_customize->add_setting( 'rehub_custom_color_nav', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_custom_color_nav', array(
		'label' => __('Custom color of menu background', 'rehub_framework'),
		'description' => __('Or leave blank for default color', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_custom_color_nav',
	)));
	
	//Custom color of menu font
	$wp_customize->add_setting( 'rehub_custom_color_nav_font', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_custom_color_nav_font', array(
		'label' => __('Custom color of menu font', 'rehub_framework'),
		'description' => __('Or leave blank for default color', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_custom_color_nav_font',
	)));
	
	//Choose color style of header top line
	$wp_customize->add_setting('header_topline_style', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control('header_topline_style', array(
		'settings' => 'header_topline_style',
		'label' => __('Choose color style of header top line', 'rehub_framework'),	
		'section' => 'rh_header_settings',
		'type' => 'select',
		'choices' => array(
			'0' => __('White style and dark fonts', 'rehub_framework'),
			'1' => __('Dark style and white fonts', 'rehub_framework'),
		),
	));

	//Custom color for top line of header
	$wp_customize->add_setting( 'rehub_custom_color_top', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_custom_color_top', array(
		'label' => __('Custom color for top line of header', 'rehub_framework'),
		'description' => __('Or leave blank for default color', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_custom_color_top',
	)));
	
	//Custom color of menu font for top line of header
	$wp_customize->add_setting( 'rehub_custom_color_top_font', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'rehub_custom_color_top_font', array(
		'label' => __('Custom color of menu font for top line of header', 'rehub_framework'),
		'description' => __('Or leave blank for default color', 'rehub_framework'),
		'section' => 'rh_header_settings',
		'settings' => 'rehub_custom_color_top_font',
	)));
	
	//Replace top menu when user logined (no live preview)
	$wp_customize->add_setting( 'rehub_logged_enable_intop', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_logged_enable_intop', array(
		'label' => __('Replace top menu when user logined', 'rehub_framework'),
		'description' => __('Default top menu will be replaced with /User Logged In Menu/', 'rehub_framework'),
		'section'  => 'rh_header_settings',
		'settings' => 'rehub_logged_enable_intop',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
	
	//Disable top line
	$wp_customize->add_setting( 'rehub_header_top', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_key',
		'default' => '0',
	));
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'rehub_header_top', array(
		'label' => __('Disable top line', 'rehub_framework'),
		'description' => __('You can disable top line', 'rehub_framework'),
		'section'  => 'rh_header_settings',
		'settings' => 'rehub_header_top',
		'type' => 'radio',
		'choices' => array(
			'0'  => __('Off', 'rehub_framework'),
			'1' => __('On', 'rehub_framework'),
		),
	)));
	
	$wp_customize->get_setting( 'rehub_body_block' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_content_shadow' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_logo' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_logo_retina' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_logo_retina_width' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_logo_retina_height' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_text_logo' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_text_slogan' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_sticky_nav' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_logo_sticky_url' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_logoline_style' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_menuline_style' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_topline_style' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_logged_enable_intop' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'rehub_header_top' )->transport  = 'postMessage';
}
add_action( 'customize_register', 'rh_customize_register' );

/* Adds admin scripts and styles */
function rh_customizer_scripts() {
	$screen = get_current_screen();
	$screen_id = $screen->id;

	if( 'customize' == $screen_id ) {
		wp_enqueue_script( 'customizer-js', get_template_directory_uri() .'/js/customizer.js', array('jquery'), '1.0', true );
		wp_enqueue_style( 'customizer-css', get_template_directory_uri() .'/css/customizer.css' );
    }
}
add_action('admin_enqueue_scripts', 'rh_customizer_scripts');

/* Adds scripts to Preview frame */
function rh_live_preview_scripts() {
	wp_enqueue_script( 'rh-customizer-js', get_template_directory_uri() .'/js/theme-customizer.js', array( 'jquery','customize-preview' ), '1.0', true );
	wp_enqueue_script( 'sticky' );
}
add_action( 'customize_preview_init', 'rh_live_preview_scripts' );

/* Saves Customizer options to Theme ones */
function rh_save_theme_options() {
	global $rh_cross_option_fields;
	$opt = get_option( 'rehub_option' );
	foreach( $rh_cross_option_fields as $key ) {
		$old_value = $opt[$key];
		$new_value = get_theme_mod( $key );
		if( $new_value != $old_value )
			$opt[$key] = $new_value;
		continue;
	}
	update_option( 'rehub_option', $opt );
}
add_action( 'save_post_customize_changeset', 'rh_save_theme_options' );

/* Saves Theme options to Customizer ones */
function rh_save_customizer_options( $opt ){
	global $rh_cross_option_fields;
	foreach( $rh_cross_option_fields as $key ){
		$old_value = get_theme_mod( $key );
		$new_value = $opt[$key];
		if( $new_value != $old_value )
			set_theme_mod( $key, $new_value );
		continue;
	}
}
add_action('vp_option_set_before_save', 'rh_save_customizer_options');
