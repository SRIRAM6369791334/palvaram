<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = \App\Models\User::where('email', 'test@gmail.com')->first();
if ($u) {
    echo "Email: " . $u->email . " | Has password: " . (!empty($u->password) ? 'YES' : 'NO') . "\n";
    // Check if password matches common test passwords
    foreach (['admin@2025', 'password', '12345678', '123456', 'test@123'] as $pw) {
        if (\Illuminate\Support\Facades\Hash::check($pw, $u->password)) {
            echo "Password matched: " . $pw . "\n";
            break;
        }
    }
}
