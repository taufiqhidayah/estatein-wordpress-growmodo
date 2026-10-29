import fs from "node:fs";
import path from "node:path";
import crypto from "node:crypto";
import { execFileSync } from "node:child_process";

const root = process.cwd();
const runtime = path.join(root, ".runtime");
const wordpress = path.join(runtime, "wordpress");
const php =
  process.env.ESTATEIN_PHP ||
  (fs.existsSync("/opt/homebrew/opt/php@8.3/bin/php")
    ? "/opt/homebrew/opt/php@8.3/bin/php"
    : "php");
fs.mkdirSync(runtime, { recursive: true });
const run = (file, args, options = {}) =>
  execFileSync(file, args, { stdio: "pipe", ...options });
function download(url, file) {
  if (!fs.existsSync(file))
    run("curl", ["-fsSL", "--retry", "2", url, "-o", file]);
}
if (!fs.existsSync(wordpress)) {
  download(
    "https://wordpress.org/latest.zip",
    path.join(runtime, "wordpress.zip"),
  );
  run("unzip", ["-q", path.join(runtime, "wordpress.zip"), "-d", runtime]);
}
for (const plugin of [
  "sqlite-database-integration",
  "advanced-custom-fields",
]) {
  if (!fs.existsSync(path.join(wordpress, "wp-content/plugins", plugin))) {
    download(
      `https://downloads.wordpress.org/plugin/${plugin}.latest-stable.zip`,
      path.join(runtime, `${plugin}.zip`),
    );
    run("unzip", [
      "-q",
      path.join(runtime, `${plugin}.zip`),
      "-d",
      path.join(wordpress, "wp-content/plugins"),
    ]);
  }
}
download(
  "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar",
  path.join(runtime, "wp-cli.phar"),
);
fs.copyFileSync(
  path.join(
    wordpress,
    "wp-content/plugins/sqlite-database-integration/db.copy",
  ),
  path.join(wordpress, "wp-content/db.php"),
);
const themeLink = path.join(wordpress, "wp-content/themes/estatein");
if (!fs.existsSync(themeLink))
  fs.symlinkSync(path.join(root, "theme/estatein"), themeLink);
const wp = (args, options) =>
  run(
    php,
    [path.join(runtime, "wp-cli.phar"), `--path=${wordpress}`, ...args],
    options,
  ).toString();
if (!fs.existsSync(path.join(wordpress, "wp-config.php"))) {
  wp([
    "config",
    "create",
    "--dbname=estatein",
    "--dbuser=local",
    "--dbpass=local",
    "--skip-check",
  ]);
  wp(["config", "set", "WP_DEBUG", "true", "--raw"]);
  wp(["config", "set", "WP_DEBUG_DISPLAY", "false", "--raw"]);
  wp([
    "config",
    "set",
    "WP_DEBUG_LOG",
    path.join(runtime, "wordpress-debug.log"),
  ]);
  wp(["config", "set", "DISALLOW_FILE_EDIT", "true", "--raw"]);
  wp(["config", "set", "WP_ENVIRONMENT_TYPE", "local"]);
}
try {
  wp(["core", "is-installed"]);
} catch {
  const password = crypto.randomBytes(24).toString("base64url");
  wp(
    [
      "core",
      "install",
      "--url=http://127.0.0.1:8888",
      "--title=Estatein — Find Your Dream Property",
      "--admin_user=estatein_local",
      "--admin_email=assessment@example.test",
      "--skip-email",
      "--prompt=admin_password",
    ],
    { input: `${password}\n` },
  );
  fs.writeFileSync(
    path.join(runtime, "local-access.json"),
    JSON.stringify(
      {
        url: "http://127.0.0.1:8888/wp-admin/",
        username: "estatein_local",
        password,
      },
      null,
      2,
    ),
    { mode: 0o600 },
  );
}
wp(["theme", "activate", "estatein"]);
// CLI installation can infer the physical wordpress/ folder; the dev server serves it at /.
wp(["option", "update", "home", "http://127.0.0.1:8888"]);
wp(["option", "update", "siteurl", "http://127.0.0.1:8888"]);
wp([
  "plugin",
  "activate",
  "advanced-custom-fields",
  "sqlite-database-integration",
]);
wp([
  "option",
  "update",
  "blogdescription",
  "Discover your dream property with Estatein.",
]);
wp(["option", "update", "default_comment_status", "closed"]);
wp(["option", "update", "default_ping_status", "closed"]);
console.log(
  wp([
    "eval",
    'wp_set_current_user(1); $result = estatein_import_demo(); if (is_wp_error($result)) { WP_CLI::error($result->get_error_message()); } WP_CLI::success("Demo content is ready.");',
  ]),
);
fs.writeFileSync(
  path.join(runtime, "php.ini"),
  `expose_php=Off\ndisplay_errors=Off\nlog_errors=On\nerror_log="${path.join(runtime, "php-errors.log")}"\nsession.upload_progress.enabled=0\npost_max_size=16M\nupload_max_filesize=12M\n`,
);
console.log(
  "Local WordPress ready. Credentials are in ignored .runtime/local-access.json (not printed).",
);
console.log(
  `Run: ${php} -c .runtime/php.ini -S 127.0.0.1:8888 -t .runtime/wordpress scripts/local-router.php`,
);
