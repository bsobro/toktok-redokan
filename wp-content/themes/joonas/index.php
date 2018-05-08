<?php
/**
 * index.php
 *
 * @package themes/joonas
 */
get_header();

while( have_posts() ):
    the_post();
?>
    <a href="<?php=get_permalink(); ?>">
        <h1><?php the_title(); ?></h1>
    </a>
    <br/>

    <?php the_content(); ?>

    <br /><br />
<?php
endwhile;

get_footer();

?>
