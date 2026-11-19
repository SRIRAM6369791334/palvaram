<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$response = \Illuminate\Support\Facades\Http::timeout(5)
    ->withoutVerifying()
    ->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) PalVarnan/1.0',
        'Accept' => 'application/json'
    ])
    ->get("https://api.postalpincode.in/pincode/999999");
echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
