<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "States: " . \App\Models\State::count() . "\n";
echo "Districts: " . \App\Models\District::count() . "\n";
echo "Areas: " . \App\Models\Area::count() . "\n";

$sampleArea = \App\Models\Area::first();
if ($sampleArea) {
    echo "Sample Area:\n";
    print_r($sampleArea->toArray());
}

$sampleDist = \App\Models\District::first();
if ($sampleDist) {
    echo "Sample District:\n";
    print_r($sampleDist->toArray());
}

$sampleState = \App\Models\State::first();
if ($sampleState) {
    echo "Sample State:\n";
    print_r($sampleState->toArray());
}
