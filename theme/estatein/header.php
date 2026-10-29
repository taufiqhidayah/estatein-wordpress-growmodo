<?php defined('ABSPATH') || exit(); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#141414">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <div class="announcement" data-banner>
        <?php estatein_asset('2f8b4.svg', '9ff3c.svg', 'announcement-art'); ?>
        <p><?php echo esc_html(
            estatein_setting('banner_text'),
        ); ?> <a href="<?php echo esc_url(
     home_url('/#properties'),
 ); ?>">Learn More</a></p>
        <button class="banner-close" type="button" aria-label="Dismiss announcement" data-dismiss-banner><?php estatein_asset(
            '5802e.svg',
            'fe723.svg',
        ); ?></button>
    </div>
    <div class="navigation wrap">
        <?php estatein_logo(); ?>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" aria-label="Open navigation"><?php estatein_asset(
            '9cc2a.svg',
        ); ?></button>
        <nav id="primary-nav" class="primary-nav" aria-label="Main navigation">
            <?php if (has_nav_menu('primary')): ?>
                <?php wp_nav_menu([
                    'theme_location' => 'primary',
                    'container' => false,
                    'depth' => 1,
                    'menu_class' => 'nav-list',
                ]); ?>
            <?php else: ?>
                <ul class="nav-list">
                    <li><a class="nav-home" href="<?php echo esc_url(
                        home_url('/#home'),
                    ); ?>"<?php echo is_front_page()
    ? ' aria-current="page"'
    : ''; ?>>Home</a></li>
                    <li><a href="<?php echo esc_url(
                        home_url('/#about'),
                    ); ?>">About Us</a></li>
                    <li><a href="<?php echo esc_url(
                        home_url('/#properties'),
                    ); ?>">Properties</a></li>
                    <li><a href="<?php echo esc_url(
                        home_url('/#services'),
                    ); ?>">Services</a></li>
                </ul>
            <?php endif; ?>
            <a class="button nav-contact" href="<?php echo esc_url(
                home_url('/#contact'),
            ); ?>">Contact Us</a>
        </nav>
    </div>
</header>
