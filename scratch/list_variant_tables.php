<?php
require __DIR__ . '/../palVarnanDashboard/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnanDashboard/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$tblKey = 'Tables_in_' . env('DB_DATABASE');
$tableNames = array_map(function($t) use ($tblKey) {
    return $t->$tblKey;
}, $tables);

$matching = array_filter($tableNames, function($n) {
    return str_contains($n, 'var') || str_contains($n, 'gift') || str_contains($n, 'stock');
});

echo "MATCHING TABLES:\n" . implode("\n", $matching) . "\n";
