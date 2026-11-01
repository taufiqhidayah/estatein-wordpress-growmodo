<?php
/** Template Name: Estatein Services */
defined("ABSPATH") || exit();
get_header();
?>
<main id="main" class="inner-page services-page">
    <?php
    estatein_page_intro();
    get_template_part("template-parts/service-links");
    estatein_page_editor_content();
    ?>
    <?php foreach (["selling", "management"] as $group): ?>
    <section id="<?php echo esc_attr($group); ?>" class="content-section wrap">
        <?php estatein_section_heading(
            estatein_page_value($group . "_title"),
            estatein_page_value($group . "_description"),
        ); ?>
        <div class="service-detail-grid">
            <?php foreach (
                estatein_service_content()[$group]
                as $i => $item
            ): ?>
            <article class="service-detail-card" id="<?php echo esc_attr(
                $item[2],
            ); ?>"><div class="service-card-heading"><?php estatein_asset(
    "service-" . $item[2] . ".png",
    "",
    "service-static-icon",
); ?><h3><?php echo esc_html(
    estatein_page_value("{$group}_{$i}_title"),
); ?></h3></div><p><?php echo esc_html(
    estatein_page_value("{$group}_{$i}_description"),
); ?></p></article>
            <?php endforeach; ?>
            <article class="service-detail-card service-cta-card"><div><h3><?php echo esc_html(
                estatein_page_value($group . "_cta_title"),
            ); ?></h3><a class="button" href="<?php echo esc_url(
    add_query_arg(
        "inquiry_type",
        "selling" === $group ? "sell" : "general",
        estatein_destination("contact", "contact-form"),
    ),
); ?>">Learn More</a></div><p><?php echo esc_html(
    estatein_page_value($group . "_cta_description"),
); ?></p></article>
        </div>
    </section>
    <?php endforeach; ?>
    <section id="investment" class="content-section investment-section wrap"><div class="investment-intro"><?php estatein_section_heading(
        estatein_page_value("investment_title"),
        estatein_page_value("investment_description"),
    ); ?><article class="service-detail-card service-cta-card"><h3><?php echo esc_html(
    estatein_page_value("investment_cta_title"),
); ?></h3><p><?php echo esc_html(
    estatein_page_value("investment_cta_description"),
); ?></p><a class="button" href="<?php echo esc_url(
    add_query_arg(
        "inquiry_type",
        "invest",
        estatein_destination("contact", "contact-form"),
    ),
); ?>">Learn More</a></article></div><div class="investment-grid">
        <?php foreach (
            estatein_service_content()["investment"]
            as $i => $item
        ): ?><article class="service-detail-card"><div class="service-card-heading"><?php estatein_asset(
    "service-" . $item[2] . ".png",
    "",
    "service-static-icon",
); ?><h3><?php echo esc_html(
    estatein_page_value("investment_{$i}_title"),
); ?></h3></div><p><?php echo esc_html(
    estatein_page_value("investment_{$i}_description"),
); ?></p></article><?php endforeach; ?>
    </div></section>
</main>
<?php get_footer(); ?>
