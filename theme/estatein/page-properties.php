<?php
/** Template Name: Estatein Properties */
defined("ABSPATH") || exit();
$filters = estatein_property_filters($_GET);
$page = max(1, absint(estatein_request_text($_GET, "property_page", 8)));
$query = estatein_property_query($filters, $page, 3);
if ($page > max(1, (int) $query->max_num_pages)) {
    $page = max(1, (int) $query->max_num_pages);
    $query = estatein_property_query($filters, $page, 3);
}
get_header();
?>
<main id="main" class="inner-page properties-page">
    <?php estatein_page_intro(); ?>
    <form class="catalog-search wrap" method="get" action="<?php echo esc_url(
        get_permalink(),
    ); ?>" role="search" aria-label="Search properties">
        <div class="search-row"><label for="property-search" class="screen-reader-text">Search for a property</label><input id="property-search" name="property_search" type="search" maxlength="100" placeholder="Search For A Property" value="<?php echo esc_attr(
            $filters["property_search"],
        ); ?>"><button class="button button-primary" type="submit"><?php estatein_asset(
    "filter-search.png",
    "",
    "tiny-icon",
); ?><span>Find Property</span></button></div>
        <div class="filter-row">
            <?php
            $locations = estatein_property_filter_options("location");
            $types = estatein_property_filter_options("type");
            $options = [
                "property_location" => [
                    "Location",
                    "location",
                    array_combine($locations, $locations) ?: [],
                ],
                "property_type" => [
                    "Property Type",
                    "type",
                    array_combine($types, $types) ?: [],
                ],
                "price_range" => [
                    "Pricing Range",
                    "price",
                    [
                        "under-500k" => 'Under $500,000',
                        "500k-1m" => '$500k – $1 million',
                        "1m-2m" => '$1m – $2 million',
                        "over-2m" => 'Over $2 million',
                    ],
                ],
                "property_size" => [
                    "Property Size",
                    "size",
                    [
                        "small" => "Under 1,500 sq ft",
                        "medium" => "1,500 – 2,999 sq ft",
                        "large" => "3,000+ sq ft",
                    ],
                ],
                "property_year" => [
                    "Build Year",
                    "year",
                    [
                        "before-2000" => "Before 2000",
                        "2000-2019" => "2000 – 2019",
                        "2020-plus" => "2020 or newer",
                    ],
                ],
            ];
            foreach ($options as $key => $option): ?>
            <div class="filter-field"><?php estatein_asset(
                "filter-" . $option[1] . ".png",
                "",
                "filter-icon",
            ); ?><label class="screen-reader-text" for="filter-<?php echo esc_attr(
    $key,
); ?>"><?php echo esc_html(
    $option[0],
); ?></label><select id="filter-<?php echo esc_attr(
    $key,
); ?>" name="<?php echo esc_attr(
    $key,
); ?>"><option value=""><?php echo esc_html(
    $option[0],
); ?></option><?php foreach (
    $option[2]
    as $value => $label
): ?><option value="<?php echo esc_attr($value); ?>" <?php selected(
    $filters[$key],
    $value,
); ?>><?php echo esc_html(
    $label,
); ?></option><?php endforeach; ?></select></div>
            <?php endforeach;
            ?>
        </div>
    </form>
    <?php estatein_page_editor_content(); ?>
    <section id="catalog" class="content-section wrap">
        <?php estatein_section_heading(
            estatein_page_value("catalog_title"),
            estatein_page_value("catalog_description"),
        ); ?>
        <?php if (
            $filters["errors"]
        ): ?><div class="catalog-feedback" role="alert"><?php foreach (
    $filters["errors"]
    as $error
) {
    echo "<p>" . esc_html($error) . "</p>";
} ?></div><?php endif; ?>
        <?php if ($query->posts): ?><div class="catalog-grid"><?php foreach (
    $query->posts
    as $property
) {
    get_template_part("template-parts/catalog-card", null, [
        "item" => estatein_property_record($property),
    ]);
} ?></div><?php else: ?><div class="empty-state"><h3>No properties found</h3><p>Try another location, price range, or property type.</p><a class="button" href="<?php echo esc_url(
    get_permalink(),
); ?>">Clear filters</a></div><?php endif; ?>
        <div class="catalog-pagination"><p><span><?php echo esc_html(
            str_pad((string) $page, 2, "0", STR_PAD_LEFT),
        ); ?></span> of <?php echo esc_html(
    str_pad((string) max(1, $query->max_num_pages), 2, "0", STR_PAD_LEFT),
); ?></p><div><?php foreach (
    [-1 => "Previous page", 1 => "Next page"]
    as $direction => $label
):
    $valid =
        $page + $direction >= 1 &&
        $page + $direction <=
            $query->max_num_pages; ?><a class="arrow-button<?php echo $valid
    ? ""
    : " is-disabled"; ?>" <?php if ($valid) {
    echo 'href="' .
        esc_url(
            add_query_arg(
                "property_page",
                $page + $direction,
                get_permalink() .
                    "?" .
                    http_build_query(
                        array_diff_key($filters, [
                            "errors" => 1,
                            "property_size_bounds" => 1,
                            "property_year_bounds" => 1,
                        ]),
                    ),
            ),
        ) .
        '#catalog"';
} else {
    echo 'aria-disabled="true" tabindex="-1"';
} ?> aria-label="<?php echo esc_attr($label); ?>"><?php estatein_asset(
    -1 === $direction ? "30f05.svg" : "65841.svg",
    -1 === $direction ? "c00b5.svg" : "aba3a.svg",
); ?></a><?php
endforeach; ?></div></div>
    </section>
    <section class="content-section wrap"><?php
    estatein_section_heading(
        estatein_page_value("form_title"),
        estatein_page_value("form_description"),
    );
    get_template_part("template-parts/inquiry-form", null, [
        "context" => "property-search",
    ]);
    ?></section>
</main>
<?php get_footer(); ?>
