# Verification record

Reviewed 29 October through 1 November 2026. Work began around 21:30 WIB on 29 October, with pauses and a scope expansion from the homepage to six page types. Active time was not separately instrumented; no four-hour completion claim is made.

## Environment

- WordPress 7.1.4, PHP CLI/development server 8.3.36, SQLite Database Integration 3.1.2, ACF Free 6.8.10.
- Core/plugin distribution checksums were verified during initial setup. Local automatic update checks also ran on resuming development.
- SQLite is a local fixture only. Production MySQL/MariaDB, hosting rules, HTTPS, and hosted performance remain untested.
- Production assets use esbuild 0.25.12. Build-time Node dependencies are not required by the deployed theme.

## Executed checks

- Theme PHP files pass `php -l`, JavaScript sources pass `node --check`, and minified CSS/JS build successfully. ZIP entries are integrity-checked.
- `node tests/http.mjs`: homepage HTTP 200, expected headers, no PHP version header, one H1, escaped CMS heading, 39 homepage assets, newsletter validation, nonce/honeypot, duplicate handling, real persistence, rate limiting, no-JS redirect, and administrator protection. Test rows/counters are removed.
- `node tests/full-site-http.mjs`: six routes return 200 with one H1, unique IDs, no empty hrefs/PHP diagnostics, valid cross-page anchor targets, 71 local assets, seven catalog scenarios, and service-to-contact enquiry preselection. Out-of-range pagination clamps to an available page; malformed arrays cannot broaden SQL.
- `node tests/inquiries-http.mjs`: nonce rejection, real storage, truthful success, no-JS 303 redirect to an owned destination, and protected enquiry dashboard. Test records/counters are removed.
- `wp eval-file tests/catalog.php`: 16 catalog, pagination, malformed-input, and permission assertions.
- `wp eval-file tests/inquiries.php`: 28 enquiry assertions covering validation, consent, property references, preference persistence, invalid preference shapes, rate limiting, private storage, and guest denial.
- `wp eval-file tests/content.php`: 11 CMS/import/native-save permission assertions.
- `wp eval-file tests/full-site-content.php`: 10 page identity/publication, CMS edit, preservation, nonce, and permission assertions. Changes are restored.
- ACF was disabled; both content suites and the six-route HTTP suite passed using native fallback fields, then ACF was reactivated.

## Browser checks actually performed

The desktop app's Chromium browser was used. Homepage menu, banner, carousels, FAQ/collection/privacy/terms dialogs, Escape/focus return, and newsletter saving were checked during the original homepage work. After expansion, mobile menu navigation to Properties, type filtering to Metropolitan Haven, detail-page navigation, office-region selection with arrow keys, and genuine Contact enquiry persistence were exercised. Client-story dialog Escape returns focus to its trigger. Optional property photo thumbnails were checked using a temporary second-photo fixture, then the original record was restored. No theme-related console errors were observed in the inspected session.

All six page types have equal viewport/document widths at 320, 768, and 1440 CSS pixels. Additional 390 and 1920 checks and screenshots cover the new layouts. Desktop Contact/Services section positions were compared with screenshot proportions; mobile screenshots and individual form/detail views were inspected. Existing homepage checks also covered 1200 pixels. These are targeted visual and interaction checks, not an accessibility certification or a full browser matrix.

## Honest design/content limits

Properties, Services, and Contact use supplied desktop screenshots. About Us and Property Details use the supplementary reference site and available original design geometry. Missing mobile design frames were adapted responsively. Property Details includes gallery, description, specs, amenities, enquiry, and related cards, but not the original Figma's extended pricing tables. Exact six-page pixel parity is not claimed.

Temporary gallery/team photos and small icons were cropped with explicit owner permission. About hero uses the supplementary reference asset. Team identities, clients, offices, and listings are demonstration content. Social links open the platforms. Real record counts replace decorative totals. No transactional purchasing, external CRM, or automatic email delivery is implemented.

## Outstanding deployment work

Public hosting is pending the owner's purchase. Before submission: deploy to patched PHP/MySQL hosting with HTTPS; verify all routes, database behavior, headers, upload execution denial, backups, form caching/nonce behavior, and logged-out public access. Run real Chrome, Firefox, Safari, and Edge checks plus hosted performance measurements. Safari inspection was previously attempted but did not complete; no Safari/Firefox/Edge pass is claimed. Replace sample business/privacy/contact information before accepting real users' data.
