<?php defined('ABSPATH') || exit();
$item = $args['item'];
?>
<article class="card faq-card" data-card>
    <h3><?php echo esc_html($item['title']); ?></h3>
    <p><?php echo esc_html($item['description']); ?></p>
    <button class="button button-secondary" type="button" data-open="<?php echo esc_attr(
        $args['dialog_id'],
    ); ?>">Read More<span class="screen-reader-text">: <?php echo esc_html(
    $item['title'],
); ?></span></button>
</article>
