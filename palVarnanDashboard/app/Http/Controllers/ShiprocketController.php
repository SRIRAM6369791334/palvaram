<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShiprocketService;

class ShiprocketController extends Controller
{
    protected $shiprocket;

    public function __construct(ShiprocketService $shiprocket)
    {
        $this->shiprocket = $shiprocket;
    }

    /**
     * Display all pickup locations.
     */
    public function pickupLocations()
    {
        try {
            $response = $this->shiprocket->getPickupLocations();
            $locations = [];

            if (isset($response['data']['shipping_address'])) {
                $locations = $response['data']['shipping_address'];
            }

            return view('pages.shiprocket_pickup', compact('locations'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Shiprocket Error: ' . $e->getMessage());
        }
    }

    /**
     * Trigger manual sync.
     */
    public function syncStatus()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('shiprocket:sync');
            return redirect()->back()->with('success', 'Shiprocket Sync completed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }
}
