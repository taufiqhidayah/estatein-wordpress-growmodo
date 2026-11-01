# WordPress hosting handoff

The owner will purchase hosting separately. No hosting account, domain, paid service, or public demo has been created. Do not submit a localhost link as the assessment's live demo.

## Provision

Choose a provider supporting custom themes, PHP, MySQL/MariaDB, HTTPS, database backups, and file uploads. Use a current stable WordPress release. Keep the hosting panel and WordPress administrator credentials separate, use unique passwords, and enable provider-supported MFA.

Select the newest maintained PHP branch supported by WordPress and ACF, with all vendor security updates applied. The tested local runtime is PHP 8.3.36. As of 29 October 2026, known critical-fix baselines are PHP 8.3.36, 8.4.27, and 8.5.12 within their respective branches; use later patched releases when available. A provider may backport fixes, but must confirm that explicitly.

## Deploy

1. Install WordPress using the provider installer or the official WordPress distribution and generate fresh WordPress salts. Use a non-default admin username.
2. Upload and activate `estatein-theme.zip`; optionally install ACF Free from WordPress.org.
3. Import demo content using Appearance -> Estatein setup. Select Home as the static front page if needed, and save Settings -> Permalinks -> Post name for property routes. Do not copy the local SQLite database or local credentials.
4. Set the real site URL to HTTPS, confirm both WordPress Address and Site Address, and configure the contact email on Home. Review sample content and the demonstration privacy/terms dialogs before broader use.
5. Apply the compatible server configuration in `security.md`. Verify backups and test restoration using the provider's tools.
6. Keep page cache lifetime shorter than the WordPress nonce validity window (use at most 8 hours), or exclude Home, Properties, Contact, and every property-detail page from full-page caching. Never cache admin-post or admin-ajax POST responses.

## Logged-out acceptance checks

- Open the public HTTPS URL in a fresh browser session. No login wall, redirect loop, mixed content, missing fonts, or broken images.
- Check 390px mobile, tablet, and desktop layouts. Exercise the menu, card navigation, property pages and FAQ/client dialogs, Escape key, focus return, and all footer links.
- Submit a test email and confirm exactly one record in Subscribers. Repeat the same address: same public success message and no additional record. Remove the test record afterward.
- Submit a Contact and a property enquiry, confirm their details/preferences in Enquiries, and remove the test records. Check catalog filters, detail routes, office tabs, and optional galleries.
- Check invalid input, expired form handling, and throttling. Confirm logged-out users cannot access subscriber records or import content.
- Test Chrome, Firefox, Safari, and Edge on the actual hosting environment; record real results. Obtain a Lighthouse measurement there rather than claiming a local score as production performance.
- Confirm database and filesystem backup, PHP version/patch status, security headers, denied PHP execution in uploads, and no public debug log or configuration backups.

Submission should contain the actual public URL, repository URL, theme ZIP, and the two-page development report. Public deployment remains an outstanding requirement until these checks pass.
