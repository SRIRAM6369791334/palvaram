<?php

echo "====================================================\n";
echo "    TESTING RAZORPAY & SHIPROCKET INTEGRATION       \n";
echo "====================================================\n\n";

// Parse .env directly
$envPath = __DIR__ . '/.env';
$env = parse_ini_file($envPath);

$key = $env['RAZORPAY_KEY'] ?? 'rzp_test_SNp3LdS68VtVwj';
$secret = $env['RAZORPAY_SECRET'] ?? 'bsZto170Mmk55F6asHLpzF99';

$srEmail = $env['SHIPROCKET_EMAIL'] ?? 'ss9819690@gmail.com';
$srPassword = $env['SHIPROCKET_PASSWORD'] ?? 'gwFIu8jB&0iY0ZHKotQ^9Mzx#O9nb69Z';
$pickupPin = $env['SHIPROCKET_PICKUP_PINCODE'] ?? '641006';

// 1. TEST RAZORPAY API
echo "[1] Testing Razorpay API (Key: " . substr($key, 0, 10) . "...):\n";

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => "$key:$secret",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'receipt' => 'test_rcpt_' . uniqid(),
        'amount' => 50000, // 500.00 INR in paise
        'currency' => 'INR'
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 12,
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "    >>> [SUCCESS] Razorpay API Connected & Test Order Created Successfully!\n";
    echo "        - Order ID: " . $data['id'] . "\n";
    echo "        - Amount: ₹" . ($data['amount'] / 100) . " " . $data['currency'] . "\n";
    echo "        - Status: " . $data['status'] . "\n\n";
} else {
    echo "    >>> [FAILED] Razorpay Response ($httpCode): $response\n\n";
}

// 2. TEST SHIPROCKET API
echo "[2] Testing Shiprocket API (Email: $srEmail):\n";

// Login
$ch = curl_init('https://apiv2.shiprocket.in/v1/external/auth/login');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'email' => $srEmail,
        'password' => $srPassword
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 12,
    CURLOPT_SSL_VERIFYPEER => false
]);

$srLoginRes = curl_exec($ch);
$loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$token = null;
if ($loginCode == 200) {
    $loginData = json_decode($srLoginRes, true);
    $token = $loginData['token'] ?? null;
    echo "    >>> [SUCCESS] Shiprocket Authentication Successful!\n";
    echo "        - Token generated: " . substr($token, 0, 30) . "...\n\n";
} else {
    echo "    >>> [FAILED] Shiprocket Login ($loginCode): $srLoginRes\n\n";
}

// Courier Serviceability Check
if ($token) {
    echo "[3] Testing Shiprocket Courier Serviceability (From: $pickupPin to: 600001):\n";
    $pincodeParams = http_build_query([
        'pickup_postcode' => $pickupPin,
        'delivery_postcode' => '600001',
        'weight' => '0.5',
        'cod' => 0
    ]);

    $ch = curl_init("https://apiv2.shiprocket.in/v1/external/courier/serviceability?$pincodeParams");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $token"
        ],
        CURLOPT_TIMEOUT => 12,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $serviceRes = curl_exec($ch);
    $servCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($servCode == 200) {
        $servData = json_decode($serviceRes, true);
        if (!empty($servData['data']['available_courier_companies'])) {
            $couriers = $servData['data']['available_courier_companies'];
            echo "    >>> [SUCCESS] Available Courier Partners Found: " . count($couriers) . "\n";
            foreach (array_slice($couriers, 0, 4) as $c) {
                echo "        - " . $c['courier_name'] . " | Rate: ₹" . $c['freight_charge'] . " | ETD: " . ($c['etd'] ?? 'N/A') . " days\n";
            }
        } else {
            echo "    >>> [INFO] Message: " . ($servData['message'] ?? 'No couriers listed for this pincode combination') . "\n";
        }
    } else {
        echo "    >>> [FAILED] Serviceability Check ($servCode): $serviceRes\n";
    }

    // Pickup locations check
    echo "\n[4] Testing Shiprocket Pickup Locations List:\n";
    $ch = curl_init("https://apiv2.shiprocket.in/v1/external/settings/company/pickup");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $token"
        ],
        CURLOPT_TIMEOUT => 12,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $locRes = curl_exec($ch);
    $locCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($locCode == 200) {
        $locData = json_decode($locRes, true);
        if (!empty($locData['data']['shipping_address'])) {
            echo "    >>> [SUCCESS] Configured Pickup Locations Found:\n";
            foreach ($locData['data']['shipping_address'] as $addr) {
                echo "        - Location: " . ($addr['pickup_location'] ?? 'N/A') . " | City: " . ($addr['city'] ?? '') . " | Pincode: " . ($addr['pin_code'] ?? '') . "\n";
            }
        }
    }
}

echo "\n====================================================\n";
echo "    ALL API TESTS COMPLETED!\n";
echo "====================================================\n";
