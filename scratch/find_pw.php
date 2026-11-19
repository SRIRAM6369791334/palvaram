<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = \App\Models\User::all();
foreach ($users as $u) {
    foreach (['admin@2025', 'password', '12345678', '123456', '123456789', 'admin123', 'test@123', 'admin', 'password123'] as $pw) {
        if (\Illuminate\Support\Facades\Hash::check($pw, $u->password)) {
            echo "User: " . $u->email . " | Password: " . $pw . "\n";
            break;
        }
    }
}
