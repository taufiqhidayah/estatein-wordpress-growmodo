import { build } from "esbuild";
import fs from "node:fs";
for (const extension of ["css", "js"]) {
  await build({
    ...(extension === "css"
      ? {
          stdin: {
            contents:
              fs.readFileSync("theme/estatein/assets/css/main.css", "utf8") +
              "\n" +
              fs.readFileSync("theme/estatein/assets/css/pages.css", "utf8"),
            loader: "css",
            resolveDir: process.cwd() + "/theme/estatein/assets/css",
          },
        }
      : {
          stdin: {
            contents:
              fs.readFileSync("theme/estatein/assets/js/main.js", "utf8") +
              "\n" +
              fs.readFileSync("theme/estatein/assets/js/pages.js", "utf8"),
            loader: "js",
            resolveDir: process.cwd() + "/theme/estatein/assets/js",
          },
        }),
    outfile: `theme/estatein/assets/${extension}/main.min.${extension}`,
    minify: true,
    target:
      extension === "js"
        ? ["es2020"]
        : ["chrome100", "safari15", "firefox100", "edge100"],
    legalComments: "none",
  });
}
console.log(
  "Production CSS and JavaScript built. No build tools are needed on WordPress hosting.",
);
