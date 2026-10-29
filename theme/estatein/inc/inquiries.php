<?php
/** Private contact/property inquiries, independent of email delivery. */
defined('ABSPATH') || exit();

function estatein_inquiries_install()
{
    if ('1' === get_option('estatein_inquiries_schema')) {
        return;
    }
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $table = $wpdb->prefix . 'estatein_inquiries';
    dbDelta(
        "CREATE TABLE {$table} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        full_name varchar(120) NOT NULL,
        email varchar(254) NOT NULL,
        phone varchar(40) NOT NULL DEFAULT '',
        inquiry_type varchar(20) NOT NULL DEFAULT 'general',
        property_id bigint(20) unsigned NOT NULL DEFAULT 0,
        message text NOT NULL,
        created_at datetime NOT NULL,
        consent tinyint(1) NOT NULL DEFAULT 1,
        PRIMARY KEY  (id),
        KEY created_at (created_at)
    ) " .
            $wpdb->get_charset_collate() .
            ';',
    );
    if (
        $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table)),
        ) === $table
    ) {
        update_option('estatein_inquiries_schema', '1', false);
    }
}
add_action('after_switch_theme', 'estatein_inquiries_install');
add_action('admin_init', 'estatein_inquiries_install');

function estatein_inquiry_types()
{
    return [
        'general' => 'General enquiry',
        'buy' => 'Buying a property',
        'sell' => 'Selling a property',
        'rent' => 'Renting a property',
        'invest' => 'Property investment',
    ];
}

/** Validate originals before normalization; oversized input is rejected, not truncated. */
function estatein_validate_inquiry($source)
{
    $raw = fn($key) => isset($source[$key]) && is_string($source[$key])
        ? trim(wp_unslash($source[$key]))
        : '';
    $errors = [];
    foreach (
        [
            'full_name',
            'first_name',
            'last_name',
            'email',
            'phone',
            'inquiry_type',
            'property_id',
            'message',
            'consent',
        ]
        as $key
    ) {
        if (isset($source[$key]) && !is_string($source[$key])) {
            $errors[$key] = 'Invalid field value.';
        }
    }
    $name = $raw('full_name');
    if ('' === $name) {
        $first = $raw('first_name');
        $last = $raw('last_name');
        if ('' === $first || '' === $last) {
            $errors['full_name'] = 'Please enter your full name.';
        }
        $name = trim($first . ' ' . $last);
    }
    $name = sanitize_text_field($name);
    if (mb_strlen($name) < 2 || mb_strlen($name) > 120) {
        $errors['full_name'] =
            'Please enter a name between 2 and 120 characters.';
    }
    $email = $raw('email');
    if (strlen($email) > 254 || !is_email($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    $phone = $raw('phone');
    if (
        '' !== $phone &&
        (strlen($phone) > 40 ||
            !preg_match('/^[+0-9(). #x-]{5,40}$/iD', $phone))
    ) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }
    $type = $raw('inquiry_type') ?: 'general';
    if (!isset(estatein_inquiry_types()[$type])) {
        $errors['inquiry_type'] = 'Please select an enquiry type.';
    }
    $message = sanitize_textarea_field($raw('message'));
    if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) {
        $errors['message'] =
            'Please enter a message between 10 and 5,000 characters.';
    }
    $property_raw = $raw('property_id');
    $property_id =
        '' === $property_raw
            ? 0
            : (ctype_digit($property_raw)
                ? (int) $property_raw
                : -1);
    if (
        $property_id < 0 ||
        ($property_id > 0 &&
            ('estatein_property' !== get_post_type($property_id) ||
                'publish' !== get_post_status($property_id)))
    ) {
        $errors['property_id'] = 'Please select an available property.';
    }
    if ('1' !== $raw('consent')) {
        $errors['consent'] =
            'Please agree to the privacy terms before sending.';
    }
    if ($errors) {
        return new WP_Error('invalid', 'Please check the highlighted fields.', [
            'fields' => $errors,
        ]);
    }
    return [
        'full_name' => $name,
        'email' => strtolower(sanitize_email($email)),
        'phone' => $phone,
        'inquiry_type' => $type,
        'property_id' => $property_id,
        'message' => $message,
        'created_at' => current_time('mysql', true),
        'consent' => 1,
    ];
}

function estatein_inquiry_messages()
{
    return [
        'success' => 'Thank you! Your enquiry has been saved.',
        'invalid' =>
            'Please check your details and complete all required fields.',
        'expired' => 'This form has expired. Reload the page and try again.',
        'limited' => 'Too many attempts. Please try again in a few minutes.',
        'error' => 'Your enquiry could not be saved. Please try again later.',
    ];
}

function estatein_inquiry_process()
{
    if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '')) {
        return ['invalid', 405, []];
    }
    if (
        !wp_verify_nonce(
            estatein_request_text($_POST, 'estatein_nonce', 100),
            'estatein_inquiry',
        )
    ) {
        return ['expired', 403, []];
    }
    if (!empty($_POST['company_website'])) {
        return ['invalid', 400, []];
    }
    $ip =
        isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])
            ? $_SERVER['REMOTE_ADDR']
            : 'unknown';
    $rate_key =
        'estatein_inquiry_rate_' . hash_hmac('sha256', $ip, wp_salt('nonce'));
    $attempts = (int) get_transient($rate_key);
    if ($attempts >= 5) {
        return ['limited', 429, []];
    }
    set_transient($rate_key, $attempts + 1, 10 * MINUTE_IN_SECONDS);
    $record = estatein_validate_inquiry($_POST);
    if (is_wp_error($record)) {
        return ['invalid', 400, $record->get_error_data()['fields'] ?? []];
    }
    if ('1' !== get_option('estatein_inquiries_schema')) {
        return ['error', 503, []];
    }
    global $wpdb;
    $saved = $wpdb->insert($wpdb->prefix . 'estatein_inquiries', $record, [
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%s',
        '%s',
        '%d',
    ]);
    return false === $saved ? ['error', 503, []] : ['success', 200, []];
}

function estatein_inquiry_submit()
{
    [$status, $code, $fields] = estatein_inquiry_process();
    nocache_headers();
    if (wp_doing_ajax()) {
        wp_send_json(
            [
                'success' => 200 === $code,
                'status' => $status,
                'message' => estatein_inquiry_messages()[$status],
                'fields' => $fields,
            ],
            $code,
        );
    }
    // Never accept a request-provided redirect URL.
    $id = absint(estatein_request_text($_POST, 'property_id', 20));
    $url =
        $id &&
        'estatein_property' === get_post_type($id) &&
        'publish' === get_post_status($id)
            ? get_permalink($id)
            : estatein_page_url('contact');
    wp_safe_redirect(
        add_query_arg('enquiry', $status, $url) . '#contact-form',
        303,
    );
    exit();
}
foreach (
    [
        'admin_post_estatein_inquiry',
        'admin_post_nopriv_estatein_inquiry',
        'wp_ajax_estatein_inquiry',
        'wp_ajax_nopriv_estatein_inquiry',
    ]
    as $hook
) {
    add_action($hook, 'estatein_inquiry_submit');
}
add_action('admin_menu', function () {
    add_menu_page(
        'Enquiries',
        'Enquiries',
        'manage_options',
        'estatein-inquiries',
        'estatein_inquiries_admin',
        'dashicons-format-chat',
        31,
    );
});

function estatein_inquiries_admin()
{
    if (!current_user_can('manage_options')) {
        return;
    }
    global $wpdb;
    $table = $wpdb->prefix . 'estatein_inquiries';
    if (isset($_POST['delete_inquiry'])) {
        check_admin_referer('estatein_delete_inquiry');
        if (!is_scalar($_POST['delete_inquiry'])) {
            wp_die('Invalid enquiry identifier.', '', ['response' => 400]);
        }
        $deleted = $wpdb->delete(
            $table,
            ['id' => absint($_POST['delete_inquiry'])],
            ['%d'],
        );
        echo '<div class="notice notice-' .
            (false === $deleted ? 'error' : 'success') .
            '"><p>' .
            esc_html(
                false === $deleted
                    ? 'Enquiry could not be removed.'
                    : 'Enquiry removed.',
            ) .
            '</p></div>';
    }
    $page = max(1, absint(estatein_request_text($_GET, 'paged', 10)));
    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d",
            25,
            ($page - 1) * 25,
        ),
    );
    echo '<div class="wrap"><h1>Contact and property enquiries</h1><p>Requests are stored privately here. No email delivery integration is configured. Dates are UTC.</p><table class="widefat striped"><thead><tr><th>Contact</th><th>Enquiry</th><th>Received</th><th>Action</th></tr></thead><tbody>';
    foreach ($rows as $row) {
        echo '<tr><td><strong>' .
            esc_html($row->full_name) .
            '</strong><br>' .
            esc_html($row->email) .
            '<br>' .
            esc_html($row->phone) .
            '</td><td><strong>' .
            esc_html(
                estatein_inquiry_types()[$row->inquiry_type] ?? 'Enquiry',
            ) .
            '</strong>';
        if ($row->property_id) {
            echo '<p>Property: ' .
                esc_html(
                    get_the_title($row->property_id) ?: 'Unavailable property',
                ) .
                '</p>';
        }
        echo '<p style="white-space:pre-wrap">' .
            esc_html($row->message) .
            '</p></td><td>' .
            esc_html($row->created_at) .
            '</td><td><form method="post">';
        wp_nonce_field('estatein_delete_inquiry');
        echo '<button class="button" name="delete_inquiry" value="' .
            (int) $row->id .
            '">Delete</button></form></td></tr>';
    }
    if (!$rows) {
        echo '<tr><td colspan="4">No enquiries yet.</td></tr>';
    }
    echo '</tbody></table>' .
        wp_kses_post(
            paginate_links([
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'current' => $page,
                'total' => (int) ceil($total / 25),
            ]),
        ) .
        '</div>';
}
