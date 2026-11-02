<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latest = DB::table('contacts')->orderBy('id', 'desc')->take(3)->get();
echo json_encode($latest, JSON_PRETTY_PRINT);
