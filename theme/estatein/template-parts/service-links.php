<?php defined("ABSPATH") || exit(); ?>
<div <?php echo !empty($args["id"])
    ? 'id="' . esc_attr($args["id"]) . '"'
    : ""; ?> class="services page-service-links" aria-label="Our services">
    <?php
    $icons = [
        ["8578e.svg", "65a0b.svg", "properties", ""],
        ["105aa.svg", "d0c3d.svg", "services", "selling"],
        ["dada2.svg", "575c7.svg", "services", "management"],
        ["8dfea.svg", "7691c.svg", "services", "investment"],
    ];
    foreach ($icons as $i => $icon): ?>
    <a class="service-card" href="<?php echo esc_url(
        estatein_destination($icon[2], $icon[3]),
    ); ?>"><span class="service-icon"><span><?php estatein_asset(
    $icon[0],
    $icon[1],
); ?></span></span><span><?php echo esc_html(
    estatein_setting("service_" . ($i + 1)),
); ?></span><?php estatein_asset(
    "900d3.svg",
    "e9405.svg",
    "service-arrow",
); ?></a>
    <?php endforeach;
    ?>
</div>
