<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShiprocketService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.shiprocket.base_url', 'https://apiv2.shiprocket.in/v1/external');
    }

    /**
     * 1. Get Authentication Token
     * Retrieves token from cache or makes a new API call to login.
     *
     * @return string
     * @throws \Exception
     */
    public function getToken()
    {
        $email = config('services.shiprocket.email');
        $cacheKey = 'shiprocket_token_' . md5($email ?? 'default');
        
        return Cache::remember($cacheKey, 8 * 60 * 60, function () { // Token valid for 10 days, cache for 8 hours
            $response = Http::post("{$this->baseUrl}/auth/login", [
                'email' => config('services.shiprocket.email'),
                'password' => config('services.shiprocket.password'),
            ]);

            if ($response->successful()) {
                Log::info('Shiprocket Login Successful');
                return $response->json('token');
            }

            Log::error('Shiprocket Login Failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Shiprocket Authentication Failed: ' . $response->body());
        });
    }

    /**
     * Helper to get HTTP client with Bearer Token
     */
    protected function client()
    {
        return Http::withToken($this->getToken())->acceptJson();
    }

    /**
     * 2. Check Serviceability
     *
     * @param array $params ['pickup_postcode' => '', 'delivery_postcode' => '', 'weight' => '', 'cod' => '']
     * @return mixed
     */
    public function checkServiceability(array $params)
    {
        // GET https://apiv2.shiprocket.in/v1/external/courier/serviceability
        $response = $this->client()->get("{$this->baseUrl}/courier/serviceability", $params);
        
        if (!$response->successful()) {
            Log::error('Shiprocket Serviceability API Error', [
                'status' => $response->status(),
                'body' => $response->json(),
                'params' => $params
            ]);
        }
        
        return $response->json();
    }

    /**
     * 3. Create Adhoc Order
     *
     * @param array $orderData
     * @return mixed
     */
    public function createOrder(array $orderData)
    {
        // POST https://apiv2.shiprocket.in/v1/external/orders/create/adhoc
        $response = $this->client()->post("{$this->baseUrl}/orders/create/adhoc", $orderData);
        
        if (!$response->successful()) {
            Log::error('Shiprocket API Error Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
        }

        return $response->json();
    }

    /**
     * 4. Assign AWB
     *
     * @param array $payload e.g. ['shipment_id' => 12345]
     * @return mixed
     */
    public function assignAWB(array $payload)
    {
        // POST https://apiv2.shiprocket.in/v1/external/courier/assign/awb
        $response = $this->client()->post("{$this->baseUrl}/courier/assign/awb", $payload);
        return $response->json();
    }

    /**
     * 5. Generate Pickup
     *
     * @param array $payload e.g. ['shipment_id' => [12345]]
     * @return mixed
     */
    public function generatePickup(array $payload)
    {
        // POST https://apiv2.shiprocket.in/v1/external/courier/generate/pickup
        $response = $this->client()->post("{$this->baseUrl}/courier/generate/pickup", $payload);
        return $response->json();
    }

    /**
     * 6. Generate Manifest
     *
     * @param array $payload e.g. ['shipment_id' => [12345]]
     * @return mixed
     */
    public function generateManifest(array $payload)
    {
        // POST https://apiv2.shiprocket.in/v1/external/manifests/generate
        $response = $this->client()->post("{$this->baseUrl}/manifests/generate", $payload);
        return $response->json();
    }

    /**
     * 7. Print Manifest
     *
     * @param array $payload e.g. ['order_ids' => [12345]]
     * @return mixed
     */
    public function printManifest(array $payload)
    {
        // POST https://apiv2.shiprocket.in/v1/external/manifests/print
        $response = $this->client()->post("{$this->baseUrl}/manifests/print", $payload);
        return $response->json();
    }

    /**
     * 8. Generate Label
     *
     * @param array $payload e.g. ['shipment_id' => [12345]]
     * @return mixed
     */
    public function generateLabel(array $payload)
    {
        // POST https://apiv2.shiprocket.in/v1/external/courier/generate/label
        $response = $this->client()->post("{$this->baseUrl}/courier/generate/label", $payload);
        return $response->json();
    }

    /**
     * 9. Print Invoice
     *
     * @param array $payload e.g. ['ids' => [12345]]
     * @return mixed
     */
    public function printInvoice(array $payload)
    {
        // POST https://apiv2.shiprocket.in/v1/external/orders/print/invoice
        $response = $this->client()->post("{$this->baseUrl}/orders/print/invoice", $payload);
        return $response->json();
    }

    /**
     * 10. Track AWB
     *
     * @param string $awbCode
     * @return mixed
     */
    public function trackAWB($awbCode)
    {
        // GET https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb_code}
        $response = $this->client()->get("{$this->baseUrl}/courier/track/awb/{$awbCode}");
        return $response->json();
    }

    /**
     * 11. Get All Pickup Locations
     */
    public function getPickupLocations()
    {
        // GET https://apiv2.shiprocket.in/v1/external/settings/company/pickup
        $response = $this->client()->get("{$this->baseUrl}/settings/company/pickup");
        return $response->json();
    }
}
