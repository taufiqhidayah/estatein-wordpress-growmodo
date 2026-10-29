<?php
/** Run only on the local fixture using wp eval-file tests/content.php. */
if (!defined('WP_CLI') || 'local' !== wp_get_environment_type()) {
    exit('Local CLI fixture required.');
}
$checks = 0;
$assert = function ($condition, $message) use (&$checks) {
    if (!$condition) {
        WP_CLI::error($message);
    }
    ++$checks;
};
wp_set_current_user(0);
$assert(
    is_wp_error(estatein_import_demo()),
    'Guest cannot import demo content.',
);
wp_set_current_user(1);
$counts = [];
foreach (
    ['estatein_property', 'estatein_testimonial', 'estatein_faq']
    as $type
) {
    $counts[$type] = wp_count_posts($type)->publish;
}
$front = get_option('page_on_front');
estatein_import_demo();
estatein_import_demo();
$assert(
    (int) get_option('page_on_front') === (int) $front,
    'Importer preserves front page.',
);
foreach ($counts as $type => $count) {
    $assert(
        (int) wp_count_posts($type)->publish === (int) $count,
        'Import remains idempotent: ' . $type,
    );
}
$old_title = get_post_meta($front, 'estatein_hero_title', true);
try {
    update_post_meta(
        $front,
        'estatein_hero_title',
        'A CMS edit for verification',
    );
    $assert(
        'A CMS edit for verification' === estatein_setting('hero_title'),
        'Homepage reads CMS changes.',
    );
    estatein_import_demo();
    $assert(
        'A CMS edit for verification' === estatein_setting('hero_title'),
        'Reimport preserves edits.',
    );
} finally {
    update_post_meta($front, 'estatein_hero_title', $old_title);
}
$property = estatein_records('property')[0];
$old_price = get_post_meta($property['id'], 'estatein_price', true);
try {
    update_post_meta($property['id'], 'estatein_price', '123456');
    $assert(
        '123456' === (string) estatein_records('property')[0]['price'],
        'Property query reads edited fields.',
    );
} finally {
    update_post_meta($property['id'], 'estatein_price', $old_price);
}
// Verify fallback native saving does not accept requests without nonce/capability.
$_POST = ['estatein_price' => '1'];
do_action('save_post', $property['id'], get_post($property['id']), true);
$assert(
    (string) get_post_meta($property['id'], 'estatein_price', true) ===
        (string) $old_price,
    'Missing nonce cannot overwrite metadata.',
);
$_POST = [
    'estatein_fields_nonce' => wp_create_nonce('estatein_save_fields'),
    'estatein_price' => '1',
];
wp_set_current_user(0);
do_action('save_post', $property['id'], get_post($property['id']), true);
$assert(
    (string) get_post_meta($property['id'], 'estatein_price', true) ===
        (string) $old_price,
    'Guest cannot overwrite metadata.',
);
wp_set_current_user(1);
$_POST = [
    'estatein_fields_nonce' => wp_create_nonce('estatein_save_fields'),
    'estatein_price' => '987654',
];
try {
    do_action('save_post', $property['id'], get_post($property['id']), true);
    $assert(
        '987654' ===
            (string) get_post_meta($property['id'], 'estatein_price', true),
        'Authorized native-field save succeeds.',
    );
} finally {
    update_post_meta($property['id'], 'estatein_price', $old_price);
}
$_POST = [];
WP_CLI::success("{$checks} content and permission assertions passed.");
