<?php defined('ABSPATH') || exit();
get_header();
?>
<main id="main" class="wrap standard-page">
<?php if (have_posts()):
    while (have_posts()):
        the_post(); ?>
<article <?php post_class(
    'editor-content',
); ?>><h1><?php the_title(); ?></h1><?php the_content(); ?></article>
<?php
    endwhile;
    the_posts_pagination();
else:
     ?>
<h1>Nothing here yet</h1><p><a href="<?php echo esc_url(
    home_url('/'),
); ?>">Return to the homepage</a></p>
<?php
endif; ?>
</main>
<?php get_footer(); ?>
