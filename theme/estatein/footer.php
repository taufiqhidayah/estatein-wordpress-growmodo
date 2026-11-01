<?php defined("ABSPATH") || exit(); ?>
<footer class="site-footer">
    <section id="contact" class="cta" aria-labelledby="cta-title">
        <?php
        estatein_asset("8b0ea.svg", "e3fb8.svg", "cta-art cta-art-right");
        estatein_asset("6dbad.svg", "a364f.svg", "cta-art cta-art-left");
        ?>
        <div class="cta-inner wrap"><div><h2 id="cta-title"><?php echo esc_html(
            estatein_setting("cta_title"),
        ); ?></h2><p><?php echo esc_html(
    estatein_setting("cta_description"),
); ?></p><?php if (
    is_email(estatein_setting("contact_email"))
): ?><a class="contact-email" href="<?php echo esc_url(
    "mailto:" . sanitize_email(estatein_setting("contact_email")),
); ?>"><?php echo esc_html(
    estatein_setting("contact_email"),
); ?></a><?php endif; ?></div><a class="button button-primary" href="<?php echo esc_url(
    estatein_destination("properties"),
); ?>">Explore Properties</a></div>
    </section>
    <div class="footer-main wrap">
        <div class="footer-brand">
            <?php estatein_logo(); ?>
            <form id="newsletter" class="newsletter" method="post" action="<?php echo esc_url(
                admin_url("admin-post.php"),
            ); ?>" data-ajax-url="<?php echo esc_url(
    admin_url("admin-ajax.php"),
); ?>">
                <label class="screen-reader-text" for="newsletter-email">Email address for property updates</label>
                <div class="newsletter-input"><?php estatein_asset(
                    "6bb38.svg",
                    "b5221.svg",
                    "email-icon",
                ); ?><input id="newsletter-email" name="email" type="email" maxlength="254" required autocomplete="email" placeholder="Enter Your Email" aria-describedby="newsletter-description newsletter-message"><button type="submit" aria-label="Subscribe to property updates"><?php estatein_asset(
    "93a35.svg",
    "f4bc2.svg",
); ?></button></div>
                <input type="hidden" name="action" value="estatein_subscribe">
                <?php wp_nonce_field(
                    "estatein_newsletter",
                    "estatein_nonce",
                    false,
                ); ?>
                <div class="honeypot" aria-hidden="true"><label for="company-website">Leave this field empty</label><input id="company-website" name="company_website" type="text" tabindex="-1" autocomplete="off"></div>
                <p id="newsletter-description" class="newsletter-description">Subscribe to property updates. <button type="button" class="text-link" data-open="privacy-dialog">Privacy</button></p>
                <?php
                $status =
                    isset($_GET["subscription"]) &&
                    is_string($_GET["subscription"])
                        ? sanitize_key($_GET["subscription"])
                        : "";
                $messages = estatein_newsletter_messages();
                ?>
                <p id="newsletter-message" class="form-message" role="status" aria-live="polite"><?php echo esc_html(
                    $messages[$status] ?? "",
                ); ?></p>
            </form>
        </div>
        <nav class="footer-links" aria-label="Footer navigation">
            <?php
            $columns = [
                "Home" => [
                    "Hero Section" => "home",
                    "Features" => "services",
                    "Properties" => "properties",
                    "Testimonials" => "testimonials",
                    "FAQ’s" => "faq",
                ],
                "About Us" => [
                    "Our Story" => "about",
                    "Our Works" => "properties",
                    "How It Works" => "faq",
                    "Our Team" => "contact",
                    "Our Clients" => "testimonials",
                ],
                "Properties" => [
                    "Portfolio" => "properties",
                    "Categories" => "properties",
                ],
                "Services" => [
                    "Valuation Mastery" => "services",
                    "Strategic Marketing" => "services",
                    "Negotiation Wizardry" => "services",
                    "Closing Success" => "services",
                    "Property Management" => "services",
                ],
                "Contact Us" => [
                    "Contact Form" => "newsletter",
                    "Our Offices" => "contact",
                ],
            ];
            foreach ($columns as $title => $links): ?>
                <div class="footer-column"><h3><?php echo esc_html(
                    $title,
                ); ?></h3><ul><?php foreach (
    $links
    as $label => $anchor
): ?><li><a href="<?php echo esc_url(
    estatein_footer_url($title, $label, $anchor),
); ?>"><?php echo esc_html($label); ?></a></li><?php endforeach; ?></ul></div>
            <?php endforeach;
            ?>
        </nav>
    </div>
    <div class="footer-bottom"><div class="wrap footer-bottom-inner"><div class="footer-legal"><p>©<?php echo esc_html(
        wp_date("Y"),
    ); ?> Estatein. All Rights Reserved.</p><button type="button" class="plain-button" data-open="terms-dialog">Terms &amp; Conditions</button></div><ul class="social-links" aria-label="Social platforms">
        <?php
        $socials = [
            ["Facebook", "https://www.facebook.com/", "d3853.svg", "1099a.svg"],
            ["LinkedIn", "https://www.linkedin.com/", "40c27.svg", "e6eee.svg"],
            ["X", "https://x.com/", "ae39f.svg", "1149d.svg"],
            ["YouTube", "https://www.youtube.com/", "c052f.svg", "89a6a.svg"],
        ];
        foreach ($socials as $social): ?><li><a href="<?php echo esc_url(
    $social[1],
); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(
    "Open " . $social[0] . " (new tab)",
); ?>"><?php estatein_asset(
    $social[2],
    $social[3],
); ?></a></li><?php endforeach;
        ?>
    </ul></div></div>
</footer>
<dialog id="privacy-dialog" class="detail-dialog text-dialog" aria-labelledby="privacy-title"><button class="dialog-close" type="button" data-close aria-label="Close privacy information">×</button><h2 id="privacy-title">Your privacy</h2><p>When you subscribe, this website stores your email address and subscription date to maintain the property updates list. Contact enquiries also store your name, contact details, message, preferences, and consent. These records are visible only to site administrators. It is not sent to an external marketing service.</p><p>To request removal, contact the website administrator<?php if (
    is_email(estatein_setting("contact_email"))
) {
    echo " at " . esc_html(estatein_setting("contact_email"));
} ?>. Please use a test address while reviewing this demonstration.</p></dialog>
<dialog id="terms-dialog" class="detail-dialog text-dialog" aria-labelledby="terms-title"><button class="dialog-close" type="button" data-close aria-label="Close terms">×</button><h2 id="terms-title">About this demonstration</h2><p>This website is a WordPress development assessment based on the Estatein design by Produce UI. Properties, prices, statistics, and testimonials are sample content, not active offers or verified business claims. Team profiles and office details are demonstration content as well.</p><p>No purchase, booking, or payment is processed on this website. The subscription form stores email addresses locally; it does not send automated email.</p></dialog>
<?php wp_footer(); ?>
</body>
</html>
