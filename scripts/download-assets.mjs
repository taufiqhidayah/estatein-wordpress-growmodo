import fs from "node:fs";
import path from "node:path";
import { execFileSync } from "node:child_process";

// Input is the locally retained Figma MCP design-context response, never runtime URLs.
const output = "theme/estatein/assets/images";
fs.mkdirSync(output, { recursive: true });
const manifest = {};
const downloads = new Map();
for (const file of fs
  .readdirSync(".references")
  .filter((file) => file.endsWith(".txt"))) {
  const source = fs.readFileSync(path.join(".references", file), "utf8");
  const prefix = source.match(/const assetPathPrefix = "([^"]+)"/)?.[1];
  if (!prefix) continue;
  manifest[file.replace(".txt", "")] = {};
  for (const [, name, filename] of source.matchAll(
    /const (img\w*) = `\$\{assetPathPrefix\}\/([^`]+)`/g,
  )) {
    manifest[file.replace(".txt", "")][name] = filename;
    downloads.set(filename, `${prefix}/${filename}`);
  }
}
for (const [filename, url] of downloads) {
  const target = path.join(output, filename);
  if (!fs.existsSync(target))
    execFileSync("curl", ["-fsSL", "--retry", "2", "-o", target, url]);
  if (!fs.statSync(target).size) throw new Error(`Empty asset: ${filename}`);
}
fs.writeFileSync(
  "theme/estatein/assets/figma-assets.json",
  JSON.stringify(manifest, null, 2) + "\n",
);
console.log(`Downloaded and verified ${downloads.size} Figma assets.`);
