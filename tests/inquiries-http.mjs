import fs from "node:fs";
import assert from "node:assert/strict";
import { execFileSync } from "node:child_process";
const base = "http://127.0.0.1:8888";
const php =
  process.env.ESTATEIN_PHP ||
  (fs.existsSync("/opt/homebrew/opt/php@8.3/bin/php")
    ? "/opt/homebrew/opt/php@8.3/bin/php"
    : "php");
const wp = (code) =>
  execFileSync(
    php,
    [".runtime/wp-cli.phar", "--path=.runtime/wordpress", "eval", code],
    { encoding: "utf8" },
  ).trim();
const nonce = wp(
  'wp_set_current_user(0); estatein_inquiries_install(); echo wp_create_nonce("estatein_inquiry");',
);
const email = "estatein-inquiry-http-qa@example.test";
const cleanup = () =>
  wp(
    'global $wpdb; $wpdb->delete($wpdb->prefix . "estatein_inquiries", array("email" => "estatein-inquiry-http-qa@example.test")); delete_transient("estatein_inquiry_rate_" . hash_hmac("sha256", "127.0.0.1", wp_salt("nonce")));',
  );
const data = {
  action: "estatein_inquiry",
  estatein_nonce: nonce,
  full_name: "HTTP QA",
  email,
  message: "Please tell me more about your properties.",
  consent: "1",
};
cleanup();
try {
  const invalid = await fetch(`${base}/wp-admin/admin-ajax.php`, {
    method: "POST",
    body: new URLSearchParams({ ...data, estatein_nonce: "" }),
  });
  assert.equal(invalid.status, 403);
  assert.equal((await invalid.json()).status, "expired");
  const response = await fetch(`${base}/wp-admin/admin-ajax.php`, {
    method: "POST",
    body: new URLSearchParams(data),
  });
  assert.equal(response.status, 200);
  assert.equal((await response.json()).status, "success");
  assert.equal(
    wp(
      'global $wpdb; echo $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}estatein_inquiries WHERE email = %s", "estatein-inquiry-http-qa@example.test"));',
    ),
    "1",
  );
  const noJs = await fetch(`${base}/wp-admin/admin-post.php`, {
    method: "POST",
    body: new URLSearchParams({
      ...data,
      return_to: "https://untrusted.example/",
    }),
    redirect: "manual",
  });
  assert.equal(noJs.status, 303);
  const location = new URL(noJs.headers.get("location"));
  assert.equal(location.origin, base);
  assert.equal(location.pathname, "/contact/");
  assert.equal(location.searchParams.get("enquiry"), "success");
  const admin = await fetch(
    `${base}/wp-admin/admin.php?page=estatein-inquiries`,
    { redirect: "manual" },
  );
  assert.equal(admin.status, 302);
  assert.ok(admin.headers.get("location")?.includes("wp-login.php"));
  console.log(
    "PASS: enquiry HTTP nonce protection, real persistence, owned redirect, no-JS handling, and protected admin route.",
  );
} finally {
  cleanup();
}
