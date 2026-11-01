# Security notes - reviewed 29 October 2026

## PHP advisories and runtime boundary

PHP's [21 October 2026 advisory](https://www.php.net/archive/2026.php#2026-10-21-5) describes two critical unauthenticated RCE vulnerabilities (CVE-2026-109618 and CVE-2026-109619). Patched versions include 8.3.36, 8.4.27, and 8.5.12. Theme-level sanitization, a plugin, or hidden error messages cannot patch these runtime vulnerabilities. Ask the hosting provider to apply current vendor patches and restart PHP workers.

The [23 October advisory](https://www.php.net/archive/2026.php#2026-10-23-1) also discusses session upload progress and an unusual configuration/runtime handler combination. Ask the provider to set `session.upload_progress.enabled=0` at PHP startup (php.ini, FPM pool, or server config) and restart workers. Do not rely on `ini_set()` in a theme or `.user.ini` as a fix before request parsing. Windows hosting also needs the [26 October Windows dependency rebuild](https://www.php.net/archive/2026.php#2026-10-26-1), or a later patched build.

Use the [PHP supported-versions list](https://www.php.net/supported-versions.php) when selecting the hosting runtime. Recheck advisories at deployment; these are dated baselines, not a permanent guarantee.

## Implemented in this theme

- Output is escaped by context; rich editor content in dialogs passes through `wp_kses_post`. Native saves check a nonce and `edit_post` capability. ACF manages its own save validation.
- The subscription handler requires POST and a valid WordPress nonce, validates the original email before sanitizing, rejects a honeypot, caps input length, and applies a per-IP transient limit (five attempts in a sliding ten-minute window).
- Custom SQL uses prepared values and a unique email index. Repeated addresses get an identical public success response. No public endpoint lists subscribers. Admin listing/deletion requires `manage_options`; deletion also checks a nonce.
- Subscriber records contain only email and UTC creation time. Raw IP addresses are not stored; short-lived rate-limit keys use a salted HMAC. Marketing delivery, verification mail, and third-party integrations are not configured.
- No public file-upload handler, SVG-upload permission, dynamic PHP include, shell execution, or `eval` is introduced by the theme. Bundled SVGs are trusted static design assets, not user uploads.
- Frontend responses set `X-Content-Type-Options`, `Referrer-Policy`, and a restrictive camera/microphone/geolocation `Permissions-Policy`.
- Database credentials, salts, local admin access, test databases, and debug logs are excluded from Git and the theme package.

- Contact/property enquiries use the same POST/nonce/honeypot pattern with a separate rate bucket. They store name, email, optional phone, property ID, message, consent, UTC timestamp, and bounded preference data. They are visible only under the administrator Enquiries menu; no public REST endpoint exposes them. Review retention and privacy requirements before accepting real data.

## Apply through hosting configuration

These settings belong to the server or WordPress configuration, not a theme. Review with the provider and preserve working WordPress rewrite rules.

Suggested PHP startup configuration:

```ini
expose_php = Off
display_errors = Off
log_errors = On
session.upload_progress.enabled = 0
post_max_size = 16M
upload_max_filesize = 12M
max_input_vars = 2000
```

Store error logs outside the public document root. Apply body/field/multipart request limits at the web server or proxy as well. Keep reasonable worker/container limits. Do not treat these limits as a replacement for patched PHP.

Production `wp-config.php` additions (never commit the real config):

```php
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_ENVIRONMENT_TYPE', 'production' );
define( 'FORCE_SSL_ADMIN', true ); // After HTTPS and proxy handling are correctly configured.
```

Enable HTTPS redirects and `X-Frame-Options: SAMEORIGIN` at the web server. Enable HSTS only after HTTPS works across the intended hostname; do not automatically add `includeSubDomains` or preload. A CSP needs testing against the site's actual WordPress/plugins and should first be evaluated in report-only mode; the theme does not impose a broad policy that breaks the editor.

For Apache hosting, put this provider-approved rule in `wp-content/uploads/.htaccess` to deny common executable extensions (Apache 2.4+):

```apache
<FilesMatch "\.(?i:php[0-9]?|phtml|phar|pht|phps)(?:\.|$)">
    Require all denied
</FilesMatch>
```

For Nginx, have the provider place an equivalent rule before any generic PHP location, covering nested paths and PATH_INFO:

```nginx
location ~* ^/wp-content/uploads/.*\.(php[0-9]?|phtml|phar|pht|phps)(/|\.|$) {
    deny all;
}
```

Adjust the URL prefix when WordPress is installed in a subdirectory. Deny public access to configuration backups, database files, dotfiles, and logs. Use filesystem ownership/permissions suitable for the hosting stack and do not make everything world-writable.

Remove unused plugins/themes, keep core and installed plugins updated, use strong admin credentials and MFA, and retain off-site backups. Add provider request throttling or a WAF for distributed abuse: a WordPress transient throttle is not an atomic distributed rate limiter. Do not enable SVG uploads or add arbitrary upload plugins for this theme.

## Verification limits

Local checks are useful but are not a penetration test or a guarantee of safety. Production MySQL behavior, TLS/proxy configuration, server deny rules, backup restoration, and the provider's actual PHP patches must be verified after hosting is provisioned. A verified plugin checksum proves distribution integrity, not absence of vulnerabilities.
