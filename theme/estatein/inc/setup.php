<?php
/** Explicit, repeatable demo import. Never modifies an existing site's homepage on activation. */
defined('ABSPATH') || exit();

add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) {
        return;
    }
    $branch = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    $patched = ['8.3' => '8.3.36', '8.4' => '8.4.27', '8.5' => '8.5.12'];
    if (
        PHP_VERSION_ID < 80300 ||
        (isset($patched[$branch]) &&
            version_compare(PHP_VERSION, $patched[$branch], '<'))
    ) {
        echo '<div class="notice notice-error"><p>Estatein: ask your hosting provider to install a supported, patched PHP release. The October 2026 security fixes require PHP 8.3.36, 8.4.27, or 8.5.12 or newer in the respective branch (or a confirmed vendor backport). Theme code cannot patch a vulnerable PHP runtime.</p></div>';
    }
    if (!get_option('estatein_demo_imported')) {
        echo '<div class="notice notice-info"><p>Estatein is ready. To make the sample content editable, visit <a href="' .
            esc_url(admin_url('themes.php?page=estatein-setup')) .
            '">Appearance → Estatein setup</a> and import the demo.</p></div>';
    }
});

add_action('admin_menu', function () {
    add_theme_page(
        'Estatein setup',
        'Estatein setup',
        'manage_options',
        'estatein-setup',
        'estatein_setup_screen',
    );
    add_menu_page(
        'Subscribers',
        'Subscribers',
        'manage_options',
        'estatein-subscribers',
        'estatein_newsletter_admin',
        'dashicons-email-alt',
        30,
    );
});

function estatein_setup_screen()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    echo '<div class="wrap"><h1>Estatein setup</h1>';
    if (isset($_POST['estatein_import'])) {
        check_admin_referer('estatein_import_demo');
        $result = estatein_import_demo();
        echo '<div class="notice notice-' .
            (is_wp_error($result) ? 'error' : 'success') .
            '"><p>' .
            esc_html(
                is_wp_error($result)
                    ? $result->get_error_message()
                    : 'Demo content imported. Edit Home in Pages, then Properties, Testimonials, and FAQs in the sidebar.',
            ) .
            '</p></div>';
    }
    echo '<p>Import the assessment content once. The importer reuses its own records, does not replace an existing front page, and does not overwrite existing content on repeated runs.</p><form method="post">';
    wp_nonce_field('estatein_import_demo');
    submit_button('Import Estatein demo', 'primary', 'estatein_import');
    echo '</form><h2>Content guide</h2><ol><li>Select Home in Settings → Reading if your site already has a homepage.</li><li>Edit Home for headings, statistics, service labels, the hero image, and contact email. ACF Free provides image pickers; native fields work without it.</li><li>Edit Properties, Testimonials, and FAQs using their dashboard menus. Excerpts appear in cards; main content appears in detail dialogs. Featured images provide card images.</li><li>Use Appearance → Menus to assign optional custom navigation. Default navigation uses completed homepage sections.</li><li>Subscriber records are visible only to administrators under Subscribers.</li></ol><p>The full six-page Figma website is outside this homepage assessment scope. No mail delivery service is configured.</p></div>';
}

function estatein_import_image($filename, $alt)
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'numberposts' => 1,
        'meta_key' => '_estatein_demo_file',
        'meta_value' => $filename,
        'fields' => 'ids',
    ]);
    if ($existing) {
        return $existing[0];
    }
    $source = get_theme_file_path('/assets/images/' . basename($filename));
    if (
        !is_file($source) ||
        !in_array(
            pathinfo($source, PATHINFO_EXTENSION),
            ['png', 'jpg', 'webp'],
            true,
        )
    ) {
        return 0;
    }
    $upload = wp_upload_bits(
        basename($filename),
        null,
        file_get_contents($source),
    );
    if ($upload['error']) {
        return 0;
    }
    $id = wp_insert_attachment(
        [
            'post_mime_type' => wp_check_filetype($upload['file'])['type'],
            'post_title' => $alt,
            'post_status' => 'inherit',
        ],
        $upload['file'],
    );
    if (!$id || is_wp_error($id)) {
        return 0;
    }
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata(
        $id,
        wp_generate_attachment_metadata($id, $upload['file']),
    );
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    update_post_meta($id, '_estatein_demo_file', $filename);
    return $id;
}

function estatein_import_demo()
{
    if (!current_user_can('manage_options')) {
        return new WP_Error('forbidden', 'Administrator access is required.');
    }
    $existing = get_posts([
        'post_type' => 'page',
        'post_status' => 'any',
        'meta_key' => '_estatein_demo_home',
        'meta_value' => '1',
        'numberposts' => 1,
        'fields' => 'ids',
    ]);
    $page_id = $existing
        ? $existing[0]
        : wp_insert_post(
            [
                'post_title' => 'Home',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
                'meta_input' => ['_estatein_demo_home' => '1'],
            ],
            true,
        );
    if (is_wp_error($page_id)) {
        return $page_id;
    }
    foreach (estatein_defaults() as $key => $value) {
        add_post_meta($page_id, 'estatein_' . $key, $value, true);
    }
    if (!metadata_exists('post', $page_id, 'estatein_hero_image')) {
        update_post_meta(
            $page_id,
            'estatein_hero_image',
            estatein_import_image(
                '99b02.png',
                'Blue glass towers reaching into the sky',
            ),
        );
    }
    if (!get_option('page_on_front')) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_id);
    }
    $collections = [
        'property' => estatein_demo_properties(),
        'testimonial' => estatein_demo_testimonials(),
        'faq' => estatein_demo_faqs(),
    ];
    foreach ($collections as $type => $items) {
        foreach ($items as $index => $item) {
            $key = $type . '_' . $index;
            $found = get_posts([
                'post_type' => 'estatein_' . $type,
                'post_status' => 'any',
                'meta_key' => '_estatein_demo_key',
                'meta_value' => $key,
                'numberposts' => 1,
                'fields' => 'ids',
            ]);
            if ($found) {
                continue;
            }
            $id = wp_insert_post(
                [
                    'post_type' => 'estatein_' . $type,
                    'post_status' => 'publish',
                    'post_title' => $item['title'],
                    'post_excerpt' => $item['description'],
                    'post_content' => $item['answer'] ?? $item['description'],
                    'menu_order' => $index,
                    'meta_input' => ['_estatein_demo_key' => $key],
                ],
                true,
            );
            if (is_wp_error($id)) {
                return $id;
            }
            foreach (
                ['beds', 'baths', 'type', 'price', 'name', 'location']
                as $field
            ) {
                if (isset($item[$field])) {
                    update_post_meta($id, 'estatein_' . $field, $item[$field]);
                }
            }
            if (isset($item['image'])) {
                set_post_thumbnail(
                    $id,
                    estatein_import_image($item['image'], $item['title']),
                );
            }
        }
    }
    update_option('estatein_demo_imported', 1, false);
    estatein_newsletter_install();
    return $page_id;
}
