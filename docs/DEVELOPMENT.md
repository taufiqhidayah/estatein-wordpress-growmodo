# Estatein - Development report

## Project and scope

Estatein is a custom WordPress theme for the Growmodo assessment, using PHP, HTML, CSS, and vanilla JavaScript without a page builder. The initial four-hour homepage scope was expanded by the owner to six page types: Home, About Us, Properties, Property Details, Services, and Contact. All six are implemented locally. The homepage includes the hero, services, featured properties, testimonials, FAQs, CTA, and footer. Inner pages add company information, a searchable catalog, individual property URLs, services, offices, and enquiry forms.

Implementation began on 29 October 2026 around 21:30 WIB and continued with clarification pauses through 1 November. Active time was not separately measured; this expanded delivery is not represented as a four-hour implementation. Public WordPress deployment remains pending the owner's hosting purchase.

## Design translation and decisions

The supplied Produce UI Figma desktop/mobile homepage context guided the visual system. After Figma MCP reached its account limit, owner-provided desktop screenshots guided Properties, Services, and Contact. The owner also supplied estatein-seven.vercel.app as an additional visual reference for About Us and Property Details. Intermediate and new mobile layouts are responsive adaptations, not independently verified against missing Figma mobile frames. Exact pixel parity across all six original Figma pages is not claimed.

Urbanist fonts and design assets are served locally. With explicit permission, contact/gallery photos and small icons were cropped from supplied screenshots; these are temporary assessment assets. Team portraits use sample identities. Property Details follows the supplementary reference structure with property photos, information, amenities, enquiries, and related listings; it does not reproduce the original Figma's extended pricing tables. Carousel totals reflect actual records and the copyright uses the current year.

## WordPress content architecture

Reusable PHP partials render navigation, cards, services, and forms. ACF Free field definitions live in code; native fields work without ACF. Page fields control section headings, descriptions, services, offices, and gallery images. The WordPress editor supports additional page content. Properties, testimonials, FAQs, team members, and client stories have dashboard records. Property fields include price, bedrooms, bathrooms, type, location, area, build year, parking, amenities, and three optional additional photos.

An explicit, repeatable importer creates the pages and sample records without replacing an existing front-page choice or overwriting content edits. Published property records have real detail URLs. Catalog filters and pagination use WordPress queries. Production assets are prebuilt, so hosting needs neither Node.js nor a frontend build service.

## Interaction and security

Mobile navigation, dismissible announcements, card controls, keyboard-friendly native dialogs, office-region tabs, and optional photo thumbnails are implemented. Contact and property forms save enquiries privately in WordPress; footer subscriptions save separate records. No email delivery or external CRM is configured. Feedback reports actual save failures, validation failures, expiration, and throttling rather than promising delivery.

Form handlers require POST, a nonce, a honeypot check, bounded inputs, validation, and per-IP throttling. Administrative views/deletions require capabilities and deletion nonces. SQL values are prepared, output is escaped, and IPs are represented only by short-lived salted rate keys. Credentials, database files, and logs stay outside the repository. Local PHP 8.3.36 includes the October advisory fixes; current vendor patches and server hardening must still be verified on hosting. These measures are not a penetration-test certification.

## Verification and practical limits

Local integration checks cover six routes, one H1 and unique IDs per page, internal anchor targets, 71 assets, catalog filtering and pagination, CMS changes, importer preservation, enquiry preferences, persistence, consent, malformed inputs, nonce checks, throttling, and protected dashboards. ACF fallback was checked with the plugin disabled and restored afterward. PHP lint, JavaScript syntax, production build, and ZIP integrity are checked. Browser UI tests use the desktop app's Chromium browser; Firefox, Safari, and Edge compatibility is not certified.

Responsive document-width checks found no horizontal overflow on all six page types at 320, 768, and 1440 CSS pixels. Desktop and mobile screenshots were reviewed; menu navigation, catalog-to-detail navigation, office keyboard tabs, and a genuine saved enquiry were exercised. The local fixture uses WordPress with SQLite; production MySQL/MariaDB, HTTPS, hosting rules, and hosted performance remain untested.

## Delivery and deployment

The handoff contains an installable theme ZIP, the GitHub source repository, this two-page report, verification notes, and deployment/security instructions. Install on patched PHP plus MySQL/MariaDB hosting, activate the theme, optionally install ACF Free, run Appearance > Estatein setup, select Home if needed, and enable pretty permalinks. Review sample contact, office, team, and privacy content before real-world use.

After hosting is available, configure HTTPS, server limits, upload-directory execution denial, backups, and suitable page-cache exclusions for every form page. Repeat logged-out form, route, responsive, browser, and database checks on the public URL before submission. Localhost is a development preview and does not satisfy the assessment's public-demo requirement. AI assisted drafting and design inspection; the resulting code was adapted and tested in WordPress.
