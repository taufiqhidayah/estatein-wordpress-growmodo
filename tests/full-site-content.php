<?php
if (!defined('WP_CLI') || 'local' !== wp_get_environment_type()) {
    exit('Local CLI fixture required.');
}
$checks = 0;
$assert = function ($ok, $message) use (&$checks) {
    if (!$ok) { WP_CLI::error($message); }
    ++$checks;
};
wp_set_current_user(1);
$before = get_option('estatein_site_page_ids');
$ids = estatein_import_full_site();
$assert(!is_wp_error($ids) && $ids === $before, 'Reimport retains page identities.');
foreach ($ids as $key => $id) {
    $assert('publish' === get_post_status($id), $key . ' is published.');
}
$id = $ids['services'];
$meta = 'estatein_page_intro_title';
$old = get_post_meta($id, $meta, true);
try {
    $_POST = [$meta => 'Unauthorized change'];
    do_action('save_post_page', $id);
    $assert($old === get_post_meta($id, $meta, true), 'Page save rejects missing nonce.');
    $_POST = ['estatein_page_nonce' => wp_create_nonce('estatein_save_page_content'), $meta => 'CMS QA title'];
    do_action('save_post_page', $id);
    $assert('CMS QA title' === estatein_page_value('intro_title', 'services', $id), 'Page reads authorized CMS edit.');
    estatein_import_full_site();
    $assert('CMS QA title' === estatein_page_value('intro_title', 'services', $id), 'Import preserves inner-page edits.');
    wp_set_current_user(0);
    $_POST = ['estatein_page_nonce' => wp_create_nonce('estatein_save_page_content'), $meta => 'Guest edit'];
    do_action('save_post_page', $id);
    $assert('CMS QA title' === get_post_meta($id, $meta, true), 'Guest cannot change page fields.');
    $assert(is_wp_error(estatein_import_full_site()), 'Guest cannot import full site.');
} finally {
    wp_set_current_user(1);
    update_post_meta($id, $meta, $old);
    $_POST = [];
}
WP_CLI::success("{$checks} full-site import and CMS permission assertions passed.");
