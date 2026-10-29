import { build } from 'esbuild';
for (const extension of ['css', 'js']) {
  await build({
    entryPoints: [`theme/estatein/assets/${extension}/main.${extension}`],
    outfile: `theme/estatein/assets/${extension}/main.min.${extension}`,
    minify: true,
    target: extension === 'js' ? ['es2020'] : ['chrome100', 'safari15', 'firefox100', 'edge100'],
    legalComments: 'none',
  });
}
console.log('Production CSS and JavaScript built. No build tools are needed on WordPress hosting.');
