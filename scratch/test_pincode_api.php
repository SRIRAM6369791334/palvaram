<?php
$pincodes = ['600001', '629001', '110001', '999999'];

foreach ($pincodes as $pin) {
    $url = "https://api.postalpincode.in/pincode/{$pin}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    $res = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Pincode: {$pin} | Status: {$status}\n";
    $data = json_decode($res, true);
    if (!empty($data[0]['Status']) && $data[0]['Status'] === 'Success') {
        $po = $data[0]['PostOffice'][0] ?? [];
        echo "  District: " . ($po['District'] ?? 'N/A') . " | State: " . ($po['State'] ?? 'N/A') . "\n";
    } else {
        echo "  Result: Invalid or No records found\n";
    }
}
