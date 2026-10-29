# Verification record

Reviewed on 29 October 2026. Implementation began around 21:30 WIB, with pauses for scope/hosting clarification; active work time was not separately instrumented. The agreed budget is four hours. No claim is made that this work used exactly four hours.

## Environment

- WordPress 7.1.4, PHP CLI/development server 8.3.36, local WordPress SQLite Database Integration 3.1.2, ACF Free 6.8.10.
- Core files and installed plugin files verified against official distribution checksums.
- Theme is independent of SQLite. **Production MySQL/MariaDB and hosting behavior have not yet been tested.**
- Production assets built using esbuild 0.25.12. npm installation reported zero known vulnerabilities at the time checked; this is not a general security guarantee.

## Executed checks

- All 13 theme PHP files pass `php -l`; JavaScript passes `node --check`; minified production CSS/JS build successfully.
- `node tests/http.mjs`: HTTP 200 homepage, expected security headers, no PHP version header, exactly one H1, no temporary Figma URLs, and 39 referenced desktop assets return HTTP 200 with appropriate non-HTML content.
- Newsletter HTTP checks: missing/invalid nonce returns 403; malformed/XSS-like addresses return 400; honeypot is rejected; valid submission persists; repeat submission has the same public response with one database record; excess attempts return 429; non-JavaScript submission redirects with HTTP 303; logged-out admin access redirects to login. Test records and rate counters are cleaned up.
- `wp eval-file tests/content.php`: 11 assertions for guest import denial, repeat-import idempotency, preservation of front-page selection and CMS edits, property field updates, and nonce/capability requirements for native saves. Modified content is restored.
- ACF was temporarily deactivated on the local fixture; the content/permission tests (including an authorized native-field save) and frontend HTTP 200 check passed, then ACF was reactivated.
- Browser UI checks in the desktop app's Chromium browser: mobile menu open/close, property dialog, FAQ dialog, all three collection dialogs, Escape and focus restoration, privacy/terms dialogs, successful newsletter save, and card navigation from 01 to 02 with appropriate button states.
- Responsive checks at 320, 390, 768, 1200, 1440, and 1920 CSS pixels. Horizontal document width measured equal to viewport width at 320, 390, 768, 1440, and 1920. Desktop 1920 and mobile 390 full-page screenshots were inspected. No theme-related browser console errors were observed during these checks.
- Readable stylesheet/script sources and minified production outputs are both included. No runtime dependency on Figma/Google Fonts URLs; image content uses local Media Library files after import.

## Scope decisions visible to a reviewer

- Homepage only, rather than unfinished versions of all six Figma pages. Property and FAQ details use accessible native dialogs.
- Carousel totals reflect the actual three seeded records, rather than the design's decorative 60/10 totals. Desktop arrows disable when all records already fit. Collection buttons expose all actual records.
- Copyright uses the current year. Privacy and demonstration terms explain sample content and subscriber storage. No purchases or automated email delivery are implemented.
- Mobile property order follows the shared CMS source order. Tablet layout is adapted between the provided desktop and mobile references. Additional laptop Figma context requests hit the account's MCP limit after desktop/mobile detail was obtained.
- Generic social platform links are present for visual fidelity; no real Estatein profile or office address was invented.

## Still outstanding

- Public hosting and HTTPS deployment; owner will purchase hosting. Local preview is not acceptable as the final live-demo deliverable.
- Production PHP patch verification, MySQL/MariaDB behavior, proxy/security-header rules, upload-directory execution denial, backups, and hosted Lighthouse measurements.
- Firefox, Safari, and Edge testing. A native Safari inspection attempt could not be completed through the available UI control; no Safari compatibility pass is claimed. These checks are not inherently dependent on hosting, but remain unperformed.
- Configure real contact information and business/privacy text before real-world use. The subscription list has no connected mail provider or verified opt-in workflow, so it should use test data during assessment review.

The included checks are targeted functional/security tests, not a penetration test or an accessibility certification.
