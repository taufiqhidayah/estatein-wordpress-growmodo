<?php
/** Content defaults and queries. The importer makes these editable records. */
defined('ABSPATH') || exit();

function estatein_defaults()
{
    return [
        'banner_text' => '✨Discover Your Dream Property with Estatein',
        'hero_title' => 'Discover Your Dream Property with Estatein',
        'hero_description' =>
            'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.',
        'stat_1_value' => '200+',
        'stat_1_label' => 'Happy Customers',
        'stat_2_value' => '10k+',
        'stat_2_label' => 'Properties For Clients',
        'stat_3_value' => '16+',
        'stat_3_label' => 'Years of Experience',
        'service_1' => 'Find Your Dream Home',
        'service_2' => 'Unlock Property Value',
        'service_3' => 'Effortless Property Management',
        'service_4' => 'Smart Investments, Informed Decisions',
        'properties_title' => 'Featured Properties',
        'properties_description' =>
            'Explore our handpicked selection of featured properties. Each listing offers a glimpse into exceptional homes and investments available through Estatein.',
        'testimonials_title' => 'What Our Clients Say',
        'testimonials_description' =>
            'Read the success stories and heartfelt testimonials from our valued clients. Discover why they chose Estatein for their real estate needs.',
        'faq_title' => 'Frequently Asked Questions',
        'faq_description' =>
            "Find answers to common questions about Estatein's services, property listings, and the real estate process. We're here to provide clarity and assist you every step of the way.",
        'cta_title' => 'Start Your Real Estate Journey Today',
        'cta_description' =>
            "Your dream property is just a click away. Whether you're looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.",
        'contact_email' => '',
    ];
}

function estatein_setting($key)
{
    $id = (int) get_option('page_on_front');
    $value = $id ? get_post_meta($id, 'estatein_' . $key, true) : '';
    return '' !== (string) $value ? $value : estatein_defaults()[$key] ?? '';
}

function estatein_demo_properties()
{
    return [
        [
            'title' => 'Seaside Serenity Villa',
            'description' =>
                'A stunning 4-bedroom, 3-bathroom villa in a peaceful suburban neighborhood.',
            'image' => '5a611.png',
            'beds' => 4,
            'baths' => 3,
            'type' => 'Villa',
            'price' => 550000,
        ],
        [
            'title' => 'Metropolitan Haven',
            'description' =>
                'A chic and fully-furnished 2-bedroom apartment with panoramic city views.',
            'image' => '48e14.png',
            'beds' => 2,
            'baths' => 2,
            'type' => 'Villa',
            'price' => 550000,
        ],
        [
            'title' => 'Rustic Retreat Cottage',
            'description' =>
                'An elegant 3-bedroom, 2.5-bathroom townhouse in a gated community.',
            'image' => 'b881e.png',
            'beds' => 3,
            'baths' => 3,
            'type' => 'Villa',
            'price' => 550000,
        ],
    ];
}

function estatein_demo_testimonials()
{
    return [
        [
            'title' => 'Exceptional Service!',
            'description' =>
                "Our experience with Estatein was outstanding. Their team's dedication and professionalism made finding our dream home a breeze. Highly recommended!",
            'name' => 'Wade Warren',
            'location' => 'USA, California',
            'image' => '89736.png',
        ],
        [
            'title' => 'Efficient and Reliable',
            'description' =>
                "Estatein provided us with top-notch service. They helped us sell our property quickly and at a great price. We couldn't be happier with the results.",
            'name' => 'Emelie Thomson',
            'location' => 'USA, Florida',
            'image' => 'a8d8d.png',
        ],
        [
            'title' => 'Trusted Advisors',
            'description' =>
                'The Estatein team guided us through the entire buying process. Their knowledge and commitment to our needs were impressive. Thank you for your support!',
            'name' => 'John Mans',
            'location' => 'USA, Nevada',
            'image' => 'df3ae.png',
        ],
    ];
}

function estatein_demo_faqs()
{
    return [
        [
            'title' => 'How do I search for properties on Estatein?',
            'description' =>
                'Learn how to use our user-friendly search tools to find properties that match your criteria.',
            'answer' =>
                'Browse the Featured Properties section and select View Property Details to explore a home. Use the arrows to see the available listings, or select View All Properties to see them together. Contact our team to discuss your preferences and arrange a viewing.',
        ],
        [
            'title' =>
                'What documents do I need to sell my property through Estatein?',
            'description' =>
                'Find out about the necessary documentation for listing your property with us.',
            'answer' =>
                'Start by preparing proof of ownership, identification, and any available property plans or inspection records. Requirements vary by location and property. Contact an Estatein agent to confirm the documents needed for your listing.',
        ],
        [
            'title' => 'How can I contact an Estatein agent?',
            'description' =>
                'Discover the different ways you can get in touch with our experienced agents.',
            'answer' =>
                'Select Contact Us to find the contact options configured for this website. You can also subscribe to receive property updates using the email form in the footer.',
        ],
    ];
}

add_action('init', function () {
    foreach (
        [
            'property' => ['Properties', 'Property', 'dashicons-building'],
            'testimonial' => [
                'Testimonials',
                'Testimonial',
                'dashicons-format-quote',
            ],
            'faq' => ['FAQs', 'FAQ', 'dashicons-editor-help'],
        ]
        as $type => $labels
    ) {
        register_post_type('estatein_' . $type, [
            'labels' => [
                'name' => $labels[0],
                'singular_name' => $labels[1],
                'add_new_item' => 'Add ' . $labels[1],
                'edit_item' => 'Edit ' . $labels[1],
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_in_menu' => true,
            'menu_icon' => $labels[2],
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'page-attributes',
            ],
            'rewrite' => false,
            'has_archive' => false,
        ]);
    }
});

function estatein_records($type)
{
    $posts = get_posts([
        'post_type' => 'estatein_' . $type,
        'post_status' => 'publish',
        'numberposts' => 30,
        'orderby' => ['menu_order' => 'ASC', 'ID' => 'ASC'],
    ]);
    if (!$posts) {
        // Demo fallback only before an explicit import. An editor can intentionally empty a collection.
        if (get_option('estatein_demo_imported')) {
            return [];
        }
        $functions = [
            'property' => 'estatein_demo_properties',
            'testimonial' => 'estatein_demo_testimonials',
            'faq' => 'estatein_demo_faqs',
        ];
        return $functions[$type]();
    }
    $items = [];
    foreach ($posts as $post) {
        $item = [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'description' =>
                $post->post_excerpt ?: wp_strip_all_tags($post->post_content),
            'content' => $post->post_content,
            'image_id' => get_post_thumbnail_id($post),
            'image' => '',
        ];
        if ('property' === $type) {
            foreach (['beds', 'baths', 'type', 'price'] as $key) {
                $item[$key] = get_post_meta(
                    $post->ID,
                    'estatein_' . $key,
                    true,
                );
            }
        } elseif ('testimonial' === $type) {
            foreach (['name', 'location'] as $key) {
                $item[$key] = get_post_meta(
                    $post->ID,
                    'estatein_' . $key,
                    true,
                );
            }
        } else {
            $item['answer'] = $post->post_content;
        }
        $items[] = $item;
    }
    return $items;
}

function estatein_item_image($item, $class = '')
{
    if (!empty($item['image_id'])) {
        echo wp_get_attachment_image($item['image_id'], 'full', false, [
            'class' => $class,
            'alt' => $item['name'] ?? $item['title'],
            'loading' => 'lazy',
            'sizes' =>
                'client-avatar' === $class
                    ? '60px'
                    : '(max-width: 767px) calc(100vw - 80px), (max-width: 1399px) 45vw, 28vw',
        ]);
    } elseif (!empty($item['image'])) {
        estatein_asset($item['image'], '', $class, $item['title']);
    }
}
