<?php

echo "====================================================\n";
echo "           INTEGRATION TEST SUITE                   \n";
echo "====================================================\n\n";

// 1. TEST DATABASE & ALTER TABLE
echo "[1] Testing Database Connection & ALTER TABLE:\n";
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=palvarnan_db;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "    - Database Connection: OK (Connected to palvarnan_db)\n";

    $columnsToAdd = [
        'razorpay_order_id' => "VARCHAR(255) NULL AFTER `payment_method`",
        'razorpay_payment_id' => "VARCHAR(255) NULL AFTER `razorpay_order_id`",
        'shiprocket_order_id' => "VARCHAR(255) NULL AFTER `razorpay_payment_id`",
        'shiprocket_shipment_id' => "VARCHAR(255) NULL AFTER `shiprocket_order_id`",
        'awb_code' => "VARCHAR(255) NULL AFTER `shiprocket_shipment_id`",
        'courier_name' => "VARCHAR(255) NULL AFTER `awb_code`",
        'pickup_scheduled' => "TINYINT(1) DEFAULT 0 AFTER `courier_name`",
        'manifest_url' => "VARCHAR(255) NULL AFTER `pickup_scheduled`",
        'label_url' => "VARCHAR(255) NULL AFTER `manifest_url`",
        'invoice_url' => "VARCHAR(255) NULL AFTER `label_url`",
        'shiprocket_status' => "VARCHAR(255) NULL AFTER `invoice_url`",
        'cancellation_reason' => "TEXT NULL AFTER `shiprocket_status`"
    ];

    // Get existing columns
    $stmt = $pdo->query("DESCRIBE `product_orders`");
    $existingCols = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($columnsToAdd as $col => $def) {
        if (!in_array($col, $existingCols)) {
            $pdo->exec("ALTER TABLE `product_orders` ADD COLUMN `$col` $def");
            echo "    [+] Added column `$col` successfully.\n";
        } else {
            echo "    [i] Column `$col` already exists.\n";
        }
    }
    echo "    - Schema Status: All required columns present in `product_orders`!\n\n";

} catch (Exception $e) {
    echo "    [!] Database Note: " . $e->getMessage() . "\n\n";
}

// 2. TEST RAZORPAY API
echo "[2] Testing Razorpay API (Direct HTTP / SDK):\n";
$key = 'rzp_test_SNp3LdS68VtVwj';
$secret = 'bsZto170Mmk55F6asHLpzF99';

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => "$key:$secret",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'receipt' => 'test_' . uniqid(),
        'amount' => 50000, // 500 INR
        'currency' => 'INR'
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "    [SUCCESS] Razorpay API working correctly!\n";
    echo "    - Created Test Order ID: " . $data['id'] . "\n";
    echo "    - Amount: ₹" . ($data['amount'] / 100) . " " . $data['currency'] . "\n";
    echo "    - Status: " . $data['status'] . "\n\n";
} else {
    echo "    [!] Razorpay Response ($httpCode): $response\n\n";
}

// 3. TEST SHIPROCKET API
echo "[3] Testing Shiprocket API:\n";
$srEmail = 'rajaeswaran260201@gmail.com';
$srPassword = 'byErHq#jQxP70D^y5N6vKC&HHKf#$4qy';

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
    CURLOPT_TIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false
]);

$srLoginRes = curl_exec($ch);
$loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$token = null;
if ($loginCode == 200) {
    $loginData = json_decode($srLoginRes, true);
    $token = $loginData['token'] ?? null;
    echo "    [SUCCESS] Shiprocket Authentication Successful!\n";
    echo "    - Token: " . substr($token, 0, 20) . "... (Length: " . strlen($token) . ")\n";
} else {
    echo "    [!] Shiprocket Login Failed ($loginCode): $srLoginRes\n";
}

// Courier Serviceability Check
if ($token) {
    echo "\n[4] Testing Shiprocket Serviceability (Courier rates):\n";
    $pincodeParams = http_build_query([
        'pickup_postcode' => '622301',
        'delivery_postcode' => '641006',
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
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $serviceRes = curl_exec($ch);
    $servCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($servCode == 200) {
        $servData = json_decode($serviceRes, true);
        if (!empty($servData['data']['available_courier_companies'])) {
            $couriers = $servData['data']['available_courier_companies'];
            echo "    [SUCCESS] Found " . count($couriers) . " available courier partners!\n";
            foreach (array_slice($couriers, 0, 3) as $c) {
                echo "      * " . $c['courier_name'] . " | Rate: ₹" . $c['freight_charge'] . " | ETD: " . ($c['etd'] ?? 'N/A') . "\n";
            }
        } else {
            echo "    [i] No specific couriers returned: " . ($servData['message'] ?? 'N/A') . "\n";
        }
    } else {
        echo "    [!] Serviceability failed ($servCode): $serviceRes\n";
    }

    // Pickup locations check
    echo "\n[5] Testing Shiprocket Pickup Locations:\n";
    $ch = curl_init("https://apiv2.shiprocket.in/v1/external/settings/company/pickup");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $token"
        ],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $locRes = curl_exec($ch);
    $locCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($locCode == 200) {
        $locData = json_decode($locRes, true);
        if (!empty($locData['data']['shipping_address'])) {
            echo "    [SUCCESS] Pickup Locations found:\n";
            foreach ($locData['data']['shipping_address'] as $addr) {
                echo "      * " . ($addr['pickup_location'] ?? 'N/A') . " (City: " . ($addr['city'] ?? '') . ", Pin: " . ($addr['pin_code'] ?? '') . ")\n";
            }
        }
    }
}

echo "\n====================================================\n";
echo "           ALL INTEGRATION TESTS COMPLETED!         \n";
echo "====================================================\n";
