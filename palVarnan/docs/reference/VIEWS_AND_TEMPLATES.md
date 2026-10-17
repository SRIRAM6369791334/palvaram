# VIEWS_AND_TEMPLATES.md

**Purpose:** The 23 Blade views and their layout composition.

---

## 1. Views inventory (23)

Total Blade files: 23 `VERIFIED_FROM_SOURCE`.

```
resources/views/
├── layouts/
│   ├── app.blade.php
│   ├── country-modal.blade.php
│   ├── footer.blade.php
│   └── header.blade.php
├── pages/
│   ├── about.blade.php
│   ├── cart.blade.php
│   ├── checkout.blade.php
│   ├── contact.blade.php
│   ├── home.blade.php
│   ├── login.blade.php
│   ├── myaccount.blade.php
│   ├── privacy-policy.blade.php
│   ├── product-detail.blade.php
│   ├── products.blade.php
│   ├── register.blade.php
│   ├── return-refund-policy.blade.php
│   ├── shipping-policy.blade.php
│   ├── terms-conditions.blade.php
│   ├── thankyou.blade.php
│   └── wishlist.blade.php
├── partials/
│   └── pagination.blade.php
├── country-selection.blade.php
└── welcome.blade.php
```

Counts: layouts 4 + pages 16 + partials 1 + 2 top-level = 23. `VERIFIED_FROM_SOURCE`.

---

## 2. Layout composition

[`layouts/app.blade.php`](../../resources/views/layouts/app.blade.php) is the main layout. It includes:
- [`header.blade.php`](../../resources/views/layouts/header.blade.php)
- [`country-modal.blade.php`](../../resources/views/layouts/country-modal.blade.php)
- [`footer.blade.php`](../../resources/views/layouts/footer.blade.php)
- Yields `content`, `styles`, `scripts` as appropriate

View composer (global) sets `cartCount`, `wishlistCount`, `cartSubtotal` for all views ([`AppServiceProvider.php:33-76`](../../app/Providers/AppServiceProvider.php#L33-L76)). `VERIFIED_FROM_SOURCE`.

---

## 3. Key pages

| Page | Controller/Route | Purpose |
|---|---|---|
| [`pages/home.blade.php`](../../resources/views/pages/home.blade.php) | `HomeController@index` (`GET /`) | Banners + categories; large template demo sections |
| [`pages/products.blade.php`](../../resources/views/pages/products.blade.php) | `ProductController@index` (`GET /products`) | Catalog listing + filters |
| [`pages/product-detail.blade.php`](../../resources/views/pages/product-detail.blade.php) | `ProductController@show` (`GET /product/{slug}`) | Product detail; also rendered by `/product` view route |
| [`pages/cart.blade.php`](../../resources/views/pages/cart.blade.php) | `CartController@index` (`GET /cart`) | Cart view |
| [`pages/checkout.blade.php`](../../resources/views/pages/checkout.blade.php) | `CheckoutController@index` (`GET /checkout`) | Checkout; includes hidden fields for `shipping_charge`, `coupon_discount`, courier selection ([see lines 472-473,805-806](../../resources/views/pages/checkout.blade.php#L472-L473)) |
| [`pages/thankyou.blade.php`](../../resources/views/pages/thankyou.blade.php) | `CheckoutController@thankyou` | Order confirmation |
| [`pages/wishlist.blade.php`](../../resources/views/pages/wishlist.blade.php) | `WishlistController@index` | Wishlist |
| [`pages/myaccount.blade.php`](../../resources/views/pages/myaccount.blade.php) | `ProfileController@index` | Account dashboard |
| [`pages/login.blade.php`](../../resources/views/pages/login.blade.php), [`pages/register.blade.php`](../../resources/views/pages/register.blade.php) | Auth views | Login/register forms (JSON POSTs) |
| [`country-selection.blade.php`](../../resources/views/country-selection.blade.php) | `CountryController@index` | Country gate view |

Static/legal pages: about, contact, privacy-policy, terms-conditions, return-refund-policy, shipping-policy. `VERIFIED_FROM_SOURCE`.

---

## 4. Related

- [`FRONTEND_AND_ASSETS.md`](./FRONTEND_AND_ASSETS.md)
- [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md)