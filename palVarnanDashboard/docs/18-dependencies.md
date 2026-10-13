# 18 — Dependencies

## PHP dependencies — `VERIFIED`

`composer.json` (77 lines). `composer.lock` present, 9,478 lines, **373 locked packages**.

### Runtime (`require`)

| Package | Constraint | Assessment |
| --- | --- | --- |
| `php` | `^7.3\|^8.0` | **7.3 is EOL (Dec 2021).** The range permits an unsupported PHP. |
| `laravel/framework` | `^8.65` | **Laravel 8 reached end of life 2022-01-18.** No security patches. |
| `laravel/sanctum` | `^2.11` | Personal access tokens. One API route only. |
| `laravel/ui` | `^3.3` | Scaffolding helper. Rarely needed at runtime. |
| `laravel/tinker` | `^2.5` | **In `require`, not `require-dev`.** Ships the REPL to production. |
| `barryvdh/laravel-debugbar` | **`*`** | See below. **Unpinned wildcard in production requirements.** |
| `barryvdh/laravel-dompdf` | `^2.2` | PDF. Config weakened — see [15-file-handling.md](15-file-handling.md). |
| `fruitcake/laravel-cors` | `^2.0` | Replaced by `laravel/framework`'s built-in CORS in L8. Superseded. |
| `guzzlehttp/guzzle` | `^7.0.1` | Redundant — `illuminate/http` already requires Guzzle. |
| `maatwebsite/excel` | `^3.1` | Import/export. Powers the ZIP-upload bulk import. |
| `spatie/laravel-permission` | `6` | RBAC. Middleware aliases **never registered**. |

### Dev (`require-dev`)

| Package | Constraint | Notes |
| --- | --- | --- |
| `phpunit/phpunit` | `^9.5.10` | — |
| `fakerphp/faker` | `^1.9.1` | Only 2 factories exist. |
| `mockery/mockery` | `^1.4.4` | Scaffold tests only. |
| `nunomaduro/collision` | `^5.10` | — |
| `facade/ignition` | `^2.5` | Ignition error page. **Shows full stack traces + `.env` when `APP_DEBUG=true`.** |
| `laravel/sail` | `^1.0.1` | Docker dev env. **No Docker artifacts exist** in the project. |
| `laravel/telescope` | `^4.14` | `composer.json` `extra.laravel.dont-discover` **disables it**. Dead dependency. |
| `kitloong/laravel-migrations-generator` | `^6.10` | Explains `schema_output.txt` and `tmp_schema_check.php` in the repo root. |

`VERIFIED` — `"minimum-stability": "dev"` with `"prefer-stable": true`. `dev` stability admits
unstable transitive releases; `prefer-stable` mitigates but does not eliminate it.

### `composer.json` identity is still the skeleton — `INCOMPLETE`

```json
"name": "laravel/laravel",
"description": "The Laravel Framework.",
"keywords": ["framework", "laravel"],
"license": "MIT"
```

Nothing identifies the product, its owner, or its purpose. `composer.lock` is the only accurate
record of what is actually installed.

## `barryvdh/laravel-debugbar: "*"` — `BROKEN`

`VERIFIED` — a **wildcard constraint in production `require`**. Two consequences:

1. Any `composer update` may pull a version whose PHP or Laravel requirements are incompatible
   with this codebase, with no upper bound.
2. `barryvdh/laravel-debugbar` ships an `/debugbar` route that exposes the full request, all
   executed queries with bindings, loaded config, and session data.

`VERIFIED` — mitigation exists but is partial:

- `.env`: `DEBUGBAR_ENABLED=false`.
- `config/app.php`: `'aliases'` does **not** include `Debugbar`.

`UNVERIFIED` — whether `Debugbar::enable()` is called anywhere, and whether
`barryvdh/laravel-debugbar` respects `DEBUGBAR_ENABLED` without the facade alias (it reads the
env var through its own service provider). **Must be confirmed on the running application.**
If debugbar is live, SEC-13 applies.

## Frontend dependencies — `VERIFIED`

`package.json` (74 lines), `package.json` name `symox`, version `1.2.0`, author `MilkVandi`.
**`node_modules/` is not installed.** Both `package-lock.json` and `yarn.lock` are committed.

`BROKEN` — **three conflicting install manifests** (`package.json`, `package-lock.json`,
`yarn.lock`) with no `packageManager` field and no documented policy. Two developers on npm and
yarn will resolve different dependency trees from the same commit.

### `admin-resources` pinned to a moving git branch — `BROKEN`

```json
"admin-resources": "git+https://github.com/MilkVandi/admin-resources#master"
```

A dependency tracking the **mutable `master` branch**. `npm install` at different times yields
different code with the same lockfile. There is no tag, no commit SHA, and no integrity guarantee.
A compromised or force-pushed upstream branch executes in this application's browser context.

### Duplicated / legacy packages

`VERIFIED` — the `dependencies` block (38 entries) is a copy-paste of the Symox admin template's
full bundle. Most appear unused:

| Package | Version | Assessment |
| --- | --- | --- |
| `axios` | `^0.21` | **Two years behind.** Last release Mar 2021; several CVEs. Should be `^1.x`. |
| `moment` | `^2.29.4` | Legacy; 300 KB locale bundle. `dayjs` is the modern substitute. |
| `flot-charts` | `^0.8.3` | **Abandoned since 2015** and depends on jQuery. |
| `flot.curvedlines` | `^1.1.1` | Plugin for the abandoned package above. |
| `gmaps` | `^0.4.25` | Last release 2016. |
| `rtlcss` | `^3.0.0` | **Declared in both `devDependencies` (line 31) and `dependencies` (line 66).** |
| `gulp-rtlcss` | `^2.0.0` | **Gulp**-based, inside a webpack/Laravel Mix project. Legacy. |
| `popper.js` | `^1.12` | v1, while `@popperjs/core ^2.10.2` is also declared. Redundant. |
| `source-map` | `^0.7.4` | In runtime deps, should be dev. |
| `webpack-rtl-plugin` | `^2.0.0` | Exists only to support the RTL pipeline. |
| `ckeditor5` | `^27.0.0` | v27 (Feb 2021). Current is v40+. |
| `tinymce` | `^5.6.2` | **Two rich-text editors** (CKEditor 5 and TinyMCE 5) are both installed. |
| `quill` | `^1.3.7` | **A third** editor library. |
| `sweetalert2` | `^11.0.18` | Used for alerts — appropriate. |
| `bootstrap` | `^5.2.0` | Correct, in devDependencies because Mix compiles SCSS. |
| `sass` | `^1.49.9` | Dart Sass, correct for Bootstrap 5. |
| `laravel-mix` | `^6.0.49` | Mix 6 is the current LTS line. Appropriate. |
| `apexcharts` / `chart.js` | `^3.27.1` / `^3.5.1` | **Two charting libraries.** |
| `leaflet` | `^1.6.0` | v1.6 is old; v1.9.x is current. |
| `swiper` | `^6.5.6` | v6; current is v11. |
| `dropzone` | `^5.5.1` | v5; current is v6. |

`VERIFIED` — **34 distinct JS libraries** are shipped for what is a server-rendered Blade
application. No `webpack.mix.js` purge or tree-shaking configuration was observed that would
drop the unused ones; `public/` is 7.5 MB.

`UNVERIFIED` — which libraries the Blade templates actually import. The list above is a
candidate removal set, not a confirmed-unused set.

### `browserslist`

```json
"browserslist": ["last 2 version", "> 2%"]
```

`VERIFIED` — acceptable, though it produces a wide autoprefixer matrix. Add `not dead` to avoid
targets that no longer receive updates.

## Dependency risks ranked

| Rank | Package | Risk | Action |
| --- | --- | --- | --- |
| 1 | `laravel/framework ^8.65` | EOL — **no security patches for 4+ years** | Plan a Laravel 10/11/12 migration |
| 2 | `barryvdh/laravel-debugbar *` | Wildcard version + debug endpoint | Pin `^3.6`, move to `require-dev` |
| 3 | `admin-resources#master` | Mutable upstream code in the browser | Pin to a commit SHA, or vendor the SCSS/JS |
| 4 | `flot-charts`, `gmaps` | Abandoned, jQuery-era, possible known vulns | Audit usage, remove |
| 5 | `axios ^0.21` | Known CVEs | Upgrade to `^1.x` |
| 6 | `facade/ignition` + `APP_DEBUG=true` | Full trace and env disclosure | `APP_DEBUG=false` |
| 7 | `laravel/tinker` in `require` | REPL available if artisan is reachable | Move to `require-dev` |
| 8 | `php ^7.3` constraint | Permits EOL runtime | Require `^8.1`+ |

## Supply-chain hygiene gaps

`VERIFIED`

| Missing | Consequence |
| --- | --- |
| No `composer audit` / `npm audit` in CI or scripts | Vulnerabilities are discovered only by chance |
| No `dependabot.yml` or `renovate.json` | No automated update PRs |
| No `.nvmrc` or `engines` field | Node version varies per developer |
| No `yarn.lock`/`package-lock.json` policy | Dual manifests, non-reproducible installs |
| No committed `node_modules` state | `npm ci` requires `package-lock.json`; with two lockfiles this is ambiguous |
| `vendor/` **is** present in the working tree | 100 MB+ of dependencies on disk; `.gitignore` covers it, so it is untracked |
| No license audit | `MIT` declared for the project; third-party license compatibility unreviewed |

## Recommendations

1. **Treat the Laravel 8 EOL as the top technical risk.** It constrains every other security
   decision and blocks PHP 8.2+.
2. Pin `barryvdh/laravel-debugbar` to `^3.6`, move it to `require-dev`, and confirm
   `DEBUGBAR_ENABLED=false` is honoured at runtime.
3. Replace `admin-resources#master` with a pinned commit SHA or vendored files.
4. Choose one lockfile; delete the other and add `"packageManager"` to `package.json`.
5. Remove the abandoned/duplicate libraries once Blade usage is confirmed: `flot-charts`,
   `flot.curvedlines`, `gmaps`, one of the three editors, one of the two chart libraries, the
   duplicate `rtlcss`, `gulp-rtlcss`, `webpack-rtl-plugin`, and `popper.js` v1.
6. Upgrade `axios` to `^1.x`.
7. Move `laravel/tinker` and `source-map` to dev dependencies.
8. Add `composer audit` and `npm audit --audit-level=high` to CI.
9. Add `dependabot.yml` for both `composer.json` and `package.json`.
10. Rewrite `composer.json` metadata (name, description, keywords) and the scaffold `README.md`.