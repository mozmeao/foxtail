// Requests old blog.mozilla.org URLs on an environment and checks what the
// theme's redirects do with them. See "Redirects" in README.md.
//
//   node scripts/redirects-check.mjs <base URL> [--all] [--status 301]
//   node scripts/redirects-check.mjs --targets
//
//   <base URL>   e.g. http://distilled.local or https://mozilladev.wpengine.com
//   --all        every row in redirects/*.csv (default: a sample of up to 5
//                active and 5 inactive posts, plus all categories)
//   --status N   the redirect status to expect (default 302; set in WP Admin ->
//                Tools -> Firefox redirects)
//   --targets    instead: check that every new_url answers 200 on firefox.com,
//                and list inactive rows that could be switched on
//
// Password-protected environments: set CHECK_BASIC_AUTH=user:password in your
// own shell first. The script only reads it from the environment.
//
// For each post it tries the old permalink and the other paths WordPress
// accepts (another category path, no language directory, /en/products/...,
// /blog/YYYY/MM/DD/..., the comment feed, a utm_ link). Active rows must
// end at new_url, redirected by the theme; inactive rows must not redirect, and must
// carry the X-Foxtail-Redirect: inactive header (so the matching is proven).
// Redirects on the same site that come before the theme's (WP Engine or
// WordPress sending an old form to the permalink) are followed first, and the
// result shows the number of steps.
// It also checks URLs that must never redirect (Mozilla categories, tags,
// authors, dates, search, site-wide and Firefox category feeds, posts that
// didn't move).
//
// The site-wide switch (Tools -> Firefox redirects) must be on for active
// rows to pass; when it's off they report "switched off".

import {
  CATEGORIES_CSV, CATEGORY_COLUMNS, POSTS_CSV, POST_COLUMNS, readCsv,
} from './redirects.mjs';

const args = process.argv.slice(2);
const flag = (name) => args.includes(name);
const option = (name, fallback) => {
  const i = args.indexOf(name);
  return i >= 0 ? args[i + 1] : fallback;
};

const posts = readCsv(POSTS_CSV, POST_COLUMNS).filter((r) => r.new_url);
const categories = readCsv(CATEGORIES_CSV, CATEGORY_COLUMNS);

if (flag('--targets')) {
  let bad = 0;
  const rows = [...posts, ...categories.filter((r) => r.new_url)];
  for (const r of rows) {
    const res = await fetch(r.new_url, { method: 'HEAD', redirect: 'manual' }).catch((e) => ({ status: e.message }));
    const live = res.status === 200;
    const label = `${r.locale}/${r.slug ?? r.path}`;
    if (r.active === 'yes' && !live) { bad++; console.log(`FAIL  active, but ${r.new_url} answers ${res.status}  (${label})`); }
    else if (r.active === 'no' && live) console.log(`READY inactive, ${r.new_url} is live: can be switched on  (${label})`);
    else if (flag('--verbose')) console.log(`ok    ${label}: ${res.status}, active=${r.active}`);
  }
  const live = rows.filter((r) => r.active === 'yes').length;
  console.log(`\n${rows.length} targets checked, ${live} rows active, ${bad} active target(s) not live.`);
  process.exit(bad ? 1 : 0);
}

const base = args.find((a) => !a.startsWith('--') && /^https?:\/\//.test(a))?.replace(/\/$/, '');
if (!base) {
  console.error('Usage: node scripts/redirects-check.mjs <base URL> [--all] [--status 301] | --targets');
  process.exit(2);
}
const expectStatus = Number(option('--status', '302'));
if (![301, 302].includes(expectStatus)) {
  console.error('--status must be 301 or 302');
  process.exit(2);
}
const headers = { 'User-Agent': 'foxtail-redirects-check' };
if (process.env.CHECK_BASIC_AUTH) headers.Authorization = `Basic ${Buffer.from(process.env.CHECK_BASIC_AUTH).toString('base64')}`;

const sample = (rows, n) => (flag('--all') ? rows : rows.slice(0, n));
const active = sample(posts.filter((r) => r.active === 'yes'), 5);
const inactive = sample(posts.filter((r) => r.active === 'no'), 5);

// Path forms WordPress accepts for a post with permalink /<locale>/<category path>/<slug>/.
function postPaths(r) {
  const permalink = new URL(r.old_url).pathname;
  const other = permalink.includes('/mozilla/') ? 'firefox' : 'mozilla';
  const forms = [
    ['permalink', permalink],
    ['other category path', `/${r.locale}/${other}/${r.slug}/`],
    ['comment feed', `${permalink}feed/`],
    ['utm link', `${permalink}?utm_source=redirects-check&utm_medium=test&fbclid=drop-me`],
  ];
  if (r.locale === 'en') {
    forms.push(
      ['no language directory', `/firefox/${r.slug}/`],
      ['legacy /en/products/firefox/', `/en/products/firefox/${r.slug}/`],
      ['legacy /blog/YYYY/MM/DD/', `/blog/2020/01/01/${r.slug}/`],
    );
  }
  return forms;
}

const checks = [];
for (const r of [...active, ...inactive]) {
  for (const [form, path] of postPaths(r)) {
    const want = r.active === 'yes'
      ? r.new_url + (path.includes('utm_') ? '?utm_source=redirects-check&utm_medium=test' : '')
      : null;
    checks.push({ label: `${r.slug} (${form})`, path, want, inactiveUrl: r.active === 'no' ? r.new_url : null });
  }
}
for (const r of sample(categories, Infinity)) {
  const path = r.path === 'firefox' ? `/${r.locale}/firefox/` : `/${r.locale}/${r.path}/`;
  const live = r.active === 'yes' && r.new_url;
  const inactiveUrl = live ? null : r.new_url || null;
  checks.push({ label: `category ${r.path}`, path, want: live ? r.new_url : null, inactiveUrl });
  // WordPress only serves pages and feeds under /category/ (/en/firefox/page/2/ is a 404).
  if (r.path.startsWith('category/')) {
    checks.push({ label: `category ${r.path} page 2`, path: `${path}page/2/`, want: live ? r.new_url : null, inactiveUrl, optional: true });
    checks.push({ label: `category ${r.path} feed (stays)`, path: `${path}feed/`, want: null });
  }
}

// The same slug under /de/ and /fr/ (where translations live) only redirects
// if that language has its own row. Checked for the sampled en rows.
for (const r of [...active, ...inactive].filter((p) => p.locale === 'en')) {
  for (const [locale, dir] of [['de', 'firefox-de'], ['fr', 'firefox-fr']]) {
    if (!posts.some((p) => p.locale === locale && p.slug === r.slug)) {
      checks.push({ label: `no ${locale} row: ${locale} path of ${r.slug}`, path: `/${locale}/${dir}/${r.slug}/`, want: null });
    }
  }
}

// Must never redirect, whatever the CSVs say.
for (const path of [
  '/en/', '/en/feed/', '/en/comments/feed/', '/en/latest/', '/en/2025/08/', '/en/?s=firefox',
  '/en/category/mozilla/news/', '/en/category/mozilla/internet-policy/', '/en/tag/firefox/',
  '/en/tag/firefox/feed/', '/en/category/firefox/feed/',
]) checks.push({ label: `stays: ${path}`, path, want: null });

// A few requests at a time: much faster than one by one, still gentle on the host.
// Requests a URL. For rows (active or inactive) it follows redirects that stay
// on this site and aren't the theme's (WP Engine, WPML or WordPress itself
// sending an old form to the permalink), so the theme's answer is what gets
// judged. URLs that must not redirect are judged on the first response.
async function fetchFollowing(path, follow) {
  let url = base + path;
  let hops = 0;
  for (;;) {
    const res = await fetch(url, { method: 'GET', redirect: 'manual', headers });
    const location = res.headers.get('location');
    const by = res.headers.get('x-redirect-by');
    const next = location ? new URL(location, url) : null;
    if (!follow || hops >= 5 || by === 'Foxtail' || res.status < 300 || res.status >= 400 || !next || next.origin !== new URL(base).origin) {
      return { res, location: next ? next.href : null, by, marker: res.headers.get('x-foxtail-redirect'), hops, url };
    }
    url = next.href;
    hops++;
  }
}

async function run(c) {
  const follow = Boolean(c.want || c.inactiveUrl);
  const r = await fetchFollowing(c.path, follow).catch((e) => ({ error: e.message }));
  if (r.error) return `FAIL  ${c.label}: ${r.error}`;
  const { res, location, by, marker, hops } = r;
  const via = hops ? ` (after ${hops} redirect${hops > 1 ? 's' : ''} on this site, from ${r.url.slice(base.length)})` : '';
  let problem = null;
  if (c.want && marker?.startsWith('off ')) {
    problem = 'redirects are switched off on this site (WP Admin -> Tools -> Firefox redirects)';
  } else if (c.want) {
    if (res.status !== expectStatus || location !== c.want || by !== 'Foxtail') {
      problem = `want ${expectStatus} -> ${c.want}, got ${res.status}${location ? ` -> ${location}` : ''}${by ? ` (by ${by})` : ''}${via}`;
    }
  } else if (by === 'Foxtail') {
    problem = `must not redirect, got ${res.status} -> ${location}${via}`;
  } else if (c.inactiveUrl && marker !== `inactive ${c.inactiveUrl}`) {
    if (!c.optional || res.status === 200) problem = `inactive row should add "X-Foxtail-Redirect: inactive ${c.inactiveUrl}", got ${res.status} ${marker ? `"${marker}"` : 'no header'}${via}`;
  }
  if (problem) return `FAIL  ${c.label}  ${c.path}\n      ${problem}`;
  return `ok    ${c.label}  ${res.status}${location ? ` -> ${location}` : ''}${marker ? `  [${marker}]` : ''}${hops ? `  (${hops + 1} steps)` : ''}`;
}

const results = new Array(checks.length);
let next = 0;
await Promise.all(Array.from({ length: 6 }, async () => {
  while (next < checks.length) {
    const i = next++;
    results[i] = await run(checks[i]);
  }
}));
for (const line of results) console.log(line);
const failed = results.filter((line) => line.startsWith('FAIL')).length;
console.log(`\n${checks.length} URLs checked on ${base}, ${failed} failed.`);
process.exit(failed ? 1 : 0);
