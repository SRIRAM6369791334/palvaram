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
     * Get Authentication Token
     */
    public function getToken()
    {
        $email = config('services.shiprocket.email');
        $cacheKey = 'shiprocket_token_' . md5($email ?? 'default');
        
        return Cache::remember($cacheKey, 8 * 60 * 60, function () { 
            $response = Http::post("{$this->baseUrl}/auth/login", [
                'email' => config('services.shiprocket.email'),
                'password' => config('services.shiprocket.password'),
            ]);

            if ($response->successful()) {
                return $response->json('token');
            }

            Log::error('Shiprocket Login Failed', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Shiprocket Authentication Failed');
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
     * Get All Pickup Locations
     */
    public function getPickupLocations()
    {
        // GET https://apiv2.shiprocket.in/v1/external/settings/company/pickup
        $response = $this->client()->get("{$this->baseUrl}/settings/company/pickup");
        return $response->json();
    }

    /**
     * Generate Pickup Request
     */
    public function generatePickup(array $payload)
    {
        // POST https://apiv2.shiprocket.in/v1/external/courier/generate/pickup
        $response = $this->client()->post("{$this->baseUrl}/courier/generate/pickup", $payload);
        return $response->json();
    }

    /**
     * Track Shipment / AWB
     */
    public function trackAWB($awbCode)
    {
        // GET https://apiv2.shiprocket.in/v1/external/courier/track/awb/{awb_code}
        $response = $this->client()->get("{$this->baseUrl}/courier/track/awb/{$awbCode}");
        return $response->json();
    }

    /**
     * Get Shipment Details
     */
    public function getShipmentDetails($shipmentId)
    {
        $response = $this->client()->get("{$this->baseUrl}/shipments/{$shipmentId}");
        return $response->json();
    }

    /**
     * Generate Label
     */
    public function generateLabel(array $payload)
    {
        $response = $this->client()->post("{$this->baseUrl}/courier/generate/label", $payload);
        return $response->json();
    }

    /**
     * Generate Invoice
     */
    public function printInvoice(array $payload)
    {
        $response = $this->client()->post("{$this->baseUrl}/orders/print/invoice", $payload);
        return $response->json();
    }
}
