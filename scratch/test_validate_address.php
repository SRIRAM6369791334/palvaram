<?php

require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(\App\Http\Controllers\CheckoutController::class);
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('validateAddress');
$method->setAccessible(true);

$validPayload = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'phone_number' => '9876543210',
    'email' => 'john@example.com',
    'address_line_one' => '123 Test Street',
    'city' => 'Chennai',
    'state' => 'Tamil Nadu',
    'pincode' => '600001'
];

try {
    $res = $method->invoke($controller, new \Illuminate\Http\Request($validPayload));
    echo "Valid Address Passed: YES\n";
} catch (\Throwable $e) {
    echo "Valid Address Failed: " . $e->getMessage() . "\n";
}

$mismatchPayload = $validPayload;
$mismatchPayload['state'] = 'Kerala'; // Mismatch!

try {
    $res = $method->invoke($controller, new \Illuminate\Http\Request($mismatchPayload));
    echo "Mismatch Passed (Unexpected): YES\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Mismatch Caught ValidationException: " . json_encode($e->errors()) . "\n";
} catch (\Throwable $e) {
    echo "Mismatch Caught Error: " . $e->getMessage() . "\n";
}

$invalidPinPayload = $validPayload;
$invalidPinPayload['pincode'] = '999999';

try {
    $res = $method->invoke($controller, new \Illuminate\Http\Request($invalidPinPayload));
    echo "Invalid Pin Passed (Unexpected): YES\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Invalid Pin Caught ValidationException: " . json_encode($e->errors()) . "\n";
} catch (\Throwable $e) {
    echo "Invalid Pin Caught Error: " . $e->getMessage() . "\n";
}
