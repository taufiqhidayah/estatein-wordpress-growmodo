<?php defined("ABSPATH") || exit();
$item = $args["item"];
$url = get_permalink($item["id"]);
?>
<article class="card catalog-card">
    <a class="property-image" href="<?php echo esc_url(
        $url,
    ); ?>" aria-label="<?php echo esc_attr(
    "View " . $item["title"],
); ?>"><?php estatein_item_image($item); ?></a>
    <div class="catalog-copy"><?php if (
        $item["tagline"]
    ): ?><p class="catalog-tagline"><?php echo esc_html(
    $item["tagline"],
); ?></p><?php endif; ?><h3><a href="<?php echo esc_url(
    $url,
); ?>"><?php echo esc_html(
    $item["title"],
); ?></a></h3><p class="catalog-summary"><?php echo esc_html(
    wp_trim_words($item["catalog_excerpt"] ?: $item["description"], 16, "…"),
); ?> <a class="text-link" href="<?php echo esc_url(
     $url,
 ); ?>">Read More<span class="screen-reader-text"> about <?php echo esc_html(
    $item["title"],
); ?></span></a></p></div>
    <div class="property-bottom"><p class="price"><span>Price</span><strong><?php echo esc_html(
        '$' . number_format_i18n((float) $item["price"]),
    ); ?></strong></p><a class="button button-primary" href="<?php echo esc_url(
    $url,
); ?>">View Property Details</a></div>
</article>
