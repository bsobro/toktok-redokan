<?php
$theme_base = get_template_directory_uri();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charget="<?php bloginfo('charset'); ?>">
    <title><?php bloginfo('name'); ?><?php wp_title(); ?></title>

    <!-- head -->
    <?php
    wp_enqueue_style('style-css', $theme_base.'/style.css');
    ?>
    <!-- /head -->
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <img src="<?php echo $theme_base; ?>/images/main-bg.jpg" class="main-bg" />

    <header>
        <div id="main-menu">
        <?php
            wp_nav_menu( array(
                'theme_location'  => 'primary',
                'container'       => 'nav',
                'container_class' => 'normal',
            ) );
        ?>
        </div>
    </header>

    <div class="wrapper-inner">
        <div class="main-content">
