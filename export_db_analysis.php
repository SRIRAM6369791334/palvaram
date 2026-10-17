<?php
// Database Schema & Tree Map Exporter
// Reads .env files and connects to MySQL

function parseEnv($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            // Remove wrapping quotes
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }
            $data[$key] = $val;
        }
    }
    return $data;
}

$env1 = parseEnv(__DIR__ . '/palVarnan/.env');
$env2 = parseEnv(__DIR__ . '/palVarnanDashboard/.env');

echo "palVarnan DB config: " . ($env1['DB_CONNECTION'] ?? 'none') . "://" . ($env1['DB_USERNAME'] ?? '') . "@" . ($env1['DB_HOST'] ?? '') . ":" . ($env1['DB_PORT'] ?? '') . "/" . ($env1['DB_DATABASE'] ?? '') . "\n";
echo "palVarnanDashboard DB config: " . ($env2['DB_CONNECTION'] ?? 'none') . "://" . ($env2['DB_USERNAME'] ?? '') . "@" . ($env2['DB_HOST'] ?? '') . ":" . ($env2['DB_PORT'] ?? '') . "/" . ($env2['DB_DATABASE'] ?? '') . "\n";

$host = $env1['DB_HOST'] ?? '127.0.0.1';
$port = $env1['DB_PORT'] ?? '3306';
$dbname = $env1['DB_DATABASE'] ?? 'palvarnan_db';
$user = $env1['DB_USERNAME'] ?? 'root';
$pass = $env1['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "SUCCESS: Connected to database '{$dbname}' successfully!\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Found " . count($tables) . " tables.\n";
    print_r($tables);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
