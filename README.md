# Foxtail

The WordPress theme for [blog.mozilla.org](https://blog.mozilla.org), built on [Mozilla Protocol](https://protocol.mozilla.org).

## Where it runs

- **Host:** WP Engine. Environments: Production, Staging, Development.
- **The WP Engine install is a WordPress multisite network.** It also serves hacks.mozilla.org, blog.thunderbird.net, blog.nightly.mozilla.org and about 90 other sites. blog.mozilla.org is site 278 (tables `wp_278_*`, media in `wp-content/blogs.dir/278/files/`).
- **Foxtail is only used by blog.mozilla.org.** Must-use plugins in `wp-content/mu-plugins/` (such as `mozilla-custom`) run on every site in the network.
- Main plugins on blog.mozilla.org: ACF Pro, WPML, Yoast SEO, Co-Authors Plus.

## Branches and environments

| Branch | Purpose | Deploys to |
|---|---|---|
| `master` | Reviewed code, the source of truth | Nothing |
| `develop` | Trying changes | Development, on push |
| `stage` | Rehearsals | Staging, on push |
| `prod` | What is live | Production, on push, after approval |

### Workflow

1. **Try a change:** create a branch from `master`, merge it into `develop`, and check it on Development.
2. **Accept it:** open a pull request from your branch into `master`.
3. **Rehearse (optional):** open a pull request from `master` into `stage`, and check Staging.
4. **Ship:** open a pull request from `master` into `prod`. Merging it starts the Production deploy, which waits for approval (see below).

**Merge methods:** squash is fine for pull requests into `master`. Pull requests from `master` into `stage` or `prod` must use **Create a merge commit**: a squash creates a new commit that `master` doesn't have, so the branches drift apart and later promotions show old changes again or conflict. The `prod` ruleset only allows merge commits.

**Urgent fix:** branch from `master`, open a pull request into `master`, then ship it. Afterwards, merge `master` into `stage` and `develop` so they stay current.

`develop` is scratch space. It can be reset to `master` when it fills up with experiments.

### Branches and protections

- `master` and `prod` only change through pull requests, and the checks below must pass.
- Pull requests into `prod` must come from `master` (the "Prod changes come from master" check).
- Every pull request runs a PHP syntax check (PHP 7.4 and 8.2).
- Production deploys wait for a reviewer to approve them in the GitHub `production` environment.

## Deploying

Deploys are done by the **Deploy theme** GitHub workflow (`.github/workflows/deploy.yml`), using [WP Engine's GitHub Action](https://github.com/wpengine/github-action-wpe-site-deploy).

- **Automatic:** pushing to `develop`, `stage` or `prod` deploys that branch to its environment.
- **Manual redeploy:** Actions → Deploy theme → Run workflow, then pick the branch under "Use workflow from". Use this after copying an environment (for example prod to Development), which overwrites the theme files there.
- **Production:** the run pauses until a reviewer clicks **Review deployments → Approve** on the run's page.

What a deploy does:

- Runs `npm run build`, which assembles `build/foxtail/`: the theme without build tooling, sources, docs and dotfiles (see `.deployignore`).
- Copies `build/foxtail/` into `wp-content/themes/foxtail/` on the chosen environment, and nothing outside that folder.
- Deletes files in that folder that aren't in `build/foxtail/`.
- Clears the WP Engine page cache when it's done.

### Deploying by hand

If the workflow can't run, deploy the same folder over SFTP:

1. Check out the commit to deploy and run `npm ci --ignore-scripts && npm run build`.
2. Upload `build/foxtail/` to `wp-content/themes/` on the environment as `foxtail-new` (use one SFTP connection; WP Engine blocks IPs that open too many).
3. Rename `foxtail` to `foxtail-old`, then immediately `foxtail-new` to `foxtail`. Don't delete the old folder first: while the active theme's folder is missing, WordPress can switch the site to a default theme.
4. Clear the cache in the WP Engine portal. Delete `foxtail-old` once the site checks out; renaming it back is the rollback.

**Rolling back:** revert the change on `master`, then ship it to `prod` as normal. For anything urgent, WP Engine backups (User Portal → Backups) restore an environment, but a full restore also rolls back the database.

### One-time setup

These steps need admin access to the GitHub repository and the WP Engine User Portal. Never share keys or other secret values in issues, chat or pull requests.

1. **Create a deploy key** on your own machine, with no passphrase, used only for this:

   ```bash
   ssh-keygen -t ed25519 -C "foxtail-github-deploy" -f ~/.ssh/foxtail_wpe_deploy
   ```

2. **Add the public key to WP Engine.** In the User Portal, open your profile → **SSH keys** (not a site's GitPush page) and add the contents of `~/.ssh/foxtail_wpe_deploy.pub`. Paste the key itself (it starts with `ssh-ed25519`), not its `SHA256:` fingerprint. The key can reach every install its WP Engine user can reach, so prefer a dedicated portal user that only has access to this install.
3. **Add the private key to GitHub.** Repository → Settings → Secrets and variables → Actions → New repository secret. Name: `WPE_SSHG_KEY_PRIVATE`. Value: the contents of `~/.ssh/foxtail_wpe_deploy` (the file without `.pub`). After that, delete the private key from your machine or keep it in a password manager.
4. **Create three GitHub environments** (Settings → Environments): `development`, `staging` and `production`. In each one, add a variable named `WPE_ENV` set to that environment's WP Engine install name, as shown under Sites in the User Portal (for example `mozilladev` for Development).
5. **Protect production.** In the `production` environment:
   - Deployment branches and tags: Selected branches → `prod` only.
   - Required reviewers: add the people who approve production deploys.
6. **Allow the WP Engine action.** The repository only runs approved actions. In Settings → Actions → General, add `wpengine/github-action-wpe-site-deploy@<commit>` using the commit pinned in `.github/workflows/deploy.yml`. If that setting is locked, ask the Mozilla GitHub org admins. When upgrading the action, update the pin and the allowed entry together.
7. **Protect branches** (Settings → Rules → Rulesets, or Branches):
   - `master`: require a pull request and the "PHP lint" checks; block force pushes and deletion.
   - `prod`: the same, plus the "Prod changes come from master" check.

## Local development

1. **Get a copy of the site.** In [Local](https://localwp.com), use WP Engine Connect to pull the **Development** environment, never Production. In the MagicSync file list, untick `wp-content/blogs.dir` and `wp-content/uploads` (tens of GB of media), except recent months if you want images. Include the database.
2. **Run blog.mozilla.org as a single site.** The pulled database is the whole multisite network, and Local's `wp-config.php` points at the network's first site. In the local site's `wp-config.php`, set:

   ```php
   $table_prefix = 'wp_278_';
   define( 'CUSTOM_USER_TABLE', 'wp_users' );
   define( 'CUSTOM_USER_META_TABLE', 'wp_usermeta' );
   define( 'WP_HOME', 'http://yoursite.local' );
   define( 'WP_SITEURL', 'http://yoursite.local' );
   define( 'DISABLE_WP_CRON', true );
   ```

   Then, in Local's Site shell, replace the Development URLs:

   ```bash
   wp search-replace 'https://mozilladev.wpengine.com' 'http://yoursite.local' --all-tables-with-prefix
   wp search-replace 'mozilladev.wpengine.com' 'yoursite.local' --all-tables-with-prefix
   ```

   Network-activated plugins (single sign-on, Duo, Post SMTP, domain mapping) don't load in this setup, so the normal WordPress login works. Use `wp user update <you> --prompt=user_pass` to set a local-only password.
3. **Turn off anything that could reach the outside world:** `wp plugin deactivate post-smtp wp-all-export-pro wp-all-import-pro`. Local catches other mail in Mailpit.
4. **Build the CSS and JS** in this repo (see [Building CSS and JS](#building-css-and-js)).
5. **Point the local theme at this repo:**

   ```bash
   mv "<local site>/app/public/wp-content/themes/foxtail" "<local site>/app/public/wp-content/themes/foxtail.pulled"
   ln -s ~/path/to/this/repo "<local site>/app/public/wp-content/themes/foxtail"
   ```

   The local site now runs whatever branch is checked out.
6. **Never push from Local** to WP Engine. Before pulling again, remove the `foxtail` symlink first, or Local will write the server's files into this repo.

Expected local differences: `/` is blank (production redirects it to `/en/` before WordPress runs), so start at `/en/`. Older images are missing if you skipped media.

## Building CSS and JS

The compiled CSS and JS are **not in git**. CI builds them for every deploy, and you build them locally:

```bash
nvm use                       # Node version from .nvmrc
npm ci --ignore-scripts       # install the 4 build tools, without running package install scripts
npm run build                 # build once
npm run watch                 # rebuild when SCSS or JS changes
```

`npm run build` also assembles `build/foxtail/`, the theme exactly as it is deployed. `build/` is ignored by git.

| Source | Output |
|---|---|
| `assets/sass/main.scss`, `assets/sass/editor.scss` | `assets/css/main.min.css`, `assets/css/editor.min.css` |
| `assets/js/custom/*.js` | `assets/js/custom.min.js` |
| `assets/js/vendor/**/*.js` | `assets/js/vendor.min.js` |

How it works (`scripts/build.mjs`):

- **CSS:** Dart Sass compiles the SCSS, then Lightning CSS adds vendor prefixes for the browsers in `package.json` → `browserslist` and minifies.
- **JS:** the files in each folder are joined in alphabetical order and minified with esbuild. They are plain scripts, not modules, so order matters.
- `assets/vendor/protocol/` is a fixed copy of Mozilla Protocol's SCSS, images and fonts (an old version, not the npm package). It has small Dart Sass compatibility patches, each marked `Foxtail patch`.
- `assets/sass/_customizer.scss` holds the rules that used to live in Appearance → Customize → Additional CSS. Keep it imported last.

The build tools are pinned to exact versions in `package.json`. When updating them, rebuild and compare the output before shipping.

## Versioning

The theme version (`Version` in `style.css`, and `version` in `package.json`) is a release label. Bump it once per Production release, in the pull request that prepares the release, not in every feature pull request: patch for fixes (1.0.1), minor for features (1.1.0).

Browser and CDN caches don't depend on it: `functions.php` adds each CSS and JS file's modification time as its `?ver=`, so every deploy serves the new files.

## WPML outage fallback

`blog.mozilla.org/` redirects to `/en/`, and browsers cache that redirect. `/en/` only exists while WPML is running, so if WPML fails, visitors land on a 404.

To serve the homepage at `/en/` during an outage:

1. In WP Admin → Pages, publish the draft page "Mozilla Blog - Homepage (EN)" (slug `en`). WordPress renders it with `page-en.php`, which loads the normal homepage template.
2. When WPML is working again, set the page back to draft.

## Housekeeping

After the theme with `_customizer.scss` is live, empty Appearance → Customize → Additional CSS on production.
