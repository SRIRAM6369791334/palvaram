# FRONTEND_AND_ASSETS.md

**Purpose:** `public/assets`, Vite/Tailwind, and the vendored MarketPro theme.

---

## 1. Assets

Public assets under [`public/assets/`](../../public/assets):
- `css/`, `images/`, `js/`, `sass/`, `webfonts/` (MarketPro theme assets). `VERIFIED_FROM_SOURCE`.

Vite sources: [`resources/css/app.css`](../../resources/css/app.css), [`resources/js/app.js`](../../resources/js/app.js), [`resources/js/bootstrap.js`](../../resources/js/bootstrap.js). `VERIFIED_FROM_SOURCE`.

Build config: [`vite.config.js`](../../vite.config.js) uses `laravel-vite-plugin` and `@tailwindcss/vite`. `VERIFIED_FROM_SOURCE`.

`package.json` declares `vite ^7.0.7`, `tailwindcss ^4.0.0`, `@tailwindcss/vite ^4.0.0`. `VERIFIED_FROM_SOURCE`.

---

## 2. Theme notes

The frontend uses a **vendored MarketPro** HTML theme (see repo root `marketpro-ecommerce-multivendor-html-template-*`). Templates in `resources/views` include MarketPro markup/classes. Home view contains substantial template/demo content. `INFERRED` from audit context; `VERIFIED_FROM_SOURCE` from view files.

---

## 3. Related

- [`VIEWS_AND_TEMPLATES.md`](./VIEWS_AND_TEMPLATES.md)
- [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md)