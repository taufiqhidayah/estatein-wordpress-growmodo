<?php
/** Theme bootstrap. */
defined('ABSPATH') || exit();

define('ESTATEIN_VERSION', '1.0.0');
add_filter(
    'theme_file_uri',
    function ($url, $file) {
        // Avoid stale asset responses during local iteration; production uses the release version.
        if ('local' === wp_get_environment_type()) {
            $path = get_template_directory() . '/' . ltrim($file, '/');
            return add_query_arg(
                'v',
                is_file($path) ? filemtime($path) : ESTATEIN_VERSION,
                $url,
            );
        }
        return $url;
    },
    10,
    2,
);
foreach (
    ['content', 'site', 'fields', 'newsletter', 'inquiries', 'setup']
    as $estatein_module
) {
    require_once get_template_directory() . '/inc/' . $estatein_module . '.php';
}

add_action('after_setup_theme', function () {
    load_theme_textdomain('estatein', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo', [
        'height' => 48,
        'width' => 160,
        'flex-width' => true,
        'flex-height' => true,
    ]);
    register_nav_menus(['primary' => __('Primary navigation', 'estatein')]);
    add_image_size('estatein-card', 864, 636, true);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'estatein',
        get_theme_file_uri('/assets/css/main.min.css'),
        [],
        ESTATEIN_VERSION,
    );
    wp_enqueue_script(
        'estatein',
        get_theme_file_uri('/assets/js/main.min.js'),
        [],
        ESTATEIN_VERSION,
        ['strategy' => 'defer', 'in_footer' => true],
    );
});

add_action(
    'wp_head',
    function () {
        if (is_front_page()) {
            printf(
                '<meta name="description" content="%s">' . "\n",
                esc_attr(estatein_setting('hero_description')),
            );
            printf(
                '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' .
                    "\n",
                esc_url(
                    get_theme_file_uri('/assets/fonts/urbanist-latin.woff2'),
                ),
            );
        }
    },
    2,
);

add_action('send_headers', function () {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    }
});

/** Render downloaded assets, with dedicated mobile sources where supplied by Figma. */
function estatein_asset(
    $desktop,
    $mobile = '',
    $class = '',
    $alt = '',
    $eager = false,
) {
    $path = get_theme_file_path('/assets/images/' . basename($desktop));
    $size =
        is_file($path) && 'svg' !== pathinfo($path, PATHINFO_EXTENSION)
            ? getimagesize($path)
            : false;
    $dimensions = $size
        ? ' width="' . (int) $size[0] . '" height="' . (int) $size[1] . '"'
        : '';
    if ($mobile) {
        printf(
            '<picture class="%s"><source media="(max-width: 767px)" srcset="%s">',
            esc_attr($class),
            esc_url(get_theme_file_uri('/assets/images/' . basename($mobile))),
        );
    }
    $above_fold = $eager || 'svg' === pathinfo($path, PATHINFO_EXTENSION);
    printf(
        '<img class="%s" src="%s" alt="%s"%s loading="%s" decoding="async"%s>',
        esc_attr($mobile ? '' : $class),
        esc_url(get_theme_file_uri('/assets/images/' . basename($desktop))),
        esc_attr($alt),
        $dimensions,
        $above_fold ? 'eager' : 'lazy',
        $eager ? ' fetchpriority="high"' : '',
    ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dimensions are integers.
    if ($mobile) {
        echo '</picture>';
    }
}

function estatein_logo()
{
    if (has_custom_logo()) {
        the_custom_logo();
        return;
    }
    echo '<a class="brand" href="' .
        esc_url(home_url('/')) .
        '" aria-label="Estatein home">';
    estatein_asset('f5564.svg', 'c9ae8.svg', 'brand-symbol');
    estatein_asset('8c648.svg', '4fc95.svg', 'brand-word');
    echo '</a>';
}

function estatein_sparkles()
{
    echo '<span class="sparkles" aria-hidden="true">';
    foreach (
        [
            ['f5d05.svg', '0edb9.svg'],
            ['6cb2a.svg', '809ca.svg'],
            ['06232.svg', '2442e.svg'],
        ]
        as $files
    ) {
        echo '<span>';
        estatein_asset($files[0], $files[1]);
        echo '</span>';
    }
    echo '</span>';
}

/** Real counts rather than the decorative Figma placeholder totals. */
function estatein_carousel_controls($label, $collection, $button_label)
{
    ?>
    <div class="carousel-controls">
        <button class="button button-secondary mobile-view-all" type="button" data-collection="<?php echo esc_attr(
            $collection,
        ); ?>"><?php echo esc_html($button_label); ?></button>
        <p class="carousel-count" aria-live="polite" aria-atomic="true"><span data-current>01</span> <span data-total>of 03</span><span class="screen-reader-text"> <?php echo esc_html(
            $label,
        ); ?></span></p>
        <div class="carousel-arrows">
            <button class="arrow-button" type="button" data-direction="-1" aria-label="<?php echo esc_attr(
                'Previous ' . $label,
            ); ?>"><?php estatein_asset('30f05.svg', 'c00b5.svg'); ?></button>
            <button class="arrow-button" type="button" data-direction="1" aria-label="<?php echo esc_attr(
                'Next ' . $label,
            ); ?>"><?php estatein_asset('65841.svg', 'aba3a.svg'); ?></button>
        </div>
    </div>
    <?php
}
