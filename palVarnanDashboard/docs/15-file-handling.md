# 15 — File Handling

## Filesystem configuration

`VERIFIED` — `config/filesystems.php`:

| Disk | Root | URL |
| --- | --- | --- |
| `local` | `storage_path('app')` | — |
| **`public`** | **`public_path('images/')`** | `env('APP_URL').'/storage'` |
| `s3` | configured but unused (`FILESYSTEM_DRIVER=local`) | — |

`links` declares the default `public_path('storage') => storage_path('app/public')`.

### The `public` disk points into the document root — `INCOMPLETE`

`VERIFIED` — the `public` disk root is `public_path('images/')`, **not** the Laravel default
`storage_path('app/public')`. Every `->store($folder, 'public')` call therefore writes directly into
the web-served document root at `public/images/<folder>/`.

`VERIFIED` — `public/storage` **does not exist**; `php artisan storage:link` has not been run.
The `links` entry is therefore inert and unused.

`BROKEN` — the disk's `'url' => env('APP_URL').'/storage'` is inconsistent with its root. Any code
using `Storage::disk('public')->url($path)` would emit `http://localhost/storage/<path>` for a file
actually stored at `public/images/<path>`. This is latent only: **`Storage::url()` has 0 call
sites**, and views construct image URLs manually. The wrong URL will surface the moment anyone
adopts the helper.

`VERIFIED` — `storage/app/` contains **1 file**. All media lives in `public/images/`.

## Upload destinations and current contents

`VERIFIED` — 673 files in `public/images/` across 13 subdirectories:

| Directory | Files | Written by |
| --- | --- | --- |
| `product_images1` | 323 | `ProductVarientControllet` (`product_image1` array) |
| `product_images` | 150 | `ProductController`, `GiftController` |
| `category_images` | 78 | `CategoryController`, `GiftController` |
| `subcategory_images` | 28 | `GiftController` |
| `varient_images` | 10 | `ProductVarientControllet:150`, `GiftVariantController` |
| `variant_images` | 26 | `ProductVarientControllet:279`, `GiftVariantController` |
| `brand_images` | 7 | `BrandController` |
| `banner_images` | 24 | `BannerImagesController` |
| `web_images` | 14 | `BannerImagesController` |
| `home_promotions` | 4 | `HomePromotionController` |
| `category_banners` | 1 | `CategoryController` |
| `offer_images` | 0 | `OfferImageController` |
| `notification_image` | 0 | `NotificationController` (commented out) |

`BROKEN` — `varient_images` and `variant_images` are **two directories for the same concept**, used
by the same controller on different lines (`ProductVarientControllet.php:150` vs `:279`). Products
uploaded through different code paths end up with image paths in different folders, so any
query that assumes one directory returns partial results.

`INCOMPLETE` — `public/images1/` exists as a separate top-level directory and is referenced by no
controller or model. It is an orphaned copy.

## Validation posture per upload

`VERIFIED`

### Correctly validated and stored

| Endpoint | Rule | Storage call |
| --- | --- | --- |
| `BannerImagesController::store()` | `banner_image => required\|mimes:png,jpg,webp,jpeg` | `->store('banner_images','public')` |
| `ProductController::store()` | `product_image => required\|mimes:png,jpg,webp,jpeg` | `->store('product_images','public')` |
| `ProductController::bulkUpload()` | `excel_file => required\|mimes:xlsx,xls,csv,txt`, `zip_file => nullable\|mimes:zip` | see below |

These use `->store()`, which generates a random filename and derives the extension from the
**detected** MIME type. This is the correct pattern.

### No MIME/type validation

`VERIFIED`

| Endpoint | Field | Rule |
| --- | --- | --- |
| `BrandController::store()` / `update()` | `brand_image` | none |
| `CategoryController::store()` / `update()` | `category_image` | none |
| `GiftController` (4 sites) | `category_image`, `subcategory_image`, `product_image` | none |
| `GiftVariantController` | `varient_image` | none |
| `ProductVarientControllet` (3 sites) | `Varient_image`, `product_image1[]`, `varient_image` | none |
| `ProductThumController` | `product_child_image` | none |
| `HomePromotionController` | `bg_image` | none |
| `OfferImageController` | `offer_image` | none |
| `HomeController::updateProfile()` | `avatar` | `nullable\|image\|mimes:jpg,jpeg,png\|max:1024` — but see below |

`BROKEN` — because these write into the document root (`public/images/`), a file of any type
passes through. Uploading a `.php`, `.phtml`, `.svg`, or `.html` file places it under a
web-served path. Mitigating factors depend entirely on Apache configuration: if
`php_admin_flag engine off` is not applied to `public/images/`, an uploaded PHP file executes.

`VERIFIED` — `public/.htaccess` and the project-root `.htaccess` contain **no** rules that
restrict execution or directory listing in `images/`. The root `.htaccess` only performs the
standard Laravel front-controller rewrite.

### Client-supplied extension written into the document root — `BROKEN`

`VERIFIED` — `app/Http/Controllers/HomeController.php:136-140`:

```php
$avatarName = time() . '.' . $avatar->getClientOriginalExtension();
$avatarPath = public_path('/images/');
$avatar->move($avatarPath, $avatarName);
```

The extension comes from the client filename, not from content inspection, and the file is written
into the document root. Laravel's `mimes` rule validates the detected MIME type, so a genuine JPEG
sent as `shell.php` passes and is saved as `<timestamp>.php`.

`VERIFIED` — the route is **commented out** (`routes/web.php:629`), so this is currently
unreachable. See [13-security-findings.md](13-security-findings.md) SEC-05. The code should be
deleted rather than left in place.

### No size limit

`VERIFIED` — only `HomeController::updateProfile()` sets `max:1024`. No upload rule anywhere in the
codebase sets a maximum file size, so PHP's `upload_max_filesize`/`post_max_size` alone governs.
A multi-hundred-megabyte upload to `POST /productImageUpload` or `POST /categories` will consume
disk.

## ZIP extraction — `BROKEN`

`VERIFIED` — `ProductController::bulkUpload()` (`app/Http/Controllers/ProductController.php:1211-1223`):

```php
$zip = new \ZipArchive;
$res = $zip->open($request->file('zip_file')->path());
if ($res === TRUE) {
    $tempPath = storage_path('app/temp_bulk_upload_' . time());
    File::makeDirectory($tempPath, 0755, true);
    $zip->extractTo($tempPath);
    $zip->close();
}
```

| Issue | Detail |
| --- | --- |
| No entry-name validation | `getNameIndex()` is never iterated. Modern PHP blocks basic `../` traversal inside `extractTo()`, but absolute paths, symlink entries, and Windows backslash separators are not addressed by the application. |
| Predictable temp path | Derived from `time()`; an attacker can predict and pre-create the directory. |
| Directory created 0755 | World-readable under a shared host. |
| No entry count / size cap | A zip bomb fills `storage/app/`. |
| Cleanup is best-effort | `File::deleteDirectory($tempPath)` runs only in the `try` success path and the `catch` block — but not if `Excel::import()` fatals rather than throwing. Orphaned directories accumulate. |
| No `finally` | If `$zip->open()` succeeds but a later statement throws outside the `try`, the temp directory leaks. |

`VERIFIED` — `ProductController::bulkUpload()` validates `excel_file` as
`mimes:xlsx,xls,csv,txt`, then hands it to `maatwebsite/excel`. `ProductImport` (`app/Imports/`)
implements `ToCollection, WithHeadingRow` and **creates** `Product`, `Brand`, and `ProductVarient`
rows from the sheet, including `Brand::create()` for unknown brand names. An import is therefore an
unauthenticated-content bulk write into the catalogue, gated only by the `auth` middleware.

## Deletion on replace

`VERIFIED` — a consistent (if duplicative) pattern exists in the update paths:

```php
if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
    Storage::disk('public')->delete($category->category_image);
}
```

Present in `CategoryController` (×2), `GiftController` (×6), `GiftVariantController` (×2).
`INCOMPLETE` — `BrandController`, `ProductController`, `HomePromotionController`,
`ProductThumController`, and `ProductVarientControllet` do **not** delete the previous file, so
replacing an image orphans the old one. There is no scheduled cleanup.

## PDF generation — `BROKEN` configuration

`VERIFIED` — `config/dompdf.php`:

```php
'chroot' => realpath(base_path()),
'allowed_protocols' => [
    "file://" => ["rules" => []],
    "http://" => ["rules" => []],
    "https://" => ["rules" => []]
],
'enable_font_subsetting' => false,
```

`allowed_protocols` is explicitly widened to permit `file://`, `http://`, and `https://` **with
empty rule sets** — no host allow-list, no scheme restriction. `isRemoteEnabled` and `isPhpEnabled`
are not set in the config and therefore fall back to dompdf package defaults.

`UNVERIFIED` — whether remote resource loading is actually enabled at runtime depends on the
dompdf defaults resolved by `barryvdh/laravel-dompdf: ^2.2` in `vendor/`, which was not inspected.
If `isRemoteEnabled` resolves to `true`, any user-controlled URL or `file://` path reaching a PDF
template becomes an SSRF or local-file-read primitive.

PDF templates: `resources/views/invoicePages/product_orders_invoice.blade.php`,
`resources/views/exports/orderwise.blade.php`, `resources/views/exports/productwise.blade.php`.

**Action:** set `isRemoteEnabled => false` and `isPhpEnabled => false` explicitly, and reduce
`allowed_protocols` to an empty array.

## Export generation

`VERIFIED` — `maatwebsite/excel: ^3.1` powers Excel export in `InventoryReportController`,
`PurchaseReportController`, `OrderwiseController`, `ProductwiseController`,
`GiftInventoryReportController`, `GiftProductwiseController`, `GiftPurchaseReportController`.
Export destinations: `resources/views/exports/*.blade.php`.

`INCOMPLETE` — export routes use `GET` (`/oreport/export/excel`, `/report/export/pdf`,
`/reports/inventory/excel`). Report exports triggered by GET are browser-navigable, cacheable, and
not CSRF-protected. Export volume is unbounded (no row cap observed).

## Recommendations

1. Add `.htaccess` to `public/images/` denying script execution, or move the `public` disk root to
   `storage/app/public` and run `php artisan storage:link`.
2. Correct the `public` disk `'url'` to match its actual root, or stop relying on it.
3. Add `mimes:` rules to all 12 unvalidated upload endpoints and a `max:` size to every one.
4. Delete `HomeController::updateProfile()`'s avatar path entirely.
5. Merge `varient_images` / `variant_images` into one directory.
6. Iterate and validate ZIP entry names; bound entry count and total size; extract into a
   `random_bytes()` directory; clean up in a `finally` block.
7. Harden `config/dompdf.php`: `isRemoteEnabled => false`, `isPhpEnabled => false`,
   `allowed_protocols => []`.
8. Move export endpoints to `POST` and cap row counts.
9. Add scheduled cleanup for orphaned images.