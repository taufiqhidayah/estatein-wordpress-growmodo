<?php defined('ABSPATH') || exit();
$item = $args['item'];
?>
<article class="card testimonial-card" data-card>
    <div class="rating" aria-label="5 out of 5 stars"><?php for (
        $i = 0;
        $i < 5;
        $i++
    ) {
        estatein_asset('9812e.svg', '76135.svg');
    } ?></div>
    <div class="testimonial-copy"><h3><?php echo esc_html(
        $item['title'],
    ); ?></h3><p><?php echo esc_html($item['description']); ?></p></div>
    <div class="client"><?php estatein_item_image(
        $item,
        'client-avatar',
    ); ?><p><span><?php echo esc_html(
    $item['name'],
); ?></span><span><?php echo esc_html($item['location']); ?></span></p></div>
</article>
