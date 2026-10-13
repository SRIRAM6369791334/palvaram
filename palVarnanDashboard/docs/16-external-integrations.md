# 16 — External Integrations

## Shiprocket — two parallel implementations

`VERIFIED` — Shiprocket is integrated **twice**, with different code paths and different
credentials.

### A. `ShiprocketService` (correct pattern, under-used)

`app/Services/ShiprocketService.php` (108 lines), the **only** file in `app/Services/`.

```php
public function __construct()
{
    $this->baseUrl = config('services.shiprocket.base_url', 'https://apiv2.shiprocket.in/v1/external');
}

public function getToken()
{
    $email    = config('services.shiprocket.email');
    $cacheKey = 'shiprocket_token_' . md5($email ?? 'default');

    return Cache::remember($cacheKey, 8 * 60 * 60, function () {
        $response = Http::post("{$this->baseUrl}/auth/login", [
            'email'    => config('services.shiprocket.email'),
            'password' => config('services.shiprocket.password'),
        ]);

        if ($response->successful()) { return $response->json('token'); }

        Log::error('Shiprocket Login Failed', [...]);
        throw new \Exception('Shiprocket Authentication Failed');
    });
}
```

Methods: `getPickupLocations()`, `generatePickup()`, `trackAWB()`, `getShipmentDetails()`,
`generateLabel()`, `printInvoice()`.

Base URL `https://apiv2.shiprocket.in/v1/external`; endpoints `/auth/login`,
`/settings/company/pickup`, `/courier/generate/pickup`, `/courier/track/awb/{awb}`,
`/shipments/{id}`, `/courier/generate/label`, `/orders/print/invoice`.

**Strengths:** credentials from `config/services.php` (which reads `.env`), token cached for 8
hours via `Cache::remember`, `Http::withToken()` client, failure logged and surfaced as an
exception.

**Defects:**

| Issue | Detail |
| --- | --- |
| Token cache is not invalidated | 8-hour TTL with no logout/revoke endpoint. If Shiprocket revokes or rotates the token, the app fails until the cache entry expires. |
| Cache key includes the email `md5` | Meaningless given one configured account, and it means rotating the email creates a second cached token. |
| No retry / backoff | A transient Shiprocket 500 propagates straight to the caller. |
| No timeout configured | Uses Laravel's default HTTP client timeout. |
| No response validation | `->json()` is called without checking `successful()` on the six data methods; a 4xx body is returned as if it were data. |
| Exceptions are bare `\Exception` | No typed exception, so callers cannot distinguish auth failure from transport failure. |
| **Effectively unused** | No controller in `app/Http/Controllers` references `ShiprocketService`. |

### B. Inline `curl_*` in controllers (hardcoded credentials)

`VERIFIED` — four controllers authenticate with raw cURL and inline literals, bypassing the service:

| File | Lines | Endpoint |
| --- | --- | --- |
| `ProductController.php` | 1002, 1010 | `/auth/login` (live, inside `upadetstatus()`) |
| `ProductController.php` | 861, 869 | `/auth/login` (commented) |
| `ProductController.php` | 889 | `/courier/assign/awb` (commented) |
| `ProductController.php` | 923-927 | `/courier/track/shipment/{id}`, `/orders/cancel/shipment/awbs` (commented) |
| `ProductController.php` | 1024 | `/courier/generate/pickup` (live) |
| `ProductOrdersController.php` | 102, 110 | `/auth/login` (live) |
| `ProductOrdersController.php` | 125 | `/orders` (live) |
| `ProductSlotController.php` | 123, 131 | `/auth/login` (live) |
| `ProductSlotController.php` | 150 | `/orders/cancel` (live) |
| `ProductSlotController.php` | 194, 202 | `/auth/login` (live) |
| `ProductSlotController.php` | 221 | `/orders/show/{id}` (live) |
| `ProductSlotController.php` | 279 | `/orders/create/return` (live) |
| `ProductSlotController.php` | 342 | `/courier/assign/awb` (live) |

**Live hardcoded credential sets — `BROKEN`**

| Location | Credential set |
| --- | --- |
| `ProductController.php:1010` | `[SECRET_REDACTED]` |
| `ProductOrdersController.php:110` | `[SECRET_REDACTED]` — **a second, different account** |
| `ProductSlotController.php:131` | `[SECRET_REDACTED]` |
| `ProductSlotController.php:202` | `[SECRET_REDACTED]` |
| `ProductController.php:869` | `[SECRET_REDACTED]` (commented, still in file) |

See [13-security-findings.md](13-security-findings.md) SEC-04. Both accounts must be rotated.

`VERIFIED` — `ProductController::upadetstatus()` (`lines 849+`) contains a 20-line commented cURL
block (858-878) **inside the live method body**, then continues into live cURL code at line 1002.
The commented block includes the login POST with credentials. This is the single worst
secret-handling pattern in the codebase: a credential sitting in a comment directly above live code
that performs the same operation.

### cURL-specific issues in the inline paths

`VERIFIED` — the inline implementations set:

```
CURLOPT_TIMEOUT => 0        (no timeout)
CURLOPT_ENCODING => ''
CURLOPT_MAXREDIRS => 10
CURLOPT_FOLLOWLOCATION => true
```

`CURLOPT_TIMEOUT => 0` means **no timeout** — a hung Shiprocket connection holds a PHP worker
indefinitely. `CURLOPT_FOLLOWLOCATION => true` combined with an attacker-influenced URL would allow
SSRF redirection; the URLs here are constants, so this is latent. No `CURLOPT_SSL_VERIFYPEER`
override is present, so peer verification is on (correct).

`VERIFIED` — responses are handled with `json_decode($response)` and then dereferenced directly
(e.g. `$token = $SR_login_Response_out->{'token'}`) with no check that login succeeded. A failed
login yields a null property access and a 500.

### Net effect

`BROKEN` — the correct, config-driven service exists and is wired into `config/services.php` and
`.env`, yet **none of it is used**. All live Shiprocket traffic goes through hardcoded cURL. The
`.env` variables `SHIPROCKET_EMAIL`, `SHIPROCKET_PASSWORD`, `SHIPROCKET_PICKUP_LOCATION`,
`SHIPROCKET_PICKUP_PINCODE`, and `SHIPROCKET_BASE_URL` have no effect on actual behaviour.

## Razorpay — `INCOMPLETE`

`VERIFIED` — migration `2026_08_17_000001_add_shiprocket_and_razorpay_columns_to_product_orders_table`
adds `razorpay_order_id` and `razorpay_payment_id` to `product_orders`, and both are in
`ProductOrder::$fillable`.

`VERIFIED` — **no Razorpay SDK** in `composer.json`, no config entry in `config/services.php`, no
service class, and no controller code that sets either column. Payment handling is entirely in the
sibling `palVarnan/` application.

`VERIFIED` — `ProductController::createMilkSubscription()` sets `payment_status => 1` at order
creation with no gateway call. `ProductManagementController::cancelOrder()` inserts a
`cancel_requests` row with the comment
`// Log refund (extend with actual Razorpay API call or wallet logic as needed)`.

**Consequence:** this dashboard records payment status without being able to verify or act on
payment. Refunds are a local flag change. `UNVERIFIED` — whether `MilkRefundController` or
`ProductRefundController` performs a gateway call; neither was fully traced.

## AWS S3 — `INCOMPLETE`

`VERIFIED` — `config/filesystems.php` defines an `s3` disk and `.env` contains
`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`,
`AWS_USE_PATH_STYLE_ENDPOINT`. But `FILESYSTEM_DRIVER=local` and **no code references the `s3`
disk**. The configuration is inert. `AWS_URL` and `AWS_ENDPOINT` are read by the config but absent
from `.env`.

## Pusher / broadcasting — `INCOMPLETE`

`VERIFIED` — `.env` contains `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`,
`PUSHER_APP_CLUSTER`, `MIX_PUSHER_APP_KEY`, `MIX_PUSHER_APP_CLUSTER`. `BROADCAST_DRIVER=log`.
`config/broadcasting.php` is scaffold, `BroadcastServiceProvider` is scaffold,
`routes/channels.php` holds the untouched `App.Models.User.{id}` channel.

`VERIFIED` — no broadcasting event classes in `app/Events/` (the directory does not exist) and no
`ShouldBroadcast` implementations. Broadcasting is not used; with the `log` driver, `php artisan
queue:work` output would receive any broadcast that is ever fired.

## Mail

`VERIFIED` — `MAIL_MAILER=smtp` with host/port/credentials in `.env`. One mail template exists:
`resources/views/emails/order_status_updated.blade.php`. `.env.example` points at `mailhog:1025`
(a local dev catcher), while `.env` uses a real SMTP host.

`VERIFIED` — `QUEUE_CONNECTION=sync`, so any `Mailable` sends inline within the request. An SMTP
timeout therefore blocks the HTTP response. `UNVERIFIED` — whether the mail template is dispatched
by any code path; the sending site was not located in the audited controllers.

## Other outbound calls

`VERIFIED` — a `sendsms` controller exists in `app/Http/Controllers/` but is not routed. Its
contents were not traced. If it performs an SMS API call, credentials and the provider would be
`UNVERIFIED`. `UNVERIFIED` — flagged for follow-up.

## Mail/OTP and notification tables — `INCOMPLETE`

`VERIFIED` — `mail_otps`, `otps`, `notifications`, `app_notifications`, `app_alerts`, and
`user_read_notifications` tables exist; `MailOtp`, `Otp`, `Notification`, `AppNotification`,
`AppAlert` models exist. No SMS or email dispatch code was found in the dashboard controllers.
These tables are almost certainly written by the sibling customer application.

## Integration risk summary

| Integration | Implementation | Credentials in config? | Credentials in code? | Used? |
| --- | --- | --- | --- | --- |
| Shiprocket auth | **Two** (service + 4× cURL) | Yes | **Yes — 4 sites, 2 accounts** | cURL paths only |
| Shiprocket data calls | cURL only | No | n/a | Yes |
| Razorpay | **None** | No | No | No |
| AWS S3 | Config only | Yes | No | No |
| Pusher | Config only | Yes | No | No |
| SMTP mail | Framework default | Yes | No | `UNVERIFIED` |
| SMS (`sendsms`) | `UNVERIFIED` | `UNVERIFIED` | `UNVERIFIED` | Not routed |

## Recommendations

1. **Rotate both Shiprocket accounts now.** Then delete all inline credentials.
2. Delete the commented cURL blocks entirely — they are a credential store, not documentation.
3. Route every Shiprocket call through `ShiprocketService`; add `timeout(15)` and
   `->throw()` to the HTTP client; check `successful()` before `json()`.
4. Delete the unused `config/services.php` entries for mailgun, postmark, and AWS, or implement
   them — dead credential slots invite confusion.
5. Add a `ShiprocketException` type and log the shipment id on every failure.
6. Decide explicitly whether Razorpay belongs in this application. If not, remove the columns from
   `product_orders` and stop writing `payment_status` from here.
7. Trace `sendsms` and `MilkRefundController`/`ProductRefundController` before the next release.