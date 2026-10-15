// Refreshes redirects/posts.csv from springfield's blog-inventory.csv. See
// "Redirects" in README.md.
//
//   node scripts/redirects-import.mjs <blog-inventory.csv>            report only
//   node scripts/redirects-import.mjs <blog-inventory.csv> --write    update posts.csv
//   node scripts/redirects-import.mjs --activate-live [--write]       turn on rows whose
//                                                                     firefox.com page is live
//
// Rows are keyed by WordPress post ID (wp_id). The import:
//   - adds newly migrated posts with active=no;
//   - updates slug and old_url when a permalink moved (keeps active);
//   - updates new_url when the target changed, and sets active=no until it's
//     checked again;
//   - never deletes rows: posts that left the inventory (unpublished on
//     WordPress) or are no longer marked migrated are listed for review.
// Review the result with `git diff redirects/posts.csv`.

import { readFileSync } from 'node:fs';
import {
  POSTS_CSV, POST_COLUMNS, parseCsv, readCsv, sortPosts, validatePosts, writeCsv,
} from './redirects.mjs';

const args = process.argv.slice(2);
const write = args.includes('--write');
const activateLive = args.includes('--activate-live');
const inventoryFile = args.find((a) => !a.startsWith('--'));

if (!inventoryFile && !activateLive) {
  console.error('Usage: node scripts/redirects-import.mjs <blog-inventory.csv> [--write] [--activate-live]');
  process.exit(2);
}

const today = new Date().toISOString().slice(0, 10);
const rows = readCsv(POSTS_CSV, POST_COLUMNS);
const byId = new Map(rows.map((r) => [r.wp_id, r]));
const report = { added: [], moved: [], retargeted: [], notMigrated: [], missing: [], activated: [] };

if (inventoryFile) {
  const { header, rows: inventory } = parseCsv(readFileSync(inventoryFile, 'utf8'));
  for (const col of ['old_url', 'locale', 'slug', 'wp_id', 'migrated', 'new_url']) {
    if (!header.includes(col)) throw new Error(`${inventoryFile}: missing column "${col}"`);
  }
  const inv = new Map(inventory.map((r) => [r.wp_id, r]));

  for (const src of inventory) {
    if (src.migrated !== 'yes') continue;
    const row = byId.get(src.wp_id);
    if (!row) {
      const added = { locale: src.locale, slug: src.slug, wp_id: src.wp_id, active: 'no', new_url: src.new_url, old_url: src.old_url, note: `imported ${today}` };
      rows.push(added);
      byId.set(src.wp_id, added);
      report.added.push(`${src.locale}/${src.slug} -> ${src.new_url || '(no new_url yet)'}`);
      continue;
    }
    if (row.slug !== src.slug || row.old_url !== src.old_url || row.locale !== src.locale) {
      report.moved.push(`${row.wp_id}: ${row.old_url} -> ${src.old_url}`);
      Object.assign(row, { locale: src.locale, slug: src.slug, old_url: src.old_url });
    }
    if (src.new_url && row.new_url !== src.new_url) {
      report.retargeted.push(`${row.locale}/${row.slug}: ${row.new_url || '(none)'} -> ${src.new_url} (now active=no)`);
      Object.assign(row, { new_url: src.new_url, active: 'no' });
    }
  }
  for (const row of rows) {
    const src = inv.get(row.wp_id);
    if (!src) report.missing.push(`${row.locale}/${row.slug} (wp_id ${row.wp_id}, active=${row.active})`);
    else if (src.migrated !== 'yes') report.notMigrated.push(`${row.locale}/${row.slug} (wp_id ${row.wp_id}, active=${row.active})`);
  }
}

if (activateLive) {
  // A target counts as live when it answers 200 without redirecting.
  for (const row of rows.filter((r) => r.active === 'no' && r.new_url)) {
    const res = await fetch(row.new_url, { method: 'HEAD', redirect: 'manual' }).catch(() => null);
    if (res?.status === 200) {
      row.active = 'yes';
      report.activated.push(`${row.locale}/${row.slug} -> ${row.new_url}`);
    }
  }
}

const sections = [
  ['Added (active=no)', report.added],
  ['Permalink moved (slug/old_url updated)', report.moved],
  ['Target changed (set to active=no; check the new target)', report.retargeted],
  ['Activated (firefox.com returned 200)', report.activated],
  ['In posts.csv but no longer migrated=yes in the inventory (review; kept)', report.notMigrated],
  ['In posts.csv but not in the inventory, e.g. unpublished on WordPress (kept: its redirect still applies)', report.missing],
];
for (const [title, items] of sections) {
  console.log(`${title}: ${items.length}`);
  for (const item of items) console.log(`  ${item}`);
}

// Line numbers as they will be in the written file.
const { errors } = validatePosts(sortPosts(rows).map((r, i) => ({ ...r, line: i + 2 })));
if (errors.length) {
  console.error(`\nThe result would not validate:\n  ${errors.join('\n  ')}`);
  process.exit(1);
}

const changed = report.added.length + report.moved.length + report.retargeted.length + report.activated.length;
if (write && changed) {
  writeCsv(POSTS_CSV, POST_COLUMNS, rows);
  console.log(`\nUpdated redirects/posts.csv. Review with: git diff redirects/posts.csv`);
} else if (changed) {
  console.log('\nNothing written. Run again with --write to update redirects/posts.csv.');
} else {
  console.log('\nredirects/posts.csv is up to date.');
}
