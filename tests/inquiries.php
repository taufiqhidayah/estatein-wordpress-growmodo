<?php
/** Test-only records are deleted before and after this local fixture run. */
if (!defined('WP_CLI') || 'local' !== wp_get_environment_type()) {
    exit('Local CLI fixture required.');
}
wp_set_current_user(0);
estatein_inquiries_install();
$checks = 0;
$assert = function ($condition, $message) use (&$checks) {
    if (!$condition) {
        WP_CLI::error($message);
    }
    ++$checks;
};
$property = estatein_records('property')[0]['id'];
$base = [
    'full_name' => 'Estatein QA',
    'email' => 'estatein-inquiry-qa@example.test',
    'phone' => '+62 812 0000 0000',
    'inquiry_type' => 'buy',
    'property_id' => (string) $property,
    'message' => 'Please share more details about this property.',
    'consent' => '1',
];
$valid = estatein_validate_inquiry($base);
$assert(
    !is_wp_error($valid) && $valid['property_id'] === $property,
    'Valid property enquiry is accepted.',
);
foreach (
    [
        ['email' => "qa@example.test\r\nBcc:other@example.test"],
        ['email' => ['unexpected']],
        ['phone' => ['unexpected']],
        ['phone' => "+62\r\n1234"],
        ['full_name' => str_repeat('x', 121)],
        ['message' => 'short'],
        ['message' => str_repeat('x', 5001)],
        ['property_id' => '-1'],
        ['property_id' => '999999999'],
        ['consent' => '0'],
        ['inquiry_type' => 'invalid'],
    ]
    as $override
) {
    $assert(
        is_wp_error(estatein_validate_inquiry(array_replace($base, $override))),
        'Malformed field is rejected: ' . array_key_first($override),
    );
}
$split = $base;
unset($split['full_name']);
$split['first_name'] = 'Estatein';
$split['last_name'] = 'QA';
$assert(
    'Estatein QA' === estatein_validate_inquiry($split)['full_name'],
    'Split contact names normalize consistently.',
);
$_SERVER['REMOTE_ADDR'] = '127.0.0.77';
$rate_key =
    'estatein_inquiry_rate_' .
    hash_hmac('sha256', $_SERVER['REMOTE_ADDR'], wp_salt('nonce'));
global $wpdb;
$cleanup = function () use ($wpdb, $base, $rate_key) {
    $wpdb->delete(
        $wpdb->prefix . 'estatein_inquiries',
        ['email' => $base['email']],
        ['%s'],
    );
    delete_transient($rate_key);
};
$cleanup();
try {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_POST = $base;
    $assert(405 === estatein_inquiry_process()[1], 'Only POST is accepted.');
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $assert(
        403 === estatein_inquiry_process()[1],
        'Missing nonce is rejected.',
    );
    $_POST['estatein_nonce'] = wp_create_nonce('estatein_inquiry');
    $_POST['company_website'] = 'spam.example';
    $assert(400 === estatein_inquiry_process()[1], 'Honeypot is rejected.');
    unset($_POST['company_website']);
    $assert(
        'success' === estatein_inquiry_process()[0],
        'Valid request is actually saved.',
    );
    $record = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}estatein_inquiries WHERE email = %s",
            $base['email'],
        ),
    );
    $assert(
        $record &&
            $record->message === $base['message'] &&
            (int) $record->property_id === $property,
        'Stored request retains the message and property.',
    );
    $_POST['email'] = 'invalid-email';
    for ($i = 0; $i < 4; ++$i) {
        $assert(
            400 === estatein_inquiry_process()[1],
            'Invalid request cannot create an enquiry.',
        );
    }
    $assert(
        429 === estatein_inquiry_process()[1],
        'Rate limit blocks further requests.',
    );
    ob_start();
    estatein_inquiries_admin();
    $guest_output = ob_get_clean();
    $assert('' === $guest_output, 'Guest cannot read enquiry records.');
    $assert(
        1 ===
            (int) $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}estatein_inquiries WHERE email = %s",
                    $base['email'],
                ),
            ),
        'Only the valid enquiry was persisted.',
    );
} finally {
    $cleanup();
    $_POST = [];
}
WP_CLI::success(
    "{$checks} enquiry validation, persistence, abuse, and privacy assertions passed.",
);
