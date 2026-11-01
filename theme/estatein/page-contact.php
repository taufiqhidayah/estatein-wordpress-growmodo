<?php
/** Template Name: Estatein Contact */
defined("ABSPATH") || exit();
get_header();
?>
<main id="main" class="inner-page contact-page">
    <?php estatein_page_intro(); ?>
    <div class="services contact-links" aria-label="Contact options">
        <a class="service-card" href="<?php echo esc_url(
            "mailto:" . sanitize_email(estatein_page_value("email")),
        ); ?>"><?php estatein_asset(
    "contact-icon-1.png",
    "",
    "service-static-icon",
); ?><span><?php echo esc_html(
    estatein_page_value("email"),
); ?></span><?php estatein_asset(
    "900d3.svg",
    "e9405.svg",
    "service-arrow",
); ?></a>
        <a class="service-card" href="<?php echo esc_url(
            "tel:" .
                preg_replace("/[^+0-9]/", "", estatein_page_value("phone")),
        ); ?>"><?php estatein_asset(
    "contact-icon-2.png",
    "",
    "service-static-icon",
); ?><span><?php echo esc_html(
    estatein_page_value("phone"),
); ?></span><?php estatein_asset(
    "900d3.svg",
    "e9405.svg",
    "service-arrow",
); ?></a>
        <a class="service-card" href="#offices"><?php estatein_asset(
            "contact-icon-3.png",
            "",
            "service-static-icon",
        ); ?><span>Main Headquarters</span><?php estatein_asset(
    "900d3.svg",
    "e9405.svg",
    "service-arrow",
); ?></a>
        <div class="service-card contact-social-card"><?php estatein_asset(
            "contact-icon-4.png",
            "",
            "service-static-icon",
        ); ?><div><a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Instagram</a><a href="https://www.linkedin.com/" target="_blank" rel="noopener noreferrer">LinkedIn</a><a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">Facebook</a></div><?php estatein_asset(
    "900d3.svg",
    "e9405.svg",
    "service-arrow",
); ?></div>
    </div>
    <?php estatein_page_editor_content(); ?>
    <section class="content-section wrap"><?php
    estatein_section_heading(
        estatein_page_value("form_title"),
        estatein_page_value("form_description"),
    );
    get_template_part("template-parts/inquiry-form", null, [
        "context" => "contact",
    ]);
    ?></section>
    <section id="offices" class="content-section wrap" data-offices>
        <?php estatein_section_heading(
            estatein_page_value("offices_title"),
            estatein_page_value("offices_description"),
        ); ?>
        <div class="office-tabs" role="tablist" aria-label="Office region"><?php foreach (
            [
                "all" => "All",
                "regional" => "Regional",
                "international" => "International",
            ]
            as $value => $label
        ): ?><button type="button" id="office-tab-<?php echo esc_attr(
    $value,
); ?>" role="tab" aria-controls="office-results" aria-selected="<?php echo "all" ===
$value
    ? "true"
    : "false"; ?>" tabindex="<?php echo "all" === $value
    ? "0"
    : "-1"; ?>" data-office-filter="<?php echo esc_attr(
    $value,
); ?>"><?php echo esc_html($label); ?></button><?php endforeach; ?></div>
        <div id="office-results" class="office-grid" role="tabpanel" aria-labelledby="office-tab-all">
        <?php foreach (estatein_office_content() as $i => $unused):

            $office = [];
            foreach (
                [
                    "label",
                    "address",
                    "description",
                    "email",
                    "phone",
                    "city",
                    "region",
                ]
                as $key
            ) {
                $office[$key] = estatein_page_value("office_{$i}_{$key}");
            }
            ?>
            <article class="office-card" data-office-region="<?php echo esc_attr(
                $office["region"],
            ); ?>"><p><?php echo esc_html(
    $office["label"],
); ?></p><h3><?php echo esc_html(
    $office["address"],
); ?></h3><p class="office-description"><?php echo esc_html(
    $office["description"],
); ?></p><ul class="office-tags"><li><?php
estatein_asset("6bb38.svg", "", "tiny-icon");
echo esc_html($office["email"]);
?></li><li><span aria-hidden="true">☎</span><?php echo esc_html(
    $office["phone"],
); ?></li><li><?php
estatein_asset("filter-location.png", "", "tiny-icon");
echo esc_html($office["city"]);
?></li></ul><a class="button button-primary" href="<?php echo esc_url(
    "https://www.google.com/maps/search/?api=1&query=" .
        rawurlencode($office["address"]),
); ?>" target="_blank" rel="noopener noreferrer">Get Direction<span class="screen-reader-text"> (opens maps in a new tab)</span></a></article>
        <?php
        endforeach; ?>
        </div><p class="screen-reader-text" aria-live="polite" data-office-status></p>
    </section>
    <section class="office-gallery wrap content-section" aria-labelledby="gallery-heading">
        <?php for (
            $i = 1;
            $i <= 6;
            $i++
        ): ?><div class="gallery-photo gallery-photo-<?php echo $i; ?>"><?php
$id = absint(estatein_page_value("gallery_image_" . $i));
if ($id) {
    echo wp_get_attachment_image($id, "large", false, [
        "alt" => "Estatein team and workspace",
        "loading" => "lazy",
    ]);
} else {
    estatein_asset(
        "contact-gallery-" . $i . ".webp",
        "",
        "",
        "Estatein team and workspace",
    );
}
?></div><?php endfor; ?>
        <div class="gallery-intro"><?php estatein_sparkles(); ?><h2 id="gallery-heading"><?php echo esc_html(
    estatein_page_value("gallery_title"),
); ?></h2><p><?php echo esc_html(
    estatein_page_value("gallery_description"),
); ?></p></div>
    </section>
</main>
<?php get_footer(); ?>
