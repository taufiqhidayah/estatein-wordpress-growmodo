<?php
defined("ABSPATH") || exit();
$context = $args["context"] ?? "contact";
$property_id = absint($args["property_id"] ?? 0);
$field = function (
    $name,
    $label,
    $type = "text",
    $placeholder = "",
    $required = false,
    $autocomplete = "",
) {
    echo '<div class="form-field"><label for="inquiry-' .
        esc_attr($name) .
        '">' .
        esc_html($label) .
        ($required
            ? '<span class="screen-reader-text"> (required)</span>'
            : "") .
        '</label><input id="inquiry-' .
        esc_attr($name) .
        '" name="' .
        esc_attr($name) .
        '" type="' .
        esc_attr($type) .
        '" placeholder="' .
        esc_attr($placeholder) .
        '" maxlength="' .
        ("email" === $type ? "254" : ("tel" === $type ? "40" : "120")) .
        '"' .
        ($required ? " required" : "") .
        ($autocomplete
            ? ' autocomplete="' . esc_attr($autocomplete) . '"'
            : "") .
        "></div>";
};
$select = function ($name, $label, $placeholder, $options, $class = "") {
    echo '<div class="form-field ' .
        esc_attr($class) .
        '"><label for="inquiry-' .
        esc_attr($name) .
        '">' .
        esc_html($label) .
        '</label><select id="inquiry-' .
        esc_attr($name) .
        '" name="' .
        esc_attr($name) .
        '"><option value="">' .
        esc_html($placeholder) .
        "</option>";
    $chosen =
        "inquiry_type" === $name
            ? estatein_request_text($_GET, "inquiry_type", 40)
            : "";
    foreach ($options as $value => $text) {
        echo '<option value="' .
            esc_attr($value) .
            '"' .
            selected($chosen, (string) $value, false) .
            ">" .
            esc_html($text) .
            "</option>";
    }
    echo "</select></div>";
};
?>
<form id="contact-form" class="inquiry-form inquiry-form--<?php echo esc_attr(
    $context,
); ?>" method="post" action="<?php echo esc_url(
    admin_url("admin-post.php"),
); ?>" data-inquiry-form data-ajax-url="<?php echo esc_url(
    admin_url("admin-ajax.php"),
); ?>">
    <input type="hidden" name="estatein_nonce" value="<?php echo esc_attr(
        wp_create_nonce("estatein_inquiry"),
    ); ?>">
    <input type="hidden" name="action" value="estatein_inquiry">
    <?php if (
        $property_id
    ): ?><input type="hidden" name="property_id" value="<?php echo (int) $property_id; ?>"><?php endif; ?>
    <div class="inquiry-grid">
        <?php
        $field(
            "first_name",
            "First Name",
            "text",
            "Enter First Name",
            true,
            "given-name",
        );
        $field(
            "last_name",
            "Last Name",
            "text",
            "Enter Last Name",
            true,
            "family-name",
        );
        $field("email", "Email", "email", "Enter your Email", true, "email");
        $field("phone", "Phone", "tel", "Enter Phone Number", false, "tel");
        if ("property-search" === $context) {

            $locations =
                array_combine(
                    estatein_property_filter_options("location"),
                    estatein_property_filter_options("location"),
                ) ?:
                [];
            $types =
                array_combine(
                    estatein_property_filter_options("type"),
                    estatein_property_filter_options("type"),
                ) ?:
                [];
            $select(
                "preferred_location",
                "Preferred Location",
                "Select Location",
                $locations,
            );
            $select(
                "property_type",
                "Property Type",
                "Select Property Type",
                $types,
            );
            $select(
                "bathrooms",
                "No. of Bathrooms",
                "Select No. of Bathrooms",
                ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5+"],
            );
            $select("bedrooms", "No. of Bedrooms", "Select No. of Bedrooms", [
                "1" => "1",
                "2" => "2",
                "3" => "3",
                "4" => "4",
                "5" => "5+",
            ]);
            $select(
                "budget",
                "Budget",
                "Select Budget",
                [
                    "under-500k" => 'Under $500,000',
                    "500k-1m" => '$500,000 – $1 million',
                    "1m-2m" => '$1 million – $2 million',
                    "over-2m" => 'Over $2 million',
                ],
                "span-two",
            );
            ?>
            <fieldset class="preferred-contact span-two"><legend>Preferred Contact Method</legend><div class="contact-methods"><div><span aria-hidden="true">☎</span><input type="tel" name="contact_phone" maxlength="40" placeholder="Enter Your Number" aria-label="Preferred contact phone number"><input type="radio" name="preferred_contact" value="phone" checked aria-label="Prefer phone"></div><div><span aria-hidden="true">✉</span><input type="email" name="contact_email" maxlength="254" placeholder="Enter Your Email" aria-label="Preferred contact email address"><input type="radio" name="preferred_contact" value="email" aria-label="Prefer email"></div></div></fieldset>
            <?php
        } elseif ("contact" === $context) {
            $select(
                "inquiry_type",
                "Inquiry Type",
                "Select Inquiry Type",
                estatein_inquiry_types(),
            );
            $select("referral", "How Did You Hear About Us?", "Select", [
                "search" => "Search engine",
                "social" => "Social media",
                "friend" => "Friend or colleague",
                "other" => "Other",
            ]);
        }
        ?>
        <div class="form-field span-all"><label for="inquiry-message">Message<span class="screen-reader-text"> (required)</span></label><textarea id="inquiry-message" name="message" required minlength="10" maxlength="5000" placeholder="Enter your Message here.." rows="5"></textarea></div>
    </div>
    <div class="honeypot" aria-hidden="true"><label for="inquiry-company-website">Leave this field empty</label><input id="inquiry-company-website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>
    <div class="form-bottom"><label class="consent"><input name="consent" type="checkbox" value="1" required><span>I agree with <button class="text-link" type="button" data-open="terms-dialog">Terms of Use</button> and <button class="text-link" type="button" data-open="privacy-dialog">Privacy Policy</button></span></label><button class="button button-primary" type="submit">Send Your Message</button></div>
    <?php $result = estatein_request_text($_GET, "enquiry", 20); ?>
    <p class="inquiry-status form-message" role="status" aria-live="polite" tabindex="-1"><?php echo esc_html(
        estatein_inquiry_messages()[$result] ?? "",
    ); ?></p>
</form>
