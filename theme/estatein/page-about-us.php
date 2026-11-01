<?php
/** Template Name: Estatein About Us */
defined("ABSPATH") || exit();
get_header();
$team = estatein_records("team");
$clients = estatein_records("client");
?>
<main id="main" class="inner-page about-page">
    <section id="journey" class="about-journey wrap"><div class="journey-copy"><div class="section-intro"><?php estatein_sparkles(); ?><h1><?php echo esc_html(
    estatein_page_value("intro_title"),
); ?></h1><p><?php echo esc_html(
    estatein_page_value("intro_description"),
); ?></p></div><dl class="stats"><?php for (
    $i = 1;
    $i <= 3;
    $i++
): ?><div><dt><?php echo esc_html(
    estatein_setting("stat_" . $i . "_label"),
); ?></dt><dd><?php echo esc_html(
    estatein_setting("stat_" . $i . "_value"),
); ?></dd></div><?php endfor; ?></dl></div><div class="journey-image"><?php
$id = absint(estatein_page_value("journey_image"));
if ($id) {
    echo wp_get_attachment_image($id, "full", false, [
        "loading" => "eager",
        "fetchpriority" => "high",
        "alt" => "A model home held in a hand",
    ]);
} else {
    estatein_asset(
        "about-journey.png",
        "",
        "",
        "A model home held in a hand",
        true,
    );
}
?></div></section>
    <?php estatein_page_editor_content(); ?>
    <section id="values" class="content-section values-section wrap"><?php estatein_section_heading(
        estatein_page_value("values_title"),
        estatein_page_value("values_description"),
    ); ?><div class="values-panel"><?php foreach (
    estatein_about_sections()["values"]
    as $i => $unused
): ?><article><div><span class="value-symbol" aria-hidden="true"><?php echo [
    "★",
    "◆",
    "♧",
    "★",
][$i]; ?></span><h3><?php echo esc_html(
    estatein_page_value("values_{$i}_title"),
); ?></h3></div><p><?php echo esc_html(
    estatein_page_value("values_{$i}_description"),
); ?></p></article><?php endforeach; ?></div></section>
    <section id="achievements" class="content-section wrap"><?php estatein_section_heading(
        estatein_page_value("achievements_title"),
        estatein_page_value("achievements_description"),
    ); ?><div class="achievement-grid"><?php foreach (
    estatein_about_sections()["achievements"]
    as $i => $unused
): ?><article class="card achievement-card"><h3><?php echo esc_html(
    estatein_page_value("achievements_{$i}_title"),
); ?></h3><p><?php echo esc_html(
    estatein_page_value("achievements_{$i}_description"),
); ?></p></article><?php endforeach; ?></div></section>
    <section id="process" class="content-section wrap"><?php estatein_section_heading(
        estatein_page_value("process_title"),
        estatein_page_value("process_description"),
    ); ?><ol class="process-grid"><?php foreach (
    estatein_about_sections()["process"]
    as $i => $unused
): ?><li><span class="process-number">Step <?php echo esc_html(
    str_pad((string) ($i + 1), 2, "0", STR_PAD_LEFT),
); ?></span><div><h3><?php echo esc_html(
    estatein_page_value("process_{$i}_title"),
); ?></h3><p><?php echo esc_html(
    estatein_page_value("process_{$i}_description"),
); ?></p></div></li><?php endforeach; ?></ol></section>
    <section id="team" class="content-section wrap"><?php estatein_section_heading(
        estatein_page_value("team_title"),
        estatein_page_value("team_description"),
    ); ?><div class="team-grid"><?php foreach (
    $team
    as $member
): ?><article class="card team-card"><?php estatein_item_image(
    $member,
    "team-photo",
); ?><h3><?php echo esc_html($member["title"]); ?></h3><p><?php echo esc_html(
    $member["role"],
); ?></p><a class="button team-contact" href="<?php echo esc_url(
    estatein_destination("contact", "contact-form"),
); ?>">Say Hello<span aria-hidden="true">↗</span><span class="screen-reader-text"> to <?php echo esc_html(
    $member["title"],
); ?></span></a></article><?php endforeach; ?></div></section>
    <?php if (
        $clients
    ): ?><section id="clients" class="content-section wrap" data-carousel><?php estatein_section_heading(
    estatein_page_value("clients_title"),
    estatein_page_value("clients_description"),
); ?><div class="carousel-track client-stories" data-track tabindex="0" role="group" aria-label="Client stories"><?php foreach (
    $clients
    as $i => $client
): ?><article class="card client-story" data-card><p class="client-since">Since <?php echo esc_html(
    $client["since"],
); ?></p><div class="client-story-title"><h3><?php echo esc_html(
    $client["title"],
); ?></h3><button class="button button-secondary" type="button" data-open="client-story-<?php echo $i; ?>">Read Client Story</button></div><dl class="client-domains"><div><dt>Domain</dt><dd><?php echo esc_html(
    $client["domain"],
); ?></dd></div><div><dt>Category</dt><dd><?php echo esc_html(
    $client["category"],
); ?></dd></div></dl><blockquote><p class="quote-label">What They Said</p><p><?php echo esc_html(
    $client["description"],
); ?></p></blockquote></article><?php endforeach; ?></div><?php estatein_carousel_controls(
    "client stories",
    "clients",
    "View All Clients",
); ?></section><?php endif; ?>
</main>
<?php foreach (
    $clients
    as $i => $client
): ?><dialog id="client-story-<?php echo $i; ?>" class="detail-dialog text-dialog" aria-labelledby="client-story-title-<?php echo $i; ?>"><button class="dialog-close" type="button" data-close aria-label="Close client story">×</button><h2 id="client-story-title-<?php echo $i; ?>"><?php echo esc_html(
    $client["title"],
); ?></h2><div class="rich-text"><?php echo wp_kses_post(
    wpautop($client["content"] ?? $client["description"]),
); ?></div></dialog><?php endforeach; ?>
<dialog id="collection-dialog" class="collection-dialog" aria-labelledby="collection-title"><button class="dialog-close" type="button" data-close aria-label="Close collection">×</button><h2 id="collection-title" tabindex="-1"></h2><div class="collection-grid" data-collection-content></div></dialog>
<?php get_footer(); ?>
