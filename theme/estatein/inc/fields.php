<?php
/** ACF Free fields and a native editor fallback using the same meta keys. */
defined('ABSPATH') || exit();

function estatein_field_groups()
{
    $home = [];
    foreach (estatein_defaults() as $key => $default) {
        $home[$key] = [
            ucwords(str_replace('_', ' ', $key)),
            str_contains($key, 'description')
                ? 'textarea'
                : ('contact_email' === $key
                    ? 'email'
                    : 'text'),
            $default,
        ];
    }
    $home['hero_image'] = ['Hero image', 'image', ''];
    return [
        'page' => $home,
        'estatein_property' => [
            'price' => ['Price (USD)', 'number', 0],
            'beds' => ['Bedrooms', 'number', 0],
            'baths' => ['Bathrooms', 'number', 0],
            'type' => ['Property type', 'text', 'Villa'],
        ],
        'estatein_testimonial' => [
            'name' => ['Client name', 'text', ''],
            'location' => ['Client location', 'text', ''],
        ],
    ];
}

add_action('acf/init', function () {
    foreach (estatein_field_groups() as $post_type => $definitions) {
        $fields = [];
        foreach ($definitions as $key => $definition) {
            $field = [
                'key' => 'field_estatein_' . $post_type . '_' . $key,
                'name' => 'estatein_' . $key,
                'label' => $definition[0],
                'type' => $definition[1],
                'default_value' => $definition[2],
            ];
            if ('image' === $definition[1]) {
                $field['return_format'] = 'id';
                $field['mime_types'] = 'jpg,jpeg,png,webp,avif';
            }
            if ('number' === $definition[1]) {
                $field['min'] = 0;
                $field['step'] = 'baths' === $key ? 0.5 : 1;
            }
            $fields[] = $field;
        }
        acf_add_local_field_group([
            'key' => 'group_estatein_' . $post_type,
            'title' => 'Estatein content',
            'fields' => $fields,
            'location' => [
                [
                    [
                        'param' =>
                            'page' === $post_type ? 'page_type' : 'post_type',
                        'operator' => '==',
                        'value' =>
                            'page' === $post_type ? 'front_page' : $post_type,
                    ],
                ],
            ],
        ]);
    }
});

add_action('add_meta_boxes', function () {
    if (function_exists('acf_add_local_field_group')) {
        return;
    }
    foreach (estatein_field_groups() as $post_type => $fields) {
        add_meta_box(
            'estatein-content',
            'Estatein content',
            function ($post) use ($fields) {
                if (
                    'page' === $post->post_type &&
                    (int) get_option('page_on_front') !== $post->ID
                ) {
                    echo '<p>Estatein fields apply to the page selected as your homepage in Settings → Reading.</p>';
                    return;
                }
                wp_nonce_field('estatein_save_fields', 'estatein_fields_nonce');
                foreach ($fields as $key => $field) {
                    $name = 'estatein_' . $key;
                    $value = get_post_meta($post->ID, $name, true);
                    $value = '' !== $value ? $value : $field[2];
                    printf(
                        '<p><label for="%1$s"><strong>%2$s</strong></label><br>',
                        esc_attr($name),
                        esc_html($field[0]),
                    );
                    if ('textarea' === $field[1]) {
                        printf(
                            '<textarea class="widefat" rows="3" id="%1$s" name="%1$s">%2$s</textarea>',
                            esc_attr($name),
                            esc_textarea($value),
                        );
                    } else {
                        printf(
                            '<input class="widefat" id="%1$s" name="%1$s" type="%2$s" value="%3$s"%4$s>',
                            esc_attr($name),
                            esc_attr(
                                'image' === $field[1] ? 'number' : $field[1],
                            ),
                            esc_attr($value),
                            in_array($field[1], ['number', 'image'], true)
                                ? ('baths' === $key
                                    ? ' min="0" step="0.5"'
                                    : ' min="0" step="1"')
                                : '',
                        );
                        if ('image' === $field[1]) {
                            echo '<span class="description">Enter an attachment ID from the Media Library, or install ACF Free for an image picker.</span>';
                        }
                    }
                    echo '</p>';
                }
            },
            $post_type,
        );
    }
});

add_action('save_post', function ($post_id) {
    if (
        !isset($_POST['estatein_fields_nonce']) ||
        !is_string($_POST['estatein_fields_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['estatein_fields_nonce'])),
            'estatein_save_fields',
        ) ||
        !current_user_can('edit_post', $post_id) ||
        wp_is_post_revision($post_id) ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    ) {
        return;
    }
    $fields = estatein_field_groups()[get_post_type($post_id)] ?? [];
    foreach ($fields as $key => $field) {
        $name = 'estatein_' . $key;
        if (!isset($_POST[$name]) || !is_scalar($_POST[$name])) {
            continue;
        }
        $raw = wp_unslash((string) $_POST[$name]);
        $value = match ($field[1]) {
            'email' => sanitize_email($raw),
            'image' => absint($raw),
            'number' => max(0, (float) $raw),
            'textarea' => sanitize_textarea_field($raw),
            default => sanitize_text_field($raw),
        };
        update_post_meta($post_id, $name, $value);
    }
});
