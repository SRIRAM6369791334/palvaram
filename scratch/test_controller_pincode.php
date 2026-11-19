<?php

require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(\App\Http\Controllers\CheckoutController::class);

$res1 = $controller->lookupPincode(new \Illuminate\Http\Request(['pincode' => '600001']));
echo "600001: " . $res1->getStatusCode() . " => " . $res1->getContent() . PHP_EOL;

$res2 = $controller->lookupPincode(new \Illuminate\Http\Request(['pincode' => '560001']));
echo "560001: " . $res2->getStatusCode() . " => " . $res2->getContent() . PHP_EOL;

$res3 = $controller->lookupPincode(new \Illuminate\Http\Request(['pincode' => '999999']));
echo "999999: " . $res3->getStatusCode() . " => " . $res3->getContent() . PHP_EOL;
