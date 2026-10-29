<?php defined('ABSPATH') || exit();
get_header();
?>
<main id="main" class="wrap standard-page"><p class="eyebrow">404 · Page not found</p><h1>Let’s find your way home.</h1><p>The page you are looking for is unavailable.</p><a class="button button-primary" href="<?php echo esc_url(
    home_url('/'),
); ?>">Back to Home</a></main>
<?php get_footer(); ?>
