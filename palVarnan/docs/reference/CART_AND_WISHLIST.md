# CART_AND_WISHLIST.md

**Purpose:** Guest vs. user cart identity, AJAX endpoints, wishlist behavior.

---

## 1. Cart identity & storage

- Authenticated: stored with `user_id = Auth::id()`
- Guest: stored with `ip_addresscart` and `user_id = NULL`
- View composer uses `guest_token` cookie or IP for guest counts/subtotal ([`AppServiceProvider.php:56,59-72`](../../app/Providers/AppServiceProvider.php#L56-L72))

**No merge** on login. `AuthController@login` does not move guest cart rows to user. `INFERRED`.

---

## 2. Cart endpoints

| Endpoint | Method | Scoped? | Notes |
|---|---|---|---|
| `/cart` | GET | Yes (by user/ip) | [`CartController@index`](../../app/Http/Controllers/CartController.php#L11-L31) |
| `/cart-update` | POST | **No** | Bulk by cart id — **IDOR** ([33-44](../../app/Http/Controllers/CartController.php#L33-L44)) |
| `/cart-remove/{id}` | GET | **No** | By cart id — **IDOR + GET** ([46-62](../../app/Http/Controllers/CartController.php#L46-L62)) |
| `/cart/store` | POST | N/A (create) | Add/increment ([64-93](../../app/Http/Controllers/CartController.php#L64-L93)) |
| `/cart/ajax-update` | POST | Yes | By product_varient_id + identity ([95-113](../../app/Http/Controllers/CartController.php#L95-L113)) |
| `/cart/ajax-remove` | POST | Yes | By product_varient_id + identity ([115-128](../../app/Http/Controllers/CartController.php#L115-L128)) |
| `/cart/subtotal` | GET | Yes | JSON subtotal ([130-145](../../app/Http/Controllers/CartController.php#L130-L145)) |

`VERIFIED_FROM_SOURCE`.

---

## 3. Wishlist endpoints

| Endpoint | Method | Scoped? | Notes |
|---|---|---|---|
| `/wishlist/toggle` | POST | Uses user_id (auth) | Creates/deletes; route not behind auth ([`WishlistController@toggle`](../../app/Http/Controllers/WishlistController.php#L12-L36)) |
| `/wishlist` | GET | By Auth::id() | Index ([40-48](../../app/Http/Controllers/WishlistController.php#L40-L48)) |
| `/wishlist/{id}/remove` | DELETE | **Yes** (id + user_id) | **FIXED** IDOR ([50-54](../../app/Http/Controllers/WishlistController.php#L50-L54)) |
| `/wishlist/check` | GET | auth required | ([81-89](../../app/Http/Controllers/WishlistController.php#L81-L89)) |
| `/wishlist/check-product` | GET | auth required | ([56-79](../../app/Http/Controllers/WishlistController.php#L56-L79)) |

`VERIFIED_FROM_SOURCE`.

---

## 4. Related

- [`../04-feature-inventory.md`](../04-feature-inventory.md)
- [`AUTHENTICATION_AND_ACCESS.md`](./AUTHENTICATION_AND_ACCESS.md)