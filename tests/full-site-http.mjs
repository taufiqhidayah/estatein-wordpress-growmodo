import assert from "node:assert/strict";
const base = "http://127.0.0.1:8888";
const paths = [
  "/",
  "/about-us/",
  "/properties/",
  "/services/",
  "/contact/",
  "/properties/seaside-serenity-villa/",
];
const pages = new Map();
const assets = new Set();
for (const path of paths) {
  const response = await fetch(base + path);
  assert.equal(response.status, 200, path);
  const html = await response.text();
  pages.set(new URL(path, base).href, html);
  assert.equal((html.match(/<h1\b/g) || []).length, 1, path + " one H1");
  assert.ok(
    !html.includes("Fatal error") && !html.includes("Warning:"),
    path + " no PHP diagnostics",
  );
  assert.ok(
    !/<a\b[^>]*href=["'](?:#["']|["'])/i.test(html),
    path + " no empty links",
  );
  const ids = [...html.matchAll(/\bid="([^"]+)"/g)].map((x) => x[1]);
  assert.equal(ids.length, new Set(ids).size, path + " unique IDs");
  for (const m of html.matchAll(
    /(?:src|href)="([^"]+\.(?:png|svg|webp|jpe?g|css|js|woff2)(?:\?[^"]*)?)"/gi,
  )) {
    const url = new URL(m[1].replaceAll("&amp;", "&"), base);
    if (url.origin === base) assets.add(url.href);
  }
}
for (const [source, html] of pages) {
  for (const m of html.matchAll(/<a\b[^>]*href="([^"]+)"/g)) {
    const url = new URL(m[1].replaceAll("&amp;", "&"), source);
    if (url.origin !== base || !url.hash) continue;
    const target = new URL(url);
    target.hash = "";
    target.search = "";
    const dest = pages.get(target.href);
    if (dest)
      assert.ok(
        dest.includes(`id="${decodeURIComponent(url.hash.slice(1))}"`),
        `${source} -> ${url.href}`,
      );
  }
}
for (const url of assets) {
  const response = await fetch(url);
  assert.equal(response.status, 200, url);
  assert.ok(!response.headers.get("content-type")?.includes("text/html"), url);
}
const cases = [
  ["?property_type=Apartment", "Metropolitan Haven", 1],
  ["?price_range=under-500k", "Rustic Retreat Cottage", 1],
  ["?property_search=Seaside", "Seaside Serenity Villa", 1],
  ["?property_type=unavailable", "No properties found", 0],
  ["?property_page=9999", "Seaside Serenity Villa", 3],
  ["?property_size=medium", "Seaside Serenity Villa", 3],
  ["?property_search[]=malformed", "Seaside Serenity Villa", 3],
];
for (const [query, text, count] of cases) {
  const html = await (await fetch(base + "/properties/" + query)).text();
  assert.ok(html.includes(text), query + " expected text");
  assert.equal(
    (html.match(/class="card catalog-card"/g) || []).length,
    count,
    query + " results",
  );
}
const serviceContact = await (
  await fetch(base + "/contact/?inquiry_type=sell")
).text();
assert.match(
  serviceContact,
  /<option value="sell"\s+selected=['"]selected['"]>/,
);
console.log(
  `PASS: six page routes, one H1, unique IDs, internal anchors, ${assets.size} local assets, seven catalog scenarios, and service enquiry preselection.`,
);
