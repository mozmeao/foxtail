// Builds the theme's CSS and JS. See "Building CSS and JS" in README.md.
//
//   node scripts/build.mjs          build everything once
//   node scripts/build.mjs --watch  rebuild when sources change
//
// CSS: assets/sass/{main,editor}.scss -> Sass -> Lightning CSS (prefixes for
//      the package.json browserslist, minify) -> assets/css/{main,editor}.min.css
// JS:  assets/js/custom/*.js and assets/js/vendor/**/*.js are joined in
//      alphabetical order (as the old gulp build did) and minified with
//      esbuild -> assets/js/custom.min.js and assets/js/vendor.min.js

import { readFile, readdir, writeFile } from 'node:fs/promises';
import { watch } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import * as sass from 'sass';
import browserslist from 'browserslist';
import { browserslistToTargets, transform as transformCss } from 'lightningcss';
import { transform as transformJs } from 'esbuild';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const at = (...parts) => path.join(root, ...parts);

const pkg = JSON.parse(await readFile(at('package.json'), 'utf8'));
const targets = browserslistToTargets(browserslist(pkg.browserslist));

// The theme and the vendored Protocol SCSS use @import and other syntax
// Dart Sass has deprecated. They still compile; silence the noise until the
// SCSS is modernized.
const silenceDeprecations = ['import', 'global-builtin', 'slash-div', 'color-functions', 'if-function'];

const cssEntries = ['main', 'editor'];
const jsBundles = [
  { out: 'custom.min.js', dir: 'assets/js/custom' },
  { out: 'vendor.min.js', dir: 'assets/js/vendor' },
];

async function buildCss(name) {
  const src = at('assets/sass', `${name}.scss`);
  const { css } = sass.compile(src, { style: 'expanded', silenceDeprecations });
  const result = transformCss({
    filename: `${name}.css`,
    code: Buffer.from(css),
    minify: true,
    targets,
    errorRecovery: true,
  });
  for (const warning of result.warnings) {
    console.warn(`  ${name}.css: ${warning.message} (line ${warning.loc.line})`);
  }
  await writeFile(at('assets/css', `${name}.min.css`), result.code);
  return `assets/css/${name}.min.css`;
}

async function listJs(dir) {
  const entries = await readdir(at(dir), { recursive: true, withFileTypes: true });
  return entries
    .filter((e) => e.isFile() && e.name.endsWith('.js'))
    .map((e) => path.relative(root, path.join(e.parentPath, e.name)))
    .sort();
}

async function buildJs({ out, dir }) {
  const files = await listJs(dir);
  const joined = (await Promise.all(files.map((f) => readFile(at(f), 'utf8')))).join('\n');
  const { code } = await transformJs(joined, { loader: 'js', minify: true, legalComments: 'none' });
  await writeFile(at('assets/js', out), code);
  return `assets/js/${out} (${files.length} files)`;
}

async function build(what = 'all') {
  const started = Date.now();
  const jobs = [];
  if (what !== 'js') jobs.push(...cssEntries.map(buildCss));
  if (what !== 'css') jobs.push(...jsBundles.map(buildJs));
  const built = await Promise.all(jobs);
  console.log(`Built in ${Date.now() - started}ms:\n  ${built.join('\n  ')}`);
}

if (process.argv.includes('--watch')) {
  await build().catch((err) => console.error(err.message));
  let timer;
  const rebuild = (what) => {
    clearTimeout(timer);
    timer = setTimeout(() => build(what).catch((err) => console.error(err.message)), 100);
  };
  watch(at('assets/sass'), { recursive: true }, () => rebuild('css'));
  for (const { dir } of jsBundles) watch(at(dir), { recursive: true }, () => rebuild('js'));
  console.log('Watching assets/sass and assets/js for changes...');
} else {
  await build();
}
