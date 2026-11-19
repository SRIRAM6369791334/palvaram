<?php

require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cached = \Illuminate\Support\Facades\Cache::get("pincode_lookup_999999");
var_dump($cached);
