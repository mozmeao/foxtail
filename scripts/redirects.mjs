// Validates redirects/posts.csv and redirects/categories.csv and writes the
// lookup the theme uses (inc/redirects-map.php). See "Redirects" in README.md.
//
//   node scripts/redirects.mjs   validate and generate (npm run build does this too)
//
// Any error stops with a non-zero exit, so a bad row fails `npm run build`,
// CI and deploys. Rows with active=no are kept in the lookup but only produce
// an X-Foxtail-Redirect response header (see inc/redirects.php), never a redirect.

import { readFileSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
export const at = (...parts) => path.join(root, ...parts);

export const POSTS_CSV = at('redirects', 'posts.csv');
export const CATEGORIES_CSV = at('redirects', 'categories.csv');
export const MAP_PHP = at('inc', 'redirects-map.php');

export const POST_COLUMNS = ['locale', 'slug', 'wp_id', 'active', 'new_url', 'old_url', 'note'];
export const CATEGORY_COLUMNS = ['locale', 'path', 'active', 'new_url', 'note'];

// WPML languages on blog.mozilla.org (language directories /en/, /de/, ...).
export const LOCALES = ['en', 'de', 'fr', 'es', 'id', 'it', 'ja', 'pl', 'pt-br', 'ru', 'zh-hans'];

// Category slugs that belong to Mozilla, not Firefox. Their archives must stay
// on blog.mozilla.org (blog-gaps.md section 4).
const MOZILLA_CATEGORIES = ['mozilla', 'news', 'ai', 'ai-tech', 'internet-policy', 'internet-culture', 'life-online', 'uncategorized'];

// firefox.com blog URLs: /<locale>/blog/<slug>/ for posts, the blog home or
// /<locale>/blog/topics/<topic>/ for categories. `all` and `topics` are
// BlogIndexPage sub-routes, never post slugs.
const POST_TARGET = /^https:\/\/www\.firefox\.com\/[a-z]{2}(-[A-Z]{2})?\/blog\/([a-z0-9-]+)\/$/;
const CATEGORY_TARGET = /^https:\/\/www\.firefox\.com\/[a-z]{2}(-[A-Z]{2})?\/blog\/(topics\/[a-z0-9-]+\/)?$/;
const RESERVED_TARGET_SLUGS = ['all', 'topics'];

// WordPress slugs are lowercase; non-ASCII ones are stored percent-encoded.
const SLUG = /^[a-z0-9_%-]+$/;
// Path segments the theme never treats as a post slug (archives, feeds, ...).
const RESERVED_SLUGS = ['feed', 'page', 'category', 'tag', 'author', 'search', 'comments', 'collections', 'amp', 'latest', 'blog'];

// ---------------------------------------------------------------------------
// CSV (RFC 4180: comma separated, double quotes around fields that need them)

export function parseCsv(text) {
  // Excel and Numbers start UTF-8 files with a byte order mark.
  text = text.replace(/^\uFEFF/, '');
  const rows = [];
  let row = [];
  let field = '';
  let quoted = false;
  let line = 1; // physical line of the current character
  let start = 1; // line the current row started on
  const endRow = () => { row.push(field); rows.push({ line: start, fields: row }); row = []; field = ''; start = line; };
  for (let i = 0; i < text.length; i++) {
    const c = text[i];
    if (c === '\n' || (c === '\r' && text[i + 1] !== '\n')) line++;
    if (quoted) {
      if (c === '"' && text[i + 1] === '"') { field += '"'; i++; }
      else if (c === '"') quoted = false;
      else field += c;
    } else if (c === '"') quoted = true;
    else if (c === ',') { row.push(field); field = ''; }
    else if (c === '\n' || c === '\r') {
      if (c === '\r' && text[i + 1] === '\n') { i++; line++; }
      endRow();
    } else field += c;
  }
  if (field !== '' || row.length) endRow();
  const [head, ...body] = rows.filter((r) => r.fields.some((f) => f.trim() !== ''));
  const header = head ? head.fields.map((h) => h.trim()) : [];
  return {
    header,
    rows: body.map((r) => ({ line: r.line, ...Object.fromEntries(header.map((h, j) => [h, (r.fields[j] ?? '').trim()])) })),
  };
}

export function readCsv(file, columns) {
  const { header, rows } = parseCsv(readFileSync(file, 'utf8'));
  const name = path.relative(root, file);
  if (header.join(',') !== columns.join(',')) {
    throw new Error(`${name}: header must be exactly "${columns.join(',')}" (got "${header.join(',')}")`);
  }
  return rows;
}

const csvField = (v) => (/[",\r\n]/.test(v) ? `"${v.replace(/"/g, '""')}"` : v);

export function writeCsv(file, columns, rows) {
  const lines = [columns.join(','), ...rows.map((r) => columns.map((c) => csvField(String(r[c] ?? ''))).join(','))];
  writeFileSync(file, lines.join('\n') + '\n');
}

// Keeps diffs stable: locale, then slug or path.
export const sortPosts = (rows) => rows.sort((a, b) => a.locale.localeCompare(b.locale) || a.slug.localeCompare(b.slug));
export const sortCategories = (rows) => rows.sort((a, b) => a.locale.localeCompare(b.locale) || a.path.localeCompare(b.path));

// ---------------------------------------------------------------------------
// Validation

export function validatePosts(rows) {
  const errors = [];
  const warnings = [];
  const seenKey = new Map();
  const seenId = new Map();
  const seenTarget = new Map();
  for (const r of rows) {
    const where = `posts.csv line ${r.line} (${r.locale}/${r.slug})`;
    const err = (msg) => errors.push(`${where}: ${msg}`);
    if (!LOCALES.includes(r.locale)) err(`unknown locale "${r.locale}" (one of ${LOCALES.join(', ')})`);
    if (!SLUG.test(r.slug)) err('slug must be lowercase letters, digits, "-", "_" or percent-encoding');
    if (RESERVED_SLUGS.includes(r.slug)) err(`"${r.slug}" is a reserved path segment`);
    if (!/^[1-9][0-9]*$/.test(r.wp_id)) err(`wp_id must be a WordPress post ID (got "${r.wp_id}")`);
    if (!['yes', 'no'].includes(r.active)) err(`active must be "yes" or "no" (got "${r.active}")`);
    if (r.new_url === '') {
      if (r.active === 'yes') err('active row needs a new_url');
    } else {
      const m = POST_TARGET.exec(r.new_url);
      if (!m) err(`new_url must look like https://www.firefox.com/en-US/blog/<slug>/ (got "${r.new_url}")`);
      else if (RESERVED_TARGET_SLUGS.includes(m[2])) err(`new_url can't end in /${m[2]}/ (a firefox.com blog sub-route)`);
    }
    // A translation should go to the same language on firefox.com (de -> /de/, en -> /en-US/).
    const targetLocale = /^https:\/\/www\.firefox\.com\/([a-zA-Z-]+)\//.exec(r.new_url)?.[1].toLowerCase();
    if (targetLocale && targetLocale.split('-')[0] !== r.locale.split('-')[0]) {
      warnings.push(`${where}: a "${r.locale}" post redirects to /${targetLocale}/ on firefox.com (intended?)`);
    }
    if (r.old_url !== '') {
      let u;
      try { u = new URL(r.old_url); } catch { err(`old_url is not a URL ("${r.old_url}")`); }
      if (u) {
        const segs = u.pathname.split('/').filter(Boolean);
        if (u.origin !== 'https://blog.mozilla.org') err('old_url must start with https://blog.mozilla.org/');
        if (segs[0] !== r.locale) err(`old_url must start with /${r.locale}/`);
        if (segs.at(-1) !== r.slug) err(`old_url must end in /${r.slug}/ (the slug column)`);
      }
    }
    const key = `${r.locale}/${r.slug}`;
    if (seenKey.has(key)) err(`duplicate locale + slug (also line ${seenKey.get(key)})`);
    seenKey.set(key, r.line);
    if (seenId.has(r.wp_id)) err(`duplicate wp_id ${r.wp_id} (also line ${seenId.get(r.wp_id)})`);
    seenId.set(r.wp_id, r.line);
    if (r.new_url && seenTarget.has(r.new_url)) {
      warnings.push(`${where}: same new_url as line ${seenTarget.get(r.new_url)} (fine for translations of one article)`);
    }
    if (r.new_url) seenTarget.set(r.new_url, r.line);
  }
  return { errors, warnings };
}

export function validateCategories(rows) {
  const errors = [];
  const seen = new Map();
  for (const r of rows) {
    const where = `categories.csv line ${r.line} (${r.locale}/${r.path})`;
    const err = (msg) => errors.push(`${where}: ${msg}`);
    const segs = r.path.split('/');
    if (!LOCALES.includes(r.locale)) err(`unknown locale "${r.locale}"`);
    if (!/^[a-z0-9-]+(\/[a-z0-9-]+)*$/.test(r.path)) err('path must be lowercase segments without leading or trailing "/", e.g. category/firefox/firefox-tips');
    // Only the Firefox subtree: "firefox" (the /en/firefox/ landing) or category/firefox.../...
    const top = segs[0] === 'category' ? segs[1] : segs.length === 1 ? segs[0] : undefined;
    if (!top || !top.startsWith('firefox')) err('path must be "firefox" or start with "category/firefox" (only Firefox categories redirect)');
    const owned = segs.find((s) => MOZILLA_CATEGORIES.includes(s));
    if (owned) err(`"${owned}" is a Mozilla-owned category; its archives stay on blog.mozilla.org`);
    if (segs.includes('feed') || segs.includes('page')) err('list the category path only (pages follow automatically, feeds are never redirected)');
    if (!['yes', 'no'].includes(r.active)) err(`active must be "yes" or "no" (got "${r.active}")`);
    if (r.new_url === '') {
      if (r.active === 'yes') err('active row needs a new_url');
    } else if (!CATEGORY_TARGET.test(r.new_url)) {
      err(`new_url must be https://www.firefox.com/en-US/blog/ or .../blog/topics/<topic>/ (got "${r.new_url}")`);
    }
    const key = `${r.locale}/${r.path}`;
    if (seen.has(key)) err(`duplicate locale + path (also line ${seen.get(key)})`);
    seen.set(key, r.line);
  }
  return { errors, warnings: [] };
}

// ---------------------------------------------------------------------------
// PHP lookup

const php = (s) => `'${String(s).replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`;

function group(rows, keyOf, valueOf) {
  const out = {};
  for (const r of rows) (out[r.locale] ??= []).push(`\t\t\t${php(keyOf(r))} => ${valueOf(r)},`);
  return Object.entries(out)
    .map(([locale, lines]) => `\t\t${php(locale)} => array(\n${lines.join('\n')}\n\t\t),`)
    .join('\n');
}

export function renderMap(posts, categories) {
  // Rows without a target can't do anything yet, so they're left out.
  const p = posts.filter((r) => r.new_url);
  const c = categories.filter((r) => r.new_url);
  const active = (r) => (r.active === 'yes' ? 'true' : 'false');
  return `<?php
/**
 * Redirect lookup for inc/redirects.php. GENERATED by scripts/redirects.mjs
 * from redirects/posts.csv and redirects/categories.csv: don't edit, and
 * don't commit (npm run build writes it).
 *
 * posts:      locale => slug => array( WordPress post ID, new URL, active )
 * categories: locale => path => array( new URL, active )
 */

return array(
\t'posts'      => array(
${group(p, (r) => r.slug, (r) => `array( ${Number(r.wp_id)}, ${php(r.new_url)}, ${active(r)} )`)}
\t),
\t'categories' => array(
${group(c, (r) => r.path, (r) => `array( ${php(r.new_url)}, ${active(r)} )`)}
\t),
);
`;
}

export function loadAndValidate() {
  const posts = readCsv(POSTS_CSV, POST_COLUMNS);
  const categories = readCsv(CATEGORIES_CSV, CATEGORY_COLUMNS);
  const a = validatePosts(posts);
  const b = validateCategories(categories);
  return { posts, categories, errors: [...a.errors, ...b.errors], warnings: [...a.warnings, ...b.warnings] };
}

// Validates and writes inc/redirects-map.php. Throws on bad data.
export function generate() {
  const { posts, categories, errors, warnings } = loadAndValidate();
  for (const w of warnings) console.warn(`  warning: ${w}`);
  if (errors.length) {
    throw new Error(`redirects: ${errors.length} problem(s) in redirects/*.csv:\n  ${errors.join('\n  ')}`);
  }
  writeFileSync(MAP_PHP, renderMap(posts, categories));
  const count = (rows) => `${rows.length} (${rows.filter((r) => r.active === 'yes').length} active)`;
  return `inc/redirects-map.php (posts ${count(posts)}, categories ${count(categories)})`;
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
  try {
    console.log(`Wrote ${generate()}`);
  } catch (err) {
    console.error(err.message);
    process.exit(1);
  }
}
