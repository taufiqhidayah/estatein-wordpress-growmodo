# Estatein - WordPress developer assessment

A custom WordPress theme implementing the Estatein homepage from Produce UI's Figma design for the Growmodo assessment. Built with PHP, CSS, and vanilla JavaScript, without a page builder or frontend framework.

![Estatein preview](theme/estatein/screenshot.png)

## Deliverable status

- Complete homepage: header, hero, service cards, featured properties, testimonials, FAQs, CTA, footer.
- Editable page fields, property/testimonial/FAQ records, reusable PHP templates, responsive layouts, accessible native dialogs, mobile navigation, and subscriber storage.
- ACF Free is supported; native fields remain available without it. No ACF Pro dependency.
- **Public WordPress deployment is pending the owner's hosting purchase. Local preview does not satisfy the brief's live-demo requirement.**
- The other five Figma page types are outside this agreed four-hour homepage scope. Navigation points to completed homepage sections. Social icons open the named platforms, not invented company profiles.

## Install on WordPress hosting

1. Use a maintained PHP release with current security patches and MySQL/MariaDB. Review [deployment/security.md](deployment/security.md), including the October 2026 PHP advisories.
2. Upload `deliverables/estatein-theme.zip` using **Appearance -> Themes -> Add New -> Upload Theme**, then activate Estatein.
3. Optionally install **Advanced Custom Fields (Free)** from the official WordPress plugin directory.
4. Open **Appearance -> Estatein setup -> Import Estatein demo**. The importer creates Home and the sample content. If a front page already exists, select the imported Home under **Settings -> Reading** yourself; the importer preserves the previous selection.
5. Edit **Pages -> Home** for headings, statistics, service labels, hero image, and contact email. Main editor content renders after the hero. Edit Properties, Testimonials, and FAQs from their dashboard menus. Use excerpts for card descriptions and main content for details.
6. Add real contact details, configure your privacy information and social destinations for a real business, and use test email addresses during assessment review. Subscribers are under the admin-only **Subscribers** menu; deletion is supported.
7. Enable HTTPS, check the public URL while logged out, and repeat the live checks in [deployment/README.md](deployment/README.md).

The theme ZIP contains ready-to-run assets. Node, npm, SQLite, WP-CLI, and the local testing tools are **not** needed on the hosting server. Subscriber data remains in the site's database when changing themes; no destructive uninstall runs automatically.

## Local development

Requires Node.js and a patched PHP CLI with PDO SQLite, mbstring, GD, XML, cURL, and Zip extensions. The setup script downloads official WordPress, ACF Free, the WordPress SQLite integration, and WP-CLI into ignored `.runtime/`.

```sh
npm ci --ignore-scripts
npm run build
export ESTATEIN_PHP=/path/to/php
npm run setup:local
"$ESTATEIN_PHP" -c .runtime/php.ini -S 127.0.0.1:8888 -t .runtime/wordpress scripts/local-router.php
```

On this Mac, PHP is at `/opt/homebrew/opt/php@8.3/bin/php`. Local credentials are randomly generated in `.runtime/local-access.json`, with file mode 0600. Never publish `.runtime/`, local database files, or these credentials. SQLite is only the local testing fixture; production instructions use MySQL/MariaDB.

```sh
npm run test:http
"$ESTATEIN_PHP" .runtime/wp-cli.phar --path=.runtime/wordpress eval-file tests/content.php
```

The tests use and clean up explicitly named local test records. Do not run them against production. `tests/http.mjs` refuses non-local URLs.

## Code layout and decisions

- `theme/estatein/`: installable theme. `inc/` holds content queries, ACF/native field definitions, setup/import, and the newsletter handler. `template-parts/` holds reusable cards.
- `assets/css/main.css` and `assets/js/main.js`: readable sources. `npm run build` creates their minified production counterparts; PHP enqueues the minified files with versioning and deferred JavaScript.
- `scripts/`: asset preparation, local fixture setup, and production asset build. Figma assets are already included; `download-assets.mjs` is only useful with retained design-context responses in ignored `.references/`.
- `tests/`: HTTP behavior/security checks and WordPress content/permission checks.
- `docs/`: development report and verification notes. `deployment/`: hosting handoff and security configuration examples.

The public subscription endpoint only stores a mailing-list record; it does not promise email delivery or send to external services. Duplicate subscriptions receive the same success response, preventing address enumeration. Per-IP throttling is basic abuse deterrence, not a replacement for hosting request limits or a WAF.

## Design and attribution

Design: [Estatein real estate template by Produce UI](https://www.figma.com/community/file/1314076616839640516). Images, icons, and abstract artwork were downloaded from the supplied Figma document and bundled locally. Urbanist is self-hosted; its SIL Open Font License is included in `assets/fonts/OFL.txt`. Theme code is GPL-2.0-or-later; original design assets retain their respective licenses.

AI assisted code drafting and design inspection. The implementation was adapted to WordPress and checked through actual browser use and local integration tests. See [verification notes](docs/VERIFICATION.md) for tested behavior and explicit limits.
