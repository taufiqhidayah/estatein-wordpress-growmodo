<?php
defined('ABSPATH') || exit();
$item = $args['item'];
$dialog_id = $args['dialog_id'];
?>
<article class="card property-card" data-card>
    <div class="property-image"><?php estatein_item_image($item); ?></div>
    <div class="property-copy">
        <h3><?php echo esc_html($item['title']); ?></h3>
        <p><?php echo esc_html(
            rtrim($item['description'], '.'),
        ); ?>… <button class="text-link" data-open="<?php echo esc_attr(
    $dialog_id,
); ?>" type="button">Read More<span class="screen-reader-text"> about <?php echo esc_html(
    $item['title'],
); ?></span></button></p>
    </div>
    <ul class="property-features" aria-label="Property features">
        <li><span class="feature-icon"><?php estatein_asset(
            'e46bc.svg',
        ); ?></span><?php echo esc_html($item['beds']); ?>-Bedroom</li>
        <li><span class="feature-icon"><?php estatein_asset(
            '2f7fc.svg',
        ); ?></span><?php echo esc_html($item['baths']); ?>-Bathroom</li>
        <li><span class="feature-icon"><?php estatein_asset(
            'cf4f1.svg',
            '6311d.svg',
        ); ?></span><?php echo esc_html($item['type']); ?></li>
    </ul>
    <div class="property-bottom"><p class="price"><span>Price</span><strong><?php echo esc_html(
        '$' . number_format_i18n((float) $item['price']),
    ); ?></strong></p><button type="button" class="button button-primary" data-open="<?php echo esc_attr(
    $dialog_id,
); ?>">View Property Details</button></div>
</article>
