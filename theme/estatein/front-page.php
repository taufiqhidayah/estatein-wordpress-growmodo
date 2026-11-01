<?php
defined("ABSPATH") || exit();
get_header();
$properties = estatein_records("property");
$testimonials = estatein_records("testimonial");
$faqs = estatein_records("faq");
?>
<main id="main">
    <section id="home" class="hero" aria-labelledby="hero-title">
        <div class="hero-grid">
            <div class="hero-content">
                <h1 id="hero-title"><?php echo esc_html(
                    estatein_setting("hero_title"),
                ); ?></h1>
                <p class="hero-description"><?php echo esc_html(
                    estatein_setting("hero_description"),
                ); ?></p>
                <a href="#properties" class="dream-seal" aria-label="Discover your dream property">
                    <span class="seal-letters" aria-hidden="true"><?php
                    // Preserve the design's editable circular lettering as real text.
                    $letters = preg_split(
                        "//u",
                        "✨Discover Your Dream Property ",
                        -1,
                        PREG_SPLIT_NO_EMPTY,
                    );
                    foreach ($letters as $i => $letter) {
                        printf(
                            '<span style="--letter:%d">%s</span>',
                            (int) $i,
                            esc_html($letter),
                        );
                    }
                    ?></span><span class="seal-center"><?php estatein_asset(
    "4ac61.svg",
    "c5315.svg",
); ?></span>
                </a>
                <div class="hero-actions"><a class="button" href="<?php echo esc_url(
                    estatein_destination("about"),
                ); ?>">Learn More</a><a class="button button-primary" href="<?php echo esc_url(
    estatein_destination("properties"),
); ?>">Browse Properties</a></div>
                <dl id="about" class="stats"><?php for (
                    $i = 1;
                    $i <= 3;
                    $i++
                ): ?><div><dt><?php echo esc_html(
    estatein_setting("stat_" . $i . "_label"),
); ?></dt><dd><?php echo esc_html(
    estatein_setting("stat_" . $i . "_value"),
); ?></dd></div><?php endfor; ?></dl>
            </div>
            <div class="hero-art">
                <?php estatein_asset("0c02a.svg", "dfad8.svg", "hero-lines"); ?>
                <?php
                $hero_id = absint(estatein_setting("hero_image"));
                if ($hero_id && wp_attachment_is_image($hero_id)) {
                    echo wp_get_attachment_image($hero_id, "full", false, [
                        "class" => "hero-building",
                        "loading" => "eager",
                        "fetchpriority" => "high",
                        "sizes" =>
                            "(max-width: 767px) calc(100vw - 32px), 50vw",
                    ]);
                } else {
                    estatein_asset(
                        "99b02.png",
                        "",
                        "hero-building",
                        "Blue glass towers reaching into the sky",
                        true,
                    );
                }
                ?>
            </div>
        </div>
        <?php get_template_part("template-parts/service-links", null, [
            "id" => "services",
        ]); ?>
    </section>
    <?php if (is_page() && have_posts()):
        while (have_posts()):
            the_post();
            if (trim(get_the_content())): ?>
        <div class="editor-content wrap"><?php the_content(); ?></div>
    <?php endif;
        endwhile;
    endif; ?>
    <?php
    $sections = [
        "properties" => [
            "items" => $properties,
            "template" => "property",
            "label" => "View All Properties",
            "singular" => "properties",
        ],
        "testimonials" => [
            "items" => $testimonials,
            "template" => "testimonial",
            "label" => "View All Testimonials",
            "singular" => "testimonials",
        ],
        "faq" => [
            "items" => $faqs,
            "template" => "faq",
            "label" => "View All FAQ’s",
            "singular" => "questions",
        ],
    ];
    foreach ($sections as $section_id => $section):
        if (!$section["items"]) {
            continue;
        } ?>
    <section id="<?php echo esc_attr(
        $section_id,
    ); ?>" class="content-section wrap" aria-labelledby="<?php echo esc_attr(
    $section_id,
); ?>-title" data-carousel>
        <div class="section-heading">
            <div class="section-intro"><?php estatein_sparkles(); ?><h2 id="<?php echo esc_attr(
    $section_id,
); ?>-title"><?php echo esc_html(
    estatein_setting($section_id . "_title"),
); ?></h2><p><?php
echo esc_html(estatein_setting($section_id . "_description"));
if (
    "properties" === $section_id
): ?><span class="desktop-description"> Click “View Details” for more information.</span><?php endif;
?></p></div>
            <?php if (
                "properties" === $section_id
            ): ?><a class="button button-secondary desktop-view-all" href="<?php echo esc_url(
    estatein_destination("properties"),
); ?>"><?php echo esc_html(
    $section["label"],
); ?></a><?php else: ?><button class="button button-secondary desktop-view-all" type="button" data-collection="<?php echo esc_attr(
    $section_id,
); ?>"><?php echo esc_html($section["label"]); ?></button><?php endif; ?>
        </div>
        <div class="carousel-track" tabindex="0" role="group" aria-label="<?php echo esc_attr(
            estatein_setting($section_id . "_title"),
        ); ?>" data-track>
            <?php foreach ($section["items"] as $index => $item) {
                get_template_part(
                    "template-parts/" . $section["template"] . "-card",
                    null,
                    [
                        "item" => $item,
                        "dialog_id" => $section_id . "-detail-" . $index,
                    ],
                );
            } ?>
        </div>
        <?php estatein_carousel_controls(
            $section["singular"],
            $section_id,
            $section["label"],
        ); ?>
    </section>
    <?php
    endforeach;
    ?>
</main>
<?php foreach ($sections as $section_id => $section):
    if ("testimonials" === $section_id) {
        continue;
    }
    foreach ($section["items"] as $index => $item): ?>
    <dialog id="<?php echo esc_attr(
        $section_id . "-detail-" . $index,
    ); ?>" class="detail-dialog" aria-labelledby="<?php echo esc_attr(
    $section_id . "-heading-" . $index,
); ?>">
        <button class="dialog-close" type="button" data-close aria-label="Close details">×</button>
        <?php if (
            "properties" === $section_id
        ): ?><div class="dialog-image"><?php estatein_item_image(
    $item,
); ?></div><?php endif; ?>
        <h2 id="<?php echo esc_attr(
            $section_id . "-heading-" . $index,
        ); ?>"><?php echo esc_html($item["title"]); ?></h2>
        <div class="rich-text"><?php echo wp_kses_post(
            wpautop(
                $item["answer"] ?? ($item["content"] ?? $item["description"]),
            ),
        ); ?></div>
        <?php if (
            "properties" === $section_id
        ): ?><p class="dialog-features"><?php echo esc_html(
    $item["beds"] .
        " bedrooms · " .
        $item["baths"] .
        " bathrooms · " .
        $item["type"],
); ?></p><p class="dialog-price"><?php echo esc_html(
    '$' . number_format_i18n((float) $item["price"]),
); ?></p><a href="#contact" class="button button-primary" data-close>Enquire About This Property</a><?php endif; ?>
    </dialog>
<?php endforeach;
endforeach; ?>
<dialog id="collection-dialog" class="collection-dialog" aria-labelledby="collection-title"><button class="dialog-close" type="button" data-close aria-label="Close collection">×</button><h2 id="collection-title" tabindex="-1"></h2><div class="collection-grid" data-collection-content></div></dialog>
<?php get_footer(); ?>
