<?php
/** Routing and catalog infrastructure shared by the full six-page site. */
defined("ABSPATH") || exit();

function estatein_site_pages()
{
    return [
        "about" => ["title" => "About Us", "slug" => "about-us"],
        "properties" => ["title" => "Properties", "slug" => "properties"],
        "services" => ["title" => "Services", "slug" => "services"],
        "contact" => ["title" => "Contact Us", "slug" => "contact"],
    ];
}

/** Reuses only pages owned by this importer. Unfinished templates remain drafts. */
function estatein_import_site_pages()
{
    if (!current_user_can("manage_options")) {
        return new WP_Error("forbidden", "Administrator access is required.");
    }
    $ids = [];
    foreach (estatein_site_pages() as $key => $page) {
        $template = "page-" . $page["slug"] . ".php";
        if (!is_file(get_theme_file_path("/" . $template))) {
            continue;
        }
        $existing = get_posts([
            "post_type" => "page",
            "post_status" => "any",
            "meta_key" => "_estatein_page_key",
            "meta_value" => $key,
            "numberposts" => 1,
            "fields" => "ids",
        ]);
        if ($existing) {
            $ids[$key] = (int) $existing[0];
            continue;
        }
        if (get_page_by_path($page["slug"], OBJECT, "page")) {
            return new WP_Error(
                "page_conflict",
                sprintf(
                    "A page already uses /%s/. Assign or rename it before importing; it was not changed.",
                    $page["slug"],
                ),
            );
        }
        $ready = is_file(
            get_theme_file_path("/page-" . $page["slug"] . ".php"),
        );
        $id = wp_insert_post(
            [
                "post_type" => "page",
                "post_title" => $page["title"],
                "post_name" => $page["slug"],
                "post_status" => $ready ? "publish" : "draft",
                "post_content" => "",
                "meta_input" => [
                    "_estatein_page_key" => $key,
                    "_wp_page_template" => $template,
                ],
            ],
            true,
        );
        if (is_wp_error($id)) {
            return $id;
        }
        $ids[$key] = (int) $id;
    }
    update_option("estatein_site_page_ids", $ids, false);
    return $ids;
}

function estatein_page_url($key)
{
    $pages = estatein_site_pages();
    if (!isset($pages[$key])) {
        return home_url("/");
    }
    $id = (int) (get_option("estatein_site_page_ids", [])[$key] ?? 0);
    if (
        $id &&
        "publish" === get_post_status($id) &&
        "page" === get_post_type($id)
    ) {
        return get_permalink($id);
    }
    return home_url("/" . $pages[$key]["slug"] . "/");
}

/** Scalar extraction prevents array-shaped request values entering the query layer. */
function estatein_request_text($source, $key, $limit = 100)
{
    if (!isset($source[$key]) || !is_string($source[$key])) {
        return "";
    }
    return mb_substr(sanitize_text_field(wp_unslash($source[$key])), 0, $limit);
}

function estatein_property_filters($source)
{
    $filters = [
        "property_search" => estatein_request_text($source, "property_search"),
        "property_location" => estatein_request_text(
            $source,
            "property_location",
            80,
        ),
        "property_type" => estatein_request_text($source, "property_type", 40),
        "property_bedrooms" => "",
        "min_price" => "",
        "max_price" => "",
        "price_range" => estatein_request_text($source, "price_range", 30),
        "property_size" => estatein_request_text($source, "property_size", 30),
        "property_year" => estatein_request_text($source, "property_year", 30),
        "errors" => [],
    ];
    foreach (["min_price", "max_price", "property_bedrooms"] as $key) {
        $raw = estatein_request_text($source, $key, 40);
        if ("" === $raw) {
            continue;
        }
        $number = filter_var($raw, FILTER_VALIDATE_FLOAT);
        if (
            false === $number ||
            !is_finite($number) ||
            $number < 0 ||
            $number > 1000000000000 ||
            ("property_bedrooms" === $key &&
                (floor($number) !== $number || $number > 100))
        ) {
            $filters["errors"][] =
                "Please enter a valid non-negative value for " .
                str_replace("_", " ", $key) .
                ".";
            continue;
        }
        $filters[$key] = "property_bedrooms" === $key ? (int) $number : $number;
    }
    $ranges = [
        "price_range" => [
            "under-500k" => [0, 500000],
            "500k-1m" => [500000, 1000000],
            "1m-2m" => [1000000, 2000000],
            "over-2m" => [2000000, null],
        ],
        "property_size" => [
            "small" => [1, 1499],
            "medium" => [1500, 2999],
            "large" => [3000, null],
        ],
        "property_year" => [
            "before-2000" => [1800, 1999],
            "2000-2019" => [2000, 2019],
            "2020-plus" => [2020, null],
        ],
    ];
    foreach ($ranges as $key => $options) {
        if ("" === $filters[$key]) {
            continue;
        }
        if (!isset($options[$filters[$key]])) {
            $filters["errors"][] = "Please select a valid filter option.";
            continue;
        }
        [$min, $max] = $options[$filters[$key]];
        if ("price_range" === $key) {
            $filters["min_price"] =
                "" === $filters["min_price"]
                    ? $min
                    : max($min, $filters["min_price"]);
            if (null !== $max) {
                $filters["max_price"] =
                    "" === $filters["max_price"]
                        ? $max
                        : min($max, $filters["max_price"]);
            }
        } else {
            $filters[$key . "_bounds"] = [$min, $max];
        }
    }
    if (
        "" !== $filters["min_price"] &&
        "" !== $filters["max_price"] &&
        $filters["min_price"] > $filters["max_price"]
    ) {
        $filters["errors"][] = "Minimum price cannot exceed maximum price.";
    }
    return $filters;
}

/** GET filters use a separate query, so the catalog page remains a WordPress page. */
function estatein_property_query($filters, $page = 1, $per_page = 6)
{
    $args = [
        "post_type" => "estatein_property",
        "post_status" => "publish",
        "posts_per_page" => min(24, max(1, (int) $per_page)),
        "paged" => min(10000, max(1, (int) $page)),
        "orderby" => ["menu_order" => "ASC", "ID" => "ASC"],
    ];
    if (!empty($filters["errors"])) {
        $args["post__in"] = [0];
        return new WP_Query($args);
    }
    if ("" !== ($filters["property_search"] ?? "")) {
        $args["s"] = $filters["property_search"];
    }
    $meta = [];
    foreach (
        ["property_location" => "location", "property_type" => "type"]
        as $input => $field
    ) {
        if ("" !== ($filters[$input] ?? "")) {
            $meta[] = [
                "key" => "estatein_" . $field,
                "value" => $filters[$input],
                "compare" => "=",
            ];
        }
    }
    if ("" !== ($filters["property_bedrooms"] ?? "")) {
        $meta[] = [
            "key" => "estatein_beds",
            "value" => (int) $filters["property_bedrooms"],
            "type" => "NUMERIC",
            "compare" => ">=",
        ];
    }
    foreach (["min_price" => ">=", "max_price" => "<="] as $input => $compare) {
        if ("" !== ($filters[$input] ?? "")) {
            $meta[] = [
                "key" => "estatein_price",
                "value" => (float) $filters[$input],
                "type" => "DECIMAL(20,2)",
                "compare" => $compare,
            ];
        }
    }
    foreach (
        ["property_size" => "area", "property_year" => "build_year"]
        as $key => $field
    ) {
        if (!isset($filters[$key . "_bounds"])) {
            continue;
        }
        [$min, $max] = $filters[$key . "_bounds"];
        $meta[] = [
            "key" => "estatein_" . $field,
            "value" => $min,
            "type" => "NUMERIC",
            "compare" => ">=",
        ];
        if (null !== $max) {
            $meta[] = [
                "key" => "estatein_" . $field,
                "value" => $max,
                "type" => "NUMERIC",
                "compare" => "<=",
            ];
        }
    }
    if ($meta) {
        $args["meta_query"] = array_merge(["relation" => "AND"], $meta);
    }
    return new WP_Query($args);
}

function estatein_property_filter_options($field)
{
    if (!in_array($field, ["location", "type"], true)) {
        return [];
    }
    global $wpdb;
    // Both the meta key and status/type values are prepared; no request-controlled SQL identifiers.
    return $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND pm.meta_value <> '' AND p.post_type = %s AND p.post_status = %s ORDER BY pm.meta_value ASC LIMIT 100",
            "estatein_" . $field,
            "estatein_property",
            "publish",
        ),
    );
}

add_action(
    "init",
    function () {
        // Flush only once after the public property route is added, never on each request.
        if ("2" !== get_option("estatein_routes_version")) {
            flush_rewrite_rules(false);
            update_option("estatein_routes_version", "2", false);
        }
    },
    99,
);
