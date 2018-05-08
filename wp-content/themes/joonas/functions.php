<?php
/**
 * functions.php
 *
 * @package theme/joonas
 */

register_nav_menu('primary', '메인 메뉴');

function main_sidebar() {
    register_sidebar( array(
        'name'          => '기본 사이드바',
        'id'            => 'main-sidebar',
        'description'   => '기본 사이드바입니다.',
    ) );
}

add_action('widgets_init', 'main_sidebar');

function dummy_data() {
    ?><meta name="dummy" content="test meta"/><?php
}

add_action('wp_head','dummy_data');

/**
 * https://developer.wordpress.org/reference/hooks/wp_title/
 *
 * Note that the filter function *must* return the content after it is finished processing,
 * or the title will be blank and other plugins also filtering the content may generate errors.
 **/
function dummy_title($title, $sep, $seplocation) {
    $title .= ' - Dummy';
    return $title;
}

add_filter('wp_title', 'dummy_title', 10, 3);

