<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "USERS COLUMNS:\n" . implode(", ", Schema::getColumnListing('users')) . "\n\n";

$users = DB::table('users')->take(3)->get();
echo json_encode($users, JSON_PRETTY_PRINT);
