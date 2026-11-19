<?php

require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(\App\Http\Controllers\CheckoutController::class);
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('validateAddress');
$method->setAccessible(true);

$invalidPinPayload = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'phone_number' => '9876543210',
    'email' => 'john@example.com',
    'address_line_one' => '123 Test Street',
    'city' => 'Chennai',
    'state' => 'Tamil Nadu',
    'pincode' => '999999'
];

try {
    $res = $method->invoke($controller, new \Illuminate\Http\Request($invalidPinPayload));
    echo "Passed with result: " . json_encode($res) . "\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "ValidationException caught: " . json_encode($e->errors()) . "\n";
} catch (\Throwable $e) {
    echo "Other exception caught: " . get_class($e) . " : " . $e->getMessage() . "\n";
}
