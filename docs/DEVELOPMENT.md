# Estatein - Development report

## Project and scope

This project implements the Estatein homepage from the supplied Produce UI Figma template as a custom WordPress theme for the Growmodo developer assessment. The agreed timebox is four hours, with quality prioritized over unfinished pages. The delivered page includes the announcement, navigation, hero, service cards, featured properties, testimonials, FAQs, CTA, and footer. The other five page types are outside this iteration. Navigation links lead to implemented homepage sections; property and FAQ details open in accessible dialogs.

## Process and design translation

Desktop and mobile sections were inspected through Figma's design context. Images, SVG artwork, icons, and Urbanist typography were retained and served locally. The implementation uses responsive grid/flex layouts instead of a fixed screenshot or absolute-positioned page. Desktop 1920 and mobile 390 references guided spacing and proportions, with intermediate tablet/laptop layouts adapted using media queries. Additional laptop context requests reached the Figma account's tool limit after the desktop/mobile detail was collected.

Reusable components provide consistent card, header, and footer rendering. Real record counts replace decorative carousel totals; the current year replaces the design's sample copyright. The privacy and terms dialogs explain demonstration content rather than presenting sample property listings as real business offers. Social icons link to the named platforms; no company profile or office address is fabricated.

## Theme and content architecture

The site uses a classic PHP theme, plain CSS, and vanilla JavaScript, with no page builder or frontend framework. WordPress handles document titles, the main page loop, media, menus, and post queries. Minified CSS and deferred JavaScript are built with esbuild; readable sources remain in the repository. The hosting server requires no Node toolchain.

ACF Free field groups are registered in code for homepage text, statistics, services, hero image, contact email, and property/client metadata. Native meta fields remain available if ACF is disabled. Properties, testimonials, and FAQs use separate dashboard content types. Editors manage titles, excerpts, body content, and featured images. An explicit, repeatable demo importer creates the sample records without overwriting existing edits or silently changing an existing front page. The standard page editor can add content after the hero.

## Functionality and security

The menu, banner dismissal, carousel controls, collection views, and native HTML dialogs are interactive. Dialogs support keyboard focus, Escape, and focus return. Reduced-motion preferences are respected. The footer form progressively enhances a server-side POST: it validates the original email, checks a nonce, rejects a honeypot, limits attempts by IP, and stores email/date with a unique database index. New and repeated subscriptions receive the same success response to avoid address enumeration. Subscriber listing and deletion require administrator capability; no automated emails or external marketing integration are implied.

Output is escaped by context and dialog body HTML is filtered through WordPress. The theme adds no public upload endpoint, dynamic include, or shell execution. Local credentials, databases, and logs are excluded from the repository and package. PHP 8.3.36 was used locally, including the critical October 2026 fixes; production must use a maintained, patched runtime. The hosting guide covers PHP startup settings, HTTPS, request limits, upload execution denial, backups, and the boundary between theme protections and server patching.

## Verification, tools, and handoff

The implementation was run on WordPress 7.1.4 with ACF Free 6.8.10 and the WordPress SQLite integration for the local fixture. Production instructions use MySQL/MariaDB. PHP syntax and JavaScript checks pass. HTTP tests cover asset responses, form validation, nonce failures, persistence, duplicates, throttling, no-JavaScript redirects, and logged-out admin access. Eleven content/permission assertions cover CMS edits and repeatable imports. WordPress/plugin distribution checksums were verified.

Chromium browser checks exercised the responsive page at mobile, tablet, and desktop sizes, plus navigation, dialogs, focus return, card movement, and newsletter submission. Full-page desktop/mobile screenshots were inspected. Firefox, Safari, Edge, production MySQL, hosted performance, and server configuration still require verification; no unperformed checks are claimed. AI assisted drafting and design inspection, followed by WordPress adaptation, code review, browser inspection, and integration tests.

The handoff includes the installable theme ZIP, source repository, this report, and setup/security instructions. Public deployment is pending the owner's hosting purchase. A localhost URL is not a substitute for the required public assessment demo. Implementation started on 29 October 2026 around 21:30 WIB; clarification pauses occurred and active time was not separately measured.
