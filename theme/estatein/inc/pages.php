<?php
/** Page content transcribed from the supplied desktop screenshots. */
defined("ABSPATH") || exit();

function estatein_page_defaults()
{
    $defaults = [
        "about" => [
            "intro_title" => "Our Journey",
            "intro_description" =>
                "A small team with a shared ambition grew into Estatein: a place to discover homes, explore investments, and get practical guidance. Our journey continues through long-term relationships, local knowledge, and a commitment to making every property decision clearer.",
            "values_title" => "Our Values",
            "values_description" =>
                "Clear advice, thoughtful service, and lasting relationships guide the way we work. These principles shape every conversation and every property journey.",
            "achievements_title" => "Our Achievements",
            "achievements_description" =>
                "Every successful move represents a shared milestone. We measure our progress through the confidence of the people we help and the relationships we build along the way.",
            "process_title" => "Navigating the Estatein Experience",
            "process_description" =>
                "From the first search to the final decision, our process keeps your goals in view. Here is how we help you move forward with clarity.",
            "team_title" => "Meet Our Team",
            "team_description" =>
                "Meet the people who bring property knowledge, fresh perspectives, and attentive service to every conversation.",
            "clients_title" => "Our Valued Clients",
            "clients_description" =>
                "Our clients bring different ambitions, from finding a first home to planning a business expansion. These sample stories illustrate the relationships at the heart of Estatein.",
            "journey_image" => 0,
        ],
        "properties" => [
            "intro_title" => "Find Your Dream Property",
            "intro_description" =>
                "Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life. With categories to suit every dreamer, your journey",
            "catalog_title" => "Discover a World of Possibilities",
            "catalog_description" =>
                "Our portfolio of properties is as diverse as your dreams. Explore the following categories to find the perfect property that resonates with your vision of home",
            "form_title" => "Let's Make it Happen",
            "form_description" =>
                "Ready to take the first step toward your dream property? Fill out the form below, and our real estate wizards will work their magic to find your perfect match. Don't wait; let's embark on this exciting journey together.",
        ],
        "services" => [
            "intro_title" => "Elevate Your Real Estate Experience",
            "intro_description" =>
                "Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.",
            "selling_title" => "Unlock Property Value",
            "selling_description" =>
                "Selling your property should be a rewarding experience, and at Estatein, we make sure it is. Our Property Selling Service is designed to maximize the value of your property, ensuring you get the best deal possible. Explore the categories below to see how we can help you at every step of your selling journey",
            "selling_cta_title" => "Unlock the Value of Your Property Today",
            "selling_cta_description" =>
                "Ready to unlock the true value of your property? Explore our Property Selling Service categories and let us help you achieve the best deal possible for your valuable asset.",
            "management_title" => "Effortless Property Management",
            "management_description" =>
                "Owning a property should be a pleasure, not a hassle. Estatein's Property Management Service takes the stress out of property ownership, offering comprehensive solutions tailored to your needs. Explore the categories below to see how we can make property management effortless for you",
            "management_cta_title" =>
                "Experience Effortless Property Management",
            "management_cta_description" =>
                "Ready to experience hassle-free property management? Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.",
            "investment_title" => "Smart Investments, Informed Decisions",
            "investment_description" =>
                "Building a real estate portfolio requires a strategic approach. Estatein's Investment Advisory Service empowers you to make smart investments and informed decisions.",
            "investment_cta_title" => "Unlock Your Investment Potential",
            "investment_cta_description" =>
                "Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.",
        ],
        "contact" => [
            "intro_title" => "Get in Touch with Estatein",
            "intro_description" =>
                "Welcome to Estatein's Contact Us page. We're here to assist you with any inquiries, requests, or feedback you may have. Whether you're looking to buy or sell a property, explore investment opportunities, or simply want to connect, we're just a message away. Reach out to us, and let's start a conversation.",
            "form_title" => "Let's Connect",
            "form_description" =>
                "We're excited to connect with you and learn more about your real estate goals. Use the form below to get in touch with Estatein. Whether you're a prospective client, partner, or simply curious about our services, we're here to answer your questions and provide the assistance you need.",
            "offices_title" => "Discover Our Office Locations",
            "offices_description" =>
                "Estatein is here to serve you across multiple locations. Whether you're looking to meet our team, discuss real estate opportunities, or simply drop by for a chat, we have offices conveniently located to assist you. Explore the categories below to find the Estatein office nearest to you",
            "gallery_title" => "Explore Estatein's World",
            "gallery_description" =>
                "Step inside the world of Estatein, where professionalism meets warmth, and expertise meets passion. Our gallery offers a glimpse into our team and workspaces, inviting you to get to know us better.",
            "email" => "info@estatein.com",
            "phone" => "+1 (123) 456-7890",
        ],
    ];
    foreach (estatein_service_content() as $group => $items) {
        foreach ($items as $i => $item) {
            $defaults["services"]["{$group}_{$i}_title"] = $item[0];
            $defaults["services"]["{$group}_{$i}_description"] = $item[1];
        }
    }
    foreach (estatein_office_content() as $i => $office) {
        foreach (
            [
                "label",
                "address",
                "description",
                "email",
                "phone",
                "city",
                "region",
            ]
            as $j => $field
        ) {
            $defaults["contact"]["office_{$i}_{$field}"] = $office[$j];
        }
    }
    for ($i = 1; $i <= 6; $i++) {
        $defaults["contact"]["gallery_image_" . $i] = 0;
    }
    foreach (estatein_about_sections() as $group => $items) {
        foreach ($items as $i => $item) {
            $defaults["about"]["{$group}_{$i}_title"] = $item[0];
            $defaults["about"]["{$group}_{$i}_description"] = $item[1];
        }
    }
    return $defaults;
}

function estatein_about_sections()
{
    return [
        "values" => [
            [
                "Trust",
                "We earn trust through clear communication and practical, transparent guidance.",
            ],
            [
                "Excellence",
                "We pay attention to the details that turn a property search into a positive experience.",
            ],
            [
                "Client-Centric",
                "Your needs shape our approach, from the first conversation to the next step.",
            ],
            [
                "Our Commitment",
                "We stay focused on thoughtful service, reliable support, and lasting relationships.",
            ],
        ],
        "achievements" => [
            [
                "3+ Years of Excellence",
                "Every project adds to our understanding of properties, people, and the choices that matter.",
            ],
            [
                "Happy Clients",
                "Helping people move forward is the most meaningful measure of our progress.",
            ],
            [
                "Industry Recognition",
                "We continue to raise our standards through collaboration, learning, and careful attention to our work.",
            ],
        ],
        "process" => [
            [
                "Discover a World of Possibilities",
                "Start with our property collection. Compare locations, property types, and budgets to explore the options that fit your goals.",
            ],
            [
                "Narrowing Down Your Choices",
                "Choose the properties that interest you, then compare their details and decide what matters most.",
            ],
            [
                "Personalized Guidance",
                "Send us your questions and preferences. We can help you identify the information you need to take the next step.",
            ],
            [
                "See It for Yourself",
                "Discuss a viewing with our team and take a closer look at the spaces you are considering.",
            ],
            [
                "Making Informed Decisions",
                "Review the practical details, ask questions, and consider the trade-offs before you commit to a decision.",
            ],
            [
                "Getting the Best Deal",
                "Work through the next steps with a clear understanding of your priorities and the options available.",
            ],
        ],
    ];
}

function estatein_service_content()
{
    return [
        "selling" => [
            [
                "Valuation Mastery",
                "Discover the true worth of your property with our expert valuation services.",
                "valuation",
            ],
            [
                "Strategic Marketing",
                "Selling a property requires more than just a listing; it demands a strategic marketing approach.",
                "marketing",
            ],
            [
                "Negotiation Wizardry",
                "Negotiating the best deal is an art, and our negotiation experts are masters of it.",
                "negotiation",
            ],
            [
                "Closing Success",
                "A successful sale is not complete until the closing. We guide you through the intricate closing process.",
                "closing",
            ],
        ],
        "management" => [
            [
                "Tenant Harmony",
                "Our Tenant Management services ensure that your tenants have a smooth and reducing vacancies.",
                "tenant",
            ],
            [
                "Maintenance Ease",
                "Say goodbye to property maintenance headaches. We handle all aspects of property upkeep.",
                "maintenance",
            ],
            [
                "Financial Peace of Mind",
                "Managing property finances can be complex. Our financial experts take care of rent collection",
                "finance",
            ],
            [
                "Legal Guardian",
                "Stay compliant with property laws and regulations effortlessly.",
                "legal",
            ],
        ],
        "investment" => [
            [
                "Market Insight",
                "Stay ahead of market trends with our expert Market Analysis. We provide in-depth insights into real estate market conditions",
                "market",
            ],
            [
                "ROI Assessment",
                "Make investment decisions with confidence. Our ROI Assessment services evaluate the potential returns on your investments",
                "roi",
            ],
            [
                "Customized Strategies",
                "Every investor is unique, and so are their goals. We develop Customized Investment Strategies tailored to your specific needs",
                "strategies",
            ],
            [
                "Diversification Mastery",
                "Diversify your real estate portfolio effectively. Our experts guide you in spreading your investments across various property types and locations",
                "diversification",
            ],
        ],
    ];
}

function estatein_office_content()
{
    return [
        [
            "Main Headquarters",
            "123 Estatein Plaza, City Center, Metropolis",
            "Our main headquarters serve as the heart of Estatein. Located in the bustling city center, this is where our core team of experts operates, driving the excellence and innovation that define us.",
            "info@estatein.com",
            "+1 (123) 456-7890",
            "Metropolis",
            "international",
        ],
        [
            "Regional Offices",
            "456 Urban Avenue, Downtown District, Metropolis",
            "Estatein's presence extends to multiple regions, each with its own dynamic real estate landscape. Discover our regional offices, staffed by local experts who understand the nuances of their respective markets.",
            "info@estatein.com",
            "+1 (123) 628-7890",
            "Metropolis",
            "regional",
        ],
    ];
}

function estatein_demo_team()
{
    return [
        [
            "title" => "Sarah Johnson",
            "role" => "Senior Real Estate Agent",
            "description" =>
                "Sarah helps buyers compare homes and plan their next move with confidence.",
            "image" => "team-1.webp",
        ],
        [
            "title" => "Michael Chen",
            "role" => "Property Investment Specialist",
            "description" =>
                "Michael connects investment goals with practical property research and clear advice.",
            "image" => "team-2.webp",
        ],
        [
            "title" => "Emily Rodriguez",
            "role" => "Commercial Real Estate Expert",
            "description" =>
                "Emily supports businesses looking for the space and location to grow.",
            "image" => "team-3.webp",
        ],
        [
            "title" => "David Thompson",
            "role" => "Property Management Director",
            "description" =>
                "David focuses on thoughtful maintenance and a positive experience for owners and tenants.",
            "image" => "team-4.webp",
        ],
    ];
}

function estatein_demo_clients()
{
    return [
        [
            "title" => "ABC Corporation",
            "description" =>
                "The team helped us turn a long list of possibilities into a clear plan for our next office. We valued the practical guidance at every stage.",
            "domain" => "Commercial Real Estate",
            "category" => "Office Development",
            "since" => "2019",
            "answer" =>
                "A growing company needed room for its next chapter. In this sample project, Estatein helped compare locations, review requirements, and organize the steps toward a suitable workspace. Clear communication kept the search focused on the client’s priorities.",
        ],
        [
            "title" => "GreenTech Enterprises",
            "description" =>
                "We found a partner who listened to our priorities and brought a structured approach to a complex property search.",
            "domain" => "Commercial Real Estate",
            "category" => "Retail Space",
            "since" => "2018",
            "answer" =>
                "This sample client story follows a retail property search, from defining customer access and space requirements to comparing available locations. Estatein’s role was to organize the options and help the client evaluate the trade-offs.",
        ],
    ];
}

function estatein_property_record($post)
{
    $item = [
        "id" => $post->ID,
        "title" => get_the_title($post),
        "description" =>
            $post->post_excerpt ?: wp_strip_all_tags($post->post_content),
        "content" => $post->post_content,
        "image_id" => get_post_thumbnail_id($post),
        "image" => "",
    ];
    foreach (
        [
            "beds",
            "baths",
            "type",
            "price",
            "location",
            "area",
            "build_year",
            "parking",
            "tagline",
            "catalog_excerpt",
            "amenities",
        ]
        as $key
    ) {
        $item[$key] = get_post_meta($post->ID, "estatein_" . $key, true);
    }
    return $item;
}

function estatein_import_full_site()
{
    $home = estatein_import_demo();
    if (is_wp_error($home)) {
        return $home;
    }
    $pages = estatein_import_site_pages();
    if (is_wp_error($pages)) {
        return $pages;
    }
    foreach ($pages as $key => $id) {
        foreach (estatein_page_defaults()[$key] ?? [] as $field => $value) {
            if (metadata_exists("post", $id, "estatein_page_" . $field)) {
                continue;
            }
            if (is_int($value)) {
                $filename =
                    "journey_image" === $field
                        ? "about-journey.png"
                        : "contact-gallery-" . substr($field, -1) . ".webp";
                $value = estatein_import_image(
                    $filename,
                    "Estatein " . str_replace("_", " ", $field),
                );
            }
            add_post_meta($id, "estatein_page_" . $field, $value, true);
        }
    }
    $extra = [
        [
            "price" => 1250000,
            "type" => "Villa",
            "location" => "Coastal Heights",
            "area" => 2800,
            "build_year" => 2019,
            "parking" => "2-Car Garage",
            "tagline" => "Coastal Escapes - Where Waves Beckon",
            "catalog_excerpt" =>
                "Wake up to the soothing melody of waves. This beachfront villa offers a peaceful setting for your next chapter.",
            "amenities" =>
                "Ocean view\nPrivate garden\nGourmet kitchen\nMaster balcony\nOpen-plan living",
        ],
        [
            "price" => 650000,
            "type" => "Apartment",
            "location" => "City Center",
            "area" => 1600,
            "build_year" => 2021,
            "parking" => "1-Car Garage",
            "tagline" => "Urban Oasis - Life in the Heart of the City",
            "catalog_excerpt" =>
                "Immerse yourself in the energy of the city. This modern apartment in the heart of town brings everyday essentials within reach.",
            "amenities" =>
                "City views\nFurnished interior\nModern kitchen\nSecure entry\nNearby amenities",
        ],
        [
            "price" => 350000,
            "type" => "Cottage",
            "location" => "Countryside",
            "area" => 1800,
            "build_year" => 2018,
            "parking" => "Private parking",
            "tagline" => "Countryside Charm - Escape to Nature’s Embrace",
            "catalog_excerpt" =>
                "Find tranquility in the countryside. This charming cottage is nestled amidst rolling hills and open skies.",
            "amenities" =>
                "Quiet setting\nOutdoor space\nSpacious living area\nNatural light\nGated community",
        ],
    ];
    foreach ($extra as $i => $values) {
        $posts = get_posts([
            "post_type" => "estatein_property",
            "post_status" => "any",
            "meta_key" => "_estatein_demo_key",
            "meta_value" => "property_" . $i,
            "numberposts" => 1,
        ]);
        if (!$posts) {
            continue;
        }
        $id = $posts[0]->ID;
        foreach ($values as $key => $value) {
            if (!metadata_exists("post", $id, "estatein_" . $key)) {
                add_post_meta($id, "estatein_" . $key, $value, true);
            }
        }
        // Upgrade untouched sample placeholders once; preserve any different editor values.
        if (!get_post_meta($id, "_estatein_catalog_migrated", true)) {
            if (550000 === (int) get_post_meta($id, "estatein_price", true)) {
                update_post_meta($id, "estatein_price", $values["price"]);
            }
            if ("Villa" === get_post_meta($id, "estatein_type", true)) {
                update_post_meta($id, "estatein_type", $values["type"]);
            }
            update_post_meta($id, "_estatein_catalog_migrated", 1);
        }
    }
    estatein_inquiries_install();
    update_option("estatein_full_site_imported", 1, false);
    flush_rewrite_rules(false);
    return $pages;
}

function estatein_current_page_key($post_id = 0)
{
    return get_post_meta(
        $post_id ?: get_queried_object_id(),
        "_estatein_page_key",
        true,
    );
}

function estatein_page_value($key, $page_key = "", $post_id = 0)
{
    $page_key = $page_key ?: estatein_current_page_key();
    $post_id =
        $post_id ?:
        (int) (get_option("estatein_site_page_ids", [])[$page_key] ?? 0);
    if (
        $post_id &&
        metadata_exists("post", $post_id, "estatein_page_" . $key)
    ) {
        return get_post_meta($post_id, "estatein_page_" . $key, true);
    }
    return estatein_page_defaults()[$page_key][$key] ?? "";
}

/** Link to finished pages; retain working homepage links for pages not supplied yet. */
function estatein_destination($key, $anchor = "")
{
    $id = (int) (get_option("estatein_site_page_ids", [])[$key] ?? 0);
    if ($id && "publish" === get_post_status($id)) {
        return get_permalink($id) . ($anchor ? "#" . $anchor : "");
    }
    $fallback = [
        "about" => "about",
        "properties" => "properties",
        "services" => "services",
        "contact" => "contact",
    ];
    return home_url("/#" . ($fallback[$key] ?? "home"));
}

function estatein_footer_url($column, $label, $anchor)
{
    if ("Home" === $column) {
        return home_url("/#" . $anchor);
    }
    if ("About Us" === $column) {
        return estatein_destination(
            "about",
            [
                "Our Story" => "journey",
                "Our Works" => "achievements",
                "How It Works" => "process",
                "Our Team" => "team",
                "Our Clients" => "clients",
            ][$label] ?? "journey",
        );
    }
    if ("Properties" === $column) {
        return estatein_destination("properties", "catalog");
    }
    if ("Services" === $column) {
        return estatein_destination(
            "services",
            [
                "Valuation Mastery" => "valuation",
                "Strategic Marketing" => "marketing",
                "Negotiation Wizardry" => "negotiation",
                "Closing Success" => "closing",
                "Property Management" => "management",
            ][$label] ?? "selling",
        );
    }
    return estatein_destination(
        "contact",
        "Contact Form" === $label ? "contact-form" : "offices",
    );
}

function estatein_section_heading($title, $description, $class = "")
{
    echo '<div class="section-heading ' .
        esc_attr($class) .
        '"><div class="section-intro">';
    estatein_sparkles();
    echo "<h2>" .
        esc_html($title) .
        "</h2><p>" .
        esc_html($description) .
        "</p></div></div>";
}

function estatein_page_intro()
{
    echo '<section class="page-intro"><div class="wrap"><h1>' .
        esc_html(estatein_page_value("intro_title")) .
        "</h1><p>" .
        esc_html(estatein_page_value("intro_description")) .
        "</p></div></section>";
}

function estatein_page_editor_content()
{
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            if (trim(get_the_content())) {
                echo '<div class="wrap editor-content">';
                the_content();
                echo "</div>";
            }
        }
    }
}

/** Page configuration remains editable through ACF Free or native fields. */
add_action("acf/init", function () {
    foreach (estatein_page_defaults() as $page => $defaults) {
        $fields = [];
        foreach ($defaults as $key => $value) {
            $field = [
                "key" => "field_estatein_page_" . $page . "_" . $key,
                "name" => "estatein_page_" . $key,
                "label" => ucwords(str_replace("_", " ", $key)),
                "type" => is_int($value)
                    ? "image"
                    : (str_contains($key, "description")
                        ? "textarea"
                        : (str_ends_with($key, "email")
                            ? "email"
                            : "text")),
                "default_value" => $value,
            ];
            if (is_int($value)) {
                $field["return_format"] = "id";
                $field["mime_types"] = "jpg,jpeg,png,webp,avif";
            }
            $fields[] = $field;
        }
        $slug = estatein_site_pages()[$page]["slug"];
        acf_add_local_field_group([
            "key" => "group_estatein_page_" . $page,
            "title" => "Estatein " . ucwords($page) . " content",
            "fields" => $fields,
            "location" => [
                [
                    [
                        "param" => "page_template",
                        "operator" => "==",
                        "value" => "page-" . $slug . ".php",
                    ],
                ],
            ],
        ]);
    }
});

add_action("add_meta_boxes", function () {
    if (function_exists("acf_add_local_field_group")) {
        return;
    }
    add_meta_box(
        "estatein-page-content",
        "Estatein page content",
        function ($post) {
            $page = estatein_current_page_key($post->ID);
            $defaults = estatein_page_defaults()[$page] ?? [];
            if (!$defaults) {
                echo "<p>This panel applies to imported Estatein inner pages.</p>";
                return;
            }
            wp_nonce_field("estatein_save_page_content", "estatein_page_nonce");
            foreach ($defaults as $key => $default) {
                $name = "estatein_page_" . $key;
                echo '<p><label for="' .
                    esc_attr($name) .
                    '"><strong>' .
                    esc_html(ucwords(str_replace("_", " ", $key))) .
                    '</strong></label><textarea class="widefat" id="' .
                    esc_attr($name) .
                    '" name="' .
                    esc_attr($name) .
                    '" rows="3">' .
                    esc_textarea(estatein_page_value($key, $page, $post->ID)) .
                    "</textarea></p>";
            }
        },
        "page",
    );
});

add_action("save_post_page", function ($id) {
    $nonce =
        isset($_POST["estatein_page_nonce"]) &&
        is_string($_POST["estatein_page_nonce"])
            ? sanitize_text_field(wp_unslash($_POST["estatein_page_nonce"]))
            : "";
    if (
        !wp_verify_nonce($nonce, "estatein_save_page_content") ||
        !current_user_can("edit_post", $id) ||
        wp_is_post_revision($id) ||
        (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
    ) {
        return;
    }
    $defaults = estatein_page_defaults()[estatein_current_page_key($id)] ?? [];
    foreach ($defaults as $key => $default) {
        $name = "estatein_page_" . $key;
        if (isset($_POST[$name]) && is_string($_POST[$name])) {
            update_post_meta(
                $id,
                $name,
                sanitize_textarea_field(wp_unslash($_POST[$name])),
            );
        }
    }
});
