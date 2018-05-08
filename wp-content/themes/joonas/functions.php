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
