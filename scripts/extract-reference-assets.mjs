// Explicitly authorized local crops from the user's supplied screenshots.
// Originals stay in ignored .references/; no screenshot is used as a page background.
import fs from "node:fs";
import { createRequire } from "node:module";
const require = createRequire(import.meta.url);
const sharp = require(process.env.ESTATEIN_SHARP_PATH || "sharp");
const output = "theme/estatein/assets/images";
const contact = ".references/screenshots/contact.png";
const services = ".references/screenshots/services.png";
const rects = JSON.parse(
  fs.readFileSync(".references/icon-rects.json", "utf8"),
);
const names = [
  "valuation",
  "marketing",
  "negotiation",
  "closing",
  "tenant",
  "maintenance",
  "finance",
  "legal",
  "market",
  "roi",
  "strategies",
  "diversification",
];
for (const [i, r] of rects["104:10350"].slice(4).entries()) {
  await sharp(services)
    .extract({
      left: Math.round(r.x * 2),
      top: Math.round(r.y * 2),
      width: Math.round(r.w * 2),
      height: Math.round(r.h * 2),
    })
    .png()
    .toFile(`${output}/service-${names[i]}.png`);
}
for (const [i, r] of rects["104:12305"].entries()) {
  await sharp(contact)
    .extract({
      left: Math.round(r.x * 2),
      top: Math.round(r.y * 2),
      width: Math.round(r.w * 2),
      height: Math.round(r.h * 2),
    })
    .png()
    .toFile(`${output}/contact-icon-${i + 1}.png`);
}
const photos = [
  [242, 3233, 708.5, 236],
  [970.5, 3233, 708.5, 236],
  [242, 3489, 708.5, 236],
  [970.5, 3489, 344.25, 236],
  [1334.75, 3489, 344.25, 236],
  [970.5, 3745, 708.5, 280],
];
for (const [i, [x, y, w, h]] of photos.entries()) {
  await sharp(contact)
    .extract({
      left: Math.round(x * 2),
      top: Math.round(y * 2),
      width: Math.round(w * 2),
      height: Math.round(h * 2),
    })
    .webp({ quality: 88 })
    .toFile(`${output}/contact-gallery-${i + 1}.webp`);
}
// Sample team portraits are crops of the supplied group photograph, not generated identities.
const teamPhoto = `${output}/contact-gallery-2.webp`;
const size = await sharp(teamPhoto).metadata();
const width = Math.floor(size.width / 4);
for (let i = 0; i < 4; i++) {
  await sharp(teamPhoto)
    .extract({ left: i * width, top: 0, width, height: size.height })
    .webp({ quality: 88 })
    .toFile(`${output}/team-${i + 1}.webp`);
}
console.log(
  "Created 16 icon crops, six gallery photos, and four sample team portraits.",
);
const propertyIcons = JSON.parse(
  fs.readFileSync(".references/property-icon-rects.json", "utf8"),
);
const propertyNames = [
  "search",
  "location",
  "chevron",
  "type",
  null,
  "price",
  null,
  "size",
  null,
  "year",
];
for (const [i, r] of propertyIcons.entries()) {
  if (!propertyNames[i]) continue;
  await sharp(".references/screenshots/properties.png")
    .extract({
      left: Math.round(r.x),
      top: Math.round(r.y),
      width: Math.round(r.w),
      height: Math.round(r.h),
    })
    .png()
    .toFile(`${output}/filter-${propertyNames[i]}.png`);
}
