<?php
defined("ABSPATH") || exit();
get_header();
while (have_posts()):

    the_post();
    $item = estatein_property_record(get_post());
    $images = [];
    if ($item["image_id"]) {
        $images[] = (int) $item["image_id"];
    }
    for ($i = 1; $i <= 3; $i++) {
        $id = absint(
            get_post_meta(get_the_ID(), "estatein_gallery_image_" . $i, true),
        );
        if ($id && wp_attachment_is_image($id)) {
            $images[] = $id;
        }
    }
    $images = array_values(array_unique($images));
    ?>
<main id="main" class="inner-page property-page wrap">
    <nav class="breadcrumbs" aria-label="Breadcrumb"><ol><li><a href="<?php echo esc_url(
        home_url("/"),
    ); ?>">Home</a></li><li><a href="<?php echo esc_url(
    estatein_destination("properties"),
); ?>">Properties</a></li><li aria-current="page"><?php the_title(); ?></li></ol></nav>
    <header class="property-heading"><div><h1><?php the_title(); ?></h1><p class="property-location"><?php
estatein_asset("filter-location.png", "", "tiny-icon");
echo esc_html($item["location"]);
?></p></div><p class="detail-price"><span>Price</span><strong><?php echo esc_html(
    '$' . number_format_i18n((float) $item["price"]),
); ?></strong></p></header>
    <section class="property-gallery" aria-label="Property photos" data-property-gallery><?php if (
        $images
    ): ?><div class="property-gallery-stage"><?php foreach (
    $images
    as $index => $id
): ?><figure <?php echo $index
    ? "hidden"
    : ""; ?> data-gallery-slide><?php echo wp_get_attachment_image(
     $id,
     "full",
     false,
     [
         "alt" => $item["title"] . " — photo " . ($index + 1),
         "loading" => $index ? "lazy" : "eager",
         "fetchpriority" => $index ? "auto" : "high",
     ],
 ); ?></figure><?php endforeach; ?></div><?php if (
    count($images) > 1
): ?><div class="gallery-thumbnails" aria-label="Choose a photo"><?php foreach (
    $images
    as $index => $id
): ?><button type="button" data-gallery-index="<?php echo $index; ?>" aria-label="<?php echo esc_attr(
    "Show photo " . ($index + 1),
); ?>" aria-pressed="<?php echo $index
    ? "false"
    : "true"; ?>"><?php echo wp_get_attachment_image($id, "thumbnail", false, [
    "alt" => "",
]); ?></button><?php endforeach; ?></div><?php endif;endif; ?></section>
    <div class="property-information"><section class="detail-panel"><h2>Description</h2><div class="rich-text"><?php the_content(); ?></div><dl class="property-specs"><?php foreach (
    ["beds" => "Bedrooms", "baths" => "Bathrooms", "area" => "Area"]
    as $key => $label
): ?><div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html(
    $item[$key] . ("area" === $key ? " sq ft" : ""),
); ?></dd></div><?php endforeach; ?></dl></section><section class="detail-panel"><h2>Key Features and Amenities</h2><ul class="amenity-list"><?php foreach (
    preg_split('/\r\n|\r|\n/', $item["amenities"])
    as $amenity
) {
    if (trim($amenity)) {
        echo '<li><span aria-hidden="true">✦</span>' .
            esc_html($amenity) .
            "</li>";
    }
} ?></ul><dl class="additional-specs"><div><dt>Property Type</dt><dd><?php echo esc_html(
    $item["type"],
); ?></dd></div><div><dt>Year Built</dt><dd><?php echo esc_html(
    $item["build_year"],
); ?></dd></div><div><dt>Parking</dt><dd><?php echo esc_html(
    $item["parking"],
); ?></dd></div></dl></section></div>
    <section class="property-enquiry content-section"><div><?php estatein_section_heading(
        "Inquire About " . $item["title"],
        "Interested in this property? Share your details and questions with our team. Your enquiry will be saved for the website administrator to review.",
    ); ?><a class="button" href="<?php echo esc_url(
    estatein_destination("contact", "contact-form"),
); ?>">Contact Our Team</a></div><?php get_template_part(
    "template-parts/inquiry-form",
    null,
    ["context" => "property", "property_id" => $item["id"]],
); ?></section>
    <?php
    $related = new WP_Query([
        "post_type" => "estatein_property",
        "post_status" => "publish",
        "post__not_in" => [$item["id"]],
        "posts_per_page" => 3,
        "orderby" => ["menu_order" => "ASC", "ID" => "ASC"],
    ]);
    if (
        $related->posts
    ): ?><section class="content-section related-properties"><?php estatein_section_heading(
    "Similar Properties",
    "Explore more properties and compare the possibilities for your next move.",
); ?><div class="catalog-grid"><?php foreach ($related->posts as $property) {
    get_template_part("template-parts/catalog-card", null, [
        "item" => estatein_property_record($property),
    ]);
} ?></div></section><?php endif;
    ?>
</main>
<?php
endwhile;
get_footer(); ?>
