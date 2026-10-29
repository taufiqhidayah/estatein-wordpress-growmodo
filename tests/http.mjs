import fs from "node:fs";
import assert from "node:assert/strict";
import { execFileSync } from "node:child_process";

const base = process.env.ESTATEIN_TEST_URL || "http://127.0.0.1:8888";
if (!["127.0.0.1", "localhost"].includes(new URL(base).hostname))
  throw new Error(
    "Run destructive test-data checks only against the local development site.",
  );
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
  );
const resetRate = () =>
  wp(
    'global $wpdb; $ip="127.0.0.1"; delete_transient("estatein_rate_" . hash_hmac("sha256", $ip, wp_salt("nonce")));',
  );
const htmlResponse = await fetch(base);
assert.equal(htmlResponse.status, 200);
assert.equal(htmlResponse.headers.get("x-content-type-options"), "nosniff");
assert.equal(htmlResponse.headers.get("x-powered-by"), null);
const html = await htmlResponse.text();
const nonce = html.match(/name="estatein_nonce" value="([^"]+)"/)?.[1];
assert.ok(nonce, "Server-rendered nonce");
assert.equal((html.match(/<h1\b/g) || []).length, 1);
assert.ok(
  !/https:\/\/www\.figma\.com\/api\/mcp/.test(html),
  "No temporary asset URLs",
);
const assetUrls = [
  ...new Set(
    [
      ...html.matchAll(
        /(?:src|href)=["']([^"']+\.(?:png|svg|css|js|woff2)(?:\?[^"']*)?)["']/g,
      ),
    ].map((match) => match[1].replaceAll("&#038;", "&")),
  ),
];
for (const url of assetUrls) {
  const response = await fetch(url);
  assert.equal(response.status, 200, url);
  assert.ok(
    !response.headers.get("content-type")?.includes("text/html"),
    `Correct asset MIME: ${url}`,
  );
}
console.log(
  `PASS: homepage, security headers, one H1, and ${assetUrls.length} assets.`,
);
const originalTitle = wp(
  'echo base64_encode(get_post_meta((int)get_option("page_on_front"), "estatein_hero_title", true));',
).trim();
try {
  wp(
    'update_post_meta((int)get_option("page_on_front"), "estatein_hero_title", "<img src=x onerror=alert(1)>");',
  );
  const escaped = await (await fetch(base)).text();
  assert.ok(escaped.includes("&lt;img src=x onerror=alert(1)&gt;"));
  assert.ok(!escaped.includes('<h1 id="hero-title"><img'));
  console.log("PASS: editable heading content is HTML-escaped.");
} finally {
  wp(
    `update_post_meta((int)get_option("page_on_front"), "estatein_hero_title", base64_decode("${originalTitle}"));`,
  );
}
const email = "estatein-http-qa@example.test";
const cleanup = () => {
  wp(
    'global $wpdb; $wpdb->delete($wpdb->prefix . "estatein_subscribers", array("email" => "estatein-http-qa@example.test"));',
  );
  resetRate();
};
const post = async (values, expectedCode, expectedStatus) => {
  const body = new URLSearchParams({
    action: "estatein_subscribe",
    estatein_nonce: nonce,
    email,
    ...values,
  });
  const response = await fetch(`${base}/wp-admin/admin-ajax.php`, {
    method: "POST",
    body,
  });
  const result = await response.json();
  assert.equal(response.status, expectedCode, JSON.stringify(result));
  assert.equal(result.status, expectedStatus);
};
cleanup();
try {
  await post({ estatein_nonce: "" }, 403, "expired");
  await post({ estatein_nonce: "invalid-nonce" }, 403, "expired");
  await post(
    { email: "<script>alert(1)</script>@example.test" },
    400,
    "invalid",
  );
  await post({ email: "not-an-email" }, 400, "invalid");
  await post({ company_website: "spam.example" }, 400, "invalid");
  await post({}, 200, "success");
  await post({}, 200, "success");
  await post({ email: "still-not-an-email" }, 400, "invalid");
  await post({}, 429, "limited");
  assert.equal(
    wp(
      'global $wpdb; echo $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}estatein_subscribers WHERE email = %s", "estatein-http-qa@example.test"));',
    ).trim(),
    "1",
  );
  console.log(
    "PASS: nonce checks, XSS/malformed input rejection, honeypot, save, duplicate, unique record, and rate limit.",
  );
  resetRate();
  const response = await fetch(`${base}/wp-admin/admin-post.php`, {
    method: "POST",
    body: new URLSearchParams({
      action: "estatein_subscribe",
      estatein_nonce: nonce,
      email,
    }),
    redirect: "manual",
  });
  assert.equal(response.status, 303);
  assert.equal(
    response.headers.get("location"),
    `${base}/?subscription=success#newsletter`,
  );
  console.log(
    "PASS: form submission without JavaScript redirects to a truthful result.",
  );
  await post({ email: "x".repeat(260) + "@example.test" }, 400, "invalid");
  await post({ "email[]": "not-a-scalar" }, 400, "invalid");
  console.log("PASS: oversized and array-shaped email inputs are rejected.");
  const unauthenticated = await fetch(
    `${base}/wp-admin/admin.php?page=estatein-subscribers`,
    { redirect: "manual" },
  );
  assert.equal(unauthenticated.status, 302);
  assert.ok(unauthenticated.headers.get("location")?.includes("wp-login.php"));
  console.log("PASS: subscriber dashboard requires authentication.");
} finally {
  cleanup();
}
