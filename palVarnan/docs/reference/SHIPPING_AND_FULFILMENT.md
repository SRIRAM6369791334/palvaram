# SHIPPING_AND_FULFILMENT.md

**Purpose:** Shiprocket serviceability, order creation, AWB assignment, and un-scheduled pickup.

---

## 1. Service

[`app/Services/ShiprocketService.php`](../../app/Services/ShiprocketService.php) wraps Shiprocket API v2.
Base URL default `https://apiv2.shiprocket.in/v1/external` ([`15`](../../app/Services/ShiprocketService.php#L15)).
Auth token cached 8 hours (`Cache::remember`, [30](../../app/Services/ShiprocketService.php#L30)).

Methods: `getToken` (25), `checkServiceability` (63), `createOrder` (85), `assignAWB` (106), `generatePickup` (119),
`generateManifest` (132), `printManifest` (145), `generateLabel` (158), `printInvoice` (171), `trackAWB` (184),
`getPickupLocations` (194). `VERIFIED_FROM_SOURCE`.

---

## 2. Credentials

`config/services.php:43-48`: `SHIPROCKET_EMAIL`, `SHIPROCKET_PASSWORD`, `SHIPROCKET_PICKUP_LOCATION` (default "Home"),
`SHIPROCKET_PICKUP_PINCODE` (default "641006"). `VERIFIED_FROM_SOURCE`.

---

## 3. Checkout integration

- **Courier serviceability:** [`CheckoutController::getShiprocketCouriers`](../../app/Http/Controllers/CheckoutController.php#L89-L167) calls `checkServiceability()` (124), normalizes couriers, sorts by rate. COD adds `cod_charges` to freight (136).
- **Order creation:** [`placeOrder`](../../app/Http/Controllers/CheckoutController.php#L433-L527) after DB commit builds payload and calls `createOrder()` (498). On success stores `shiprocket_order_id`, `shiprocket_shipment_id`, `shiprocket_status` (502-506).
- **AWB:** if `selected_courier_id` provided, `assignAWB()` (510-513); on success stores `awb_code`, `courier_name`, `shiprocket_status='AWB Assigned'` (517-521).
- Errors caught/logged; DB already committed (525-527).

`VERIFIED_FROM_SOURCE`.

---

## 4. Pickup never scheduled

- `generatePickup()` exists ([`ShiprocketService.php:119-124`](../../app/Services/ShiprocketService.php#L119-L124)) but **has no caller in `app/`**.
- `generateLabel()` and `generateManifest()` also have no callers.
- **Behaviourally, pickup is never scheduled.** `VERIFIED_FROM_SOURCE`. `CONFLICTING_EVIDENCE` with older docs naming `createPickup()` (that method does not exist).

---

## 5. Related

- [`../23-external-services.md`](../23-external-services.md)
- [`../12-services-and-integrations.md`](../12-services-and-integrations.md)
- [`CHECKOUT_AND_ORDERS.md`](./CHECKOUT_AND_ORDERS.md)