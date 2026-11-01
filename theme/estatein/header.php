<?php defined("ABSPATH") || exit(); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo("charset"); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#141414">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <div class="announcement" data-banner><?php estatein_asset(
        "2f8b4.svg",
        "9ff3c.svg",
        "announcement-art",
    ); ?><p><?php echo esc_html(
    estatein_setting("banner_text"),
); ?> <a href="<?php echo esc_url(
     estatein_destination("about"),
 ); ?>">Learn More</a></p><button class="banner-close" type="button" aria-label="Dismiss announcement" data-dismiss-banner><?php estatein_asset(
    "5802e.svg",
    "fe723.svg",
); ?></button></div>
    <div class="navigation wrap">
        <?php estatein_logo(); ?>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav" aria-label="Open navigation"><?php estatein_asset(
            "9cc2a.svg",
        ); ?></button>
        <nav id="primary-nav" class="primary-nav" aria-label="Main navigation">
            <?php if (has_nav_menu("primary")) {
                wp_nav_menu([
                    "theme_location" => "primary",
                    "container" => false,
                    "depth" => 1,
                    "menu_class" => "nav-list",
                ]);
            } else {
                 ?>
                <ul class="nav-list"><?php foreach (
                    [
                        "home" => "Home",
                        "about" => "About Us",
                        "properties" => "Properties",
                        "services" => "Services",
                    ]
                    as $key => $label
                ):

                    $active =
                        "home" === $key
                            ? is_front_page()
                            : estatein_current_page_key() === $key ||
                                ("properties" === $key &&
                                    is_singular("estatein_property"));
                    $url =
                        "home" === $key
                            ? home_url("/")
                            : estatein_destination($key);
                    ?><li><a class="<?php echo $active
    ? "nav-home"
    : ""; ?>" href="<?php echo esc_url($url); ?>"<?php echo $active
    ? ' aria-current="page"'
    : ""; ?>><?php echo esc_html($label); ?></a></li><?php
                endforeach; ?></ul>
            <?php
            } ?>
            <a class="button nav-contact<?php echo "contact" ===
            estatein_current_page_key()
                ? " button-primary"
                : ""; ?>" href="<?php echo esc_url(
    estatein_destination("contact"),
); ?>"<?php echo "contact" === estatein_current_page_key()
    ? ' aria-current="page"'
    : ""; ?>>Contact Us</a>
        </nav>
    </div>
</header>
