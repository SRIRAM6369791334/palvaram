<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$data = [
    [
        'badge_text' => 'Weekend Discount',
        'main_title' => 'Daily Needs Items',
        'highlight_text' => 'Best Quality',
        'bg_image' => 'web_images/0MEO2uwOvWE6hSFgrSlw7ZCW1vv8i2RwUtB8OoQf.png',
        'link_url' => 'shop',
        'sort_order' => 1,
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'badge_text' => 'Weekend Discount',
        'main_title' => 'Fresh Vegetables',
        'highlight_text' => 'Organic',
        'bg_image' => 'web_images/LbxtpvY2aaObbWQiefsBP15LiZZ6hKqquD6ZurNM.png',
        'link_url' => 'shop',
        'sort_order' => 2,
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'badge_text' => 'Weekend Discount',
        'main_title' => 'Healthy Snacks',
        'highlight_text' => 'Low Calorie',
        'bg_image' => 'web_images/Sv2UzNCNk48OvihR69EWJNVmcJZGdj53kpP8LWHz.webp',
        'link_url' => 'shop',
        'sort_order' => 3,
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'badge_text' => 'Weekend Discount',
        'main_title' => 'Summer Drinks',
        'highlight_text' => 'Cooling',
        'bg_image' => 'web_images/RaJ8DBX9bcPULvGPjkmv1x8kAvU3mLQzFiHQv86V.png',
        'link_url' => 'shop',
        'sort_order' => 4,
        'status' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]
];

DB::table('home_promotions')->truncate();
DB::table('home_promotions')->insert($data);

echo "Seed data added successfully!\n";
