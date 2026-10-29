<?php
/** Exercise the full-site catalog query layer without changing seeded content. */
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
$query = fn($input, $page = 1, $limit = 6) => estatein_property_query(
    estatein_property_filters($input),
    $page,
    $limit,
);
$total = (int) wp_count_posts('estatein_property')->publish;
$assert(
    $query([])->found_posts === $total,
    'Default catalog contains published properties.',
);
$assert(count($query([], 1, 1)->posts) === 1, 'Pagination limits results.');
$assert(
    $query([], 2, 1)->posts[0]->ID !== $query([], 1, 1)->posts[0]->ID,
    'Pagination advances to another property.',
);
$assert(
    count($query(['property_search' => 'Seaside Serenity'])->posts) === 1,
    'Text search matches title.',
);
$assert(
    count($query(['min_price' => '550001'])->posts) === 0,
    'Minimum price excludes lower-priced properties.',
);
$assert(
    count($query(['max_price' => '549999'])->posts) === 0,
    'Maximum price excludes higher-priced properties.',
);
$assert(
    count($query(['property_bedrooms' => '4'])->posts) === 1,
    'Bedroom filter matches minimum count.',
);
$assert(
    count($query(['property_type' => 'unlisted-type'])->posts) === 0,
    'Type filter supports empty results.',
);
$assert(
    count($query(['min_price' => '600000', 'max_price' => '500000'])->posts) ===
        0,
    'Contradictory range is rejected.',
);
$assert(
    !empty(estatein_property_filters(['min_price' => 'NaN'])['errors']),
    'Non-finite numbers are rejected.',
);
$assert(
    !empty(estatein_property_filters(['property_bedrooms' => '2.5'])['errors']),
    'Fractional bedroom filter is rejected.',
);
$assert(
    '' ===
        estatein_property_filters(['property_search' => ['unexpected']])[
            'property_search'
        ],
    'Array request values never reach SQL.',
);
$assert(
    count($query(['property_type' => "Villa' OR 1=1 --"])->posts) === 0,
    'SQL-like type text cannot broaden results.',
);
$assert(
    'estatein_property' === get_post_type_object('estatein_property')->name &&
        is_post_type_viewable('estatein_property'),
    'Property detail routes are public.',
);
$assert(
    is_array(estatein_property_filter_options('type')) &&
        !estatein_property_filter_options('unsupported_field'),
    'Filter option keys are allowlisted.',
);
wp_set_current_user(0);
$assert(
    is_wp_error(estatein_import_site_pages()),
    'Guest cannot create site pages.',
);
WP_CLI::success(
    "{$checks} catalog, pagination, input, and permission assertions passed.",
);
