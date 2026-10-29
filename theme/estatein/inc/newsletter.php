<?php
/** Newsletter storage. No mail delivery or external marketing service is implied. */
defined('ABSPATH') || exit();

function estatein_newsletter_install()
{
    if ('1' === get_option('estatein_newsletter_schema')) {
        return;
    }
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $table = $wpdb->prefix . 'estatein_subscribers';
    dbDelta(
        "CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        email varchar(254) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY email (email)
    ) " .
            $wpdb->get_charset_collate() .
            ';',
    );
    if (
        $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table)),
        ) === $table
    ) {
        update_option('estatein_newsletter_schema', '1', false);
    }
}
add_action('after_switch_theme', 'estatein_newsletter_install');
add_action('admin_init', 'estatein_newsletter_install');

function estatein_newsletter_messages()
{
    return [
        'success' => 'Thank you! Your subscription is recorded.',
        'invalid' => 'Please enter a valid email address.',
        'expired' => 'This form has expired. Reload the page and try again.',
        'limited' => 'Too many attempts. Please try again in a few minutes.',
        'error' =>
            'Your subscription could not be saved. Please try again later.',
    ];
}

function estatein_newsletter_process()
{
    if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
        return ['invalid', 405];
    }
    $nonce =
        isset($_POST['estatein_nonce']) && is_string($_POST['estatein_nonce'])
            ? sanitize_text_field(wp_unslash($_POST['estatein_nonce']))
            : '';
    if (!wp_verify_nonce($nonce, 'estatein_newsletter')) {
        return ['expired', 403];
    }
    if (!empty($_POST['company_website'])) {
        return ['invalid', 400];
    }
    // Do not trust spoofable forwarded headers; the hosting proxy must set REMOTE_ADDR correctly.
    $ip =
        isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])
            ? $_SERVER['REMOTE_ADDR']
            : 'unknown';
    $rate_key = 'estatein_rate_' . hash_hmac('sha256', $ip, wp_salt('nonce'));
    $attempts = (int) get_transient($rate_key);
    if ($attempts >= 5) {
        return ['limited', 429];
    }
    set_transient($rate_key, $attempts + 1, 10 * MINUTE_IN_SECONDS);
    $raw =
        isset($_POST['email']) && is_string($_POST['email'])
            ? trim(wp_unslash($_POST['email']))
            : '';
    // Validate the original input rather than silently turning malformed input into another address.
    if (strlen($raw) > 254 || !is_email($raw)) {
        return ['invalid', 400];
    }
    if ('1' !== get_option('estatein_newsletter_schema')) {
        return ['error', 503];
    }
    $email = strtolower(sanitize_email($raw));
    global $wpdb;
    $table = $wpdb->prefix . 'estatein_subscribers';
    // The unique index resolves concurrent duplicate submissions without creating multiple records.
    $result = $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO {$table} (email, created_at) VALUES (%s, %s) ON DUPLICATE KEY UPDATE email = VALUES(email)",
            $email,
            current_time('mysql', true),
        ),
    );
    if (false === $result) {
        return ['error', 503];
    }
    // Identical public responses avoid disclosing whether an address was already subscribed.
    return ['success', 200];
}

function estatein_newsletter_submit()
{
    [$status, $code] = estatein_newsletter_process();
    nocache_headers();
    if (wp_doing_ajax()) {
        wp_send_json(
            [
                'success' => 200 === $code,
                'status' => $status,
                'message' => estatein_newsletter_messages()[$status],
            ],
            $code,
        );
    }
    wp_safe_redirect(
        add_query_arg('subscription', $status, home_url('/')) . '#newsletter',
        303,
    );
    exit();
}
foreach (
    [
        'admin_post_estatein_subscribe',
        'admin_post_nopriv_estatein_subscribe',
        'wp_ajax_estatein_subscribe',
        'wp_ajax_nopriv_estatein_subscribe',
    ]
    as $hook
) {
    add_action($hook, 'estatein_newsletter_submit');
}

function estatein_newsletter_admin()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    global $wpdb;
    $table = $wpdb->prefix . 'estatein_subscribers';
    if (isset($_POST['delete_subscriber'])) {
        check_admin_referer('estatein_delete_subscriber');
        $wpdb->delete(
            $table,
            ['id' => absint($_POST['delete_subscriber'])],
            ['%d'],
        );
        echo '<div class="notice notice-success"><p>Subscriber removed.</p></div>';
    }
    $page = max(1, absint($_GET['paged'] ?? 1));
    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, email, created_at FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d",
            25,
            ($page - 1) * 25,
        ),
    );
    echo '<div class="wrap"><h1>Property updates subscribers</h1><p>These addresses are stored locally. No emails are sent automatically. Delete an address when its owner requests removal.</p><table class="widefat striped"><thead><tr><th>Email</th><th>Subscribed (UTC)</th><th>Action</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        echo '<tr><td>' .
            esc_html($row->email) .
            '</td><td>' .
            esc_html($row->created_at) .
            '</td><td><form method="post">';
        wp_nonce_field('estatein_delete_subscriber');
        echo '<button class="button" name="delete_subscriber" value="' .
            (int) $row->id .
            '">Delete</button></form></td></tr>';
    }
    if (!$rows) {
        echo '<tr><td colspan="3">No subscribers yet.</td></tr>';
    }
    echo '</tbody></table>';
    echo wp_kses_post(
        paginate_links([
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'current' => $page,
            'total' => (int) ceil($total / 25),
        ]),
    );
    echo '</div>';
}
