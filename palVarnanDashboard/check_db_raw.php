<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=palvarnan_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = [
        'product_orders',
        'product_slots',
        'product_order_items',
        'product_order_user_addresses',
        'user_addresses',
        'product_transaction_logs',
        'product_tracking',
        'product_refunds',
        'productstocks',
        'product_varient',
        'carts',
        'users',
        'coupons',
        'states',
        'districts',
        'countries'
    ];

    $result = [];
    foreach ($tables as $t) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$t'");
        if ($stmt->rowCount() > 0) {
            $cols = $pdo->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_ASSOC);
            $result[$t] = [
                'exists' => true,
                'columns' => array_map(function($c) {
                    return $c['Field'] . ' (' . $c['Type'] . ')';
                }, $cols)
            ];
        } else {
            $result[$t] = ['exists' => false];
        }
    }

    file_put_contents(__DIR__ . '/db_structure.json', json_encode($result, JSON_PRETTY_PRINT));
    echo "SUCCESS: db_structure.json written.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
