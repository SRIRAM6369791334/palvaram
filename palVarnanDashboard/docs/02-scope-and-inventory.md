# 02 — Scope and Inventory

Audit target: `C:\xampp\htdocs\pauls\palVarnanDashboard`.

## Repository state

`VERIFIED` — `git log --oneline` returns a single commit:

```
b156a59 Initial commit of palvaram project
```

`VERIFIED` — `git status --porcelain` shows a large volume of uncommitted modifications spanning
both `palVarnan/` and `palVarnanDashboard/`, including
`palVarnanDashboard/app/Console/Kernel.php`,
`palVarnanDashboard/app/Http/Controllers/ProductOrdersController.php`,
`palVarnanDashboard/app/Models/ProductOrder.php`,
`palVarnanDashboard/config/services.php`, and many Blade views.

**Implication:** there is no clean baseline to diff against. Any remediation plan must assume the
working tree — not the last commit — is the system of record. Take a snapshot before changing
anything.

## Source file counts

`VERIFIED`

| Area | Count |
| --- | --- |
| Controllers (`app/Http/Controllers`, recursive) | 84 |
| Eloquent models (`app/Models`) | 63 |
| Blade views (`resources/views`, recursive) | 197 |
| Migrations (`database/migrations`) | 60 |
| Services (`app/Services`) | 1 |
| Middleware (`app/Http/Middleware`) | 9 |
| Service providers (`app/Providers`) | 6 |
| Config files (`config`) | 19 |
| Test files (`tests`) | 4 |
| Seeders (`database/seeders`) | 2 |
| Factories (`database/factories`) | 1 |
| Console commands | 1 (`SyncShiprocketStatus.php`) |

## Disk footprint

`VERIFIED`

| Directory | Size |
| --- | --- |
| `public/` | 229.40 MB |
| `vendor/` | 56.89 MB |
| `resources/` | 46.19 MB |
| `storage/` | 6.03 MB |
| `app/` | 0.51 MB |
| `database/` | 0.06 MB |
| `config/` | 0.08 MB |
| `routes/` | 0.04 MB |
| `bootstrap/` | 0.02 MB |

`public/` at 229 MB is dominated by compiled template assets and copied fonts/images.
`resources/` at 46 MB contains template source and vendored `resources/libs` payloads.

## Loosely tracked / debug artifacts at project root

`VERIFIED` — the following runnable PHP files sit in the web project root. Under XAMPP/Apache this
directory is *not* the document root (`public/` is), so they are not directly web-reachable, but
they are committed to git, can be executed via CLI, and leak schema/credential details if the
document root is ever misconfigured.

| File | Concern |
| --- | --- |
| `check_db_raw.php` | Ad-hoc database introspection. |
| `check_db_schema.php` | Ad-hoc database introspection. |
| `debug_query.php` | Ad-hoc query execution. |
| `dump_order.php` | Order data dumping. |
| `seed_promotions.php` | Data seeding outside `database/seeders`. |
| `test.php` | Ad-hoc script. |
| `tmp_schema_check.php` | Ad-hoc schema check. |
| `schema_output.txt` | Committed database schema dump. |

## Existing documentation

`VERIFIED` — `README.md` is the **unmodified Laravel 8 framework README**. It contains no project
information: no setup steps, no description of the domain, no deployment notes. Everything a new
developer needs is in this `docs/` directory.

`VERIFIED` — `.styleci.yml` and `.editorconfig` are present. `.gitattributes` is present.

## Out of scope

Present in the repository parent `C:\xampp\htdocs\pauls` and **not audited**:

- `palVarnan/` — separate customer/mobile application (has uncommitted changes).
- `palVarnan1/` — appears to be an older copy.
- `marketpro-ecommerce-multivendor-html-template-2026-01-10-16-20-47-utc/` — static HTML template.
- `yesbe_dash/` — empty directory (also present inside the dashboard project).
- `palvarnan_db (2).sql` — full database dump, used only as a schema-drift reference.
- ZIP archives of the above.

`UNVERIFIED` — whether `palVarnanDashboard` is the correct audit boundary, and whether `palVarnan/`
and `palVarnanDashboard/` are deployed together as one system. Confirm before relying on this
documentation for a cross-application change. See [30-open-questions.md](30-open-questions.md).