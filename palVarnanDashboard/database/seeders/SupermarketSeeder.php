<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SupermarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            ['unit_name' => 'Kilogram', 'short_name' => 'kg'],
            ['unit_name' => 'Gram', 'short_name' => 'g'],
            ['unit_name' => 'Litre', 'short_name' => 'l'],
            ['unit_name' => 'Millilitre', 'short_name' => 'ml'],
            ['unit_name' => 'Pieces', 'short_name' => 'pcs'],
            ['unit_name' => 'Packet', 'short_name' => 'pkt'],
        ];

        foreach ($units as $unit) {
            \App\Models\Unit::firstOrCreate(['short_name' => $unit['short_name']], $unit);
        }

        $brands = [
            ['brand_name' => 'Nestle', 'brand_slug' => 'nestle'],
            ['brand_name' => 'P&G', 'brand_slug' => 'p-g'],
            ['brand_name' => 'Unilever', 'brand_slug' => 'unilever'],
            ['brand_name' => 'Amul', 'brand_slug' => 'amul'],
            ['brand_name' => 'Tata', 'brand_slug' => 'tata'],
        ];

        foreach ($brands as $brand) {
            \App\Models\Brand::firstOrCreate(['brand_slug' => $brand['brand_slug']], $brand);
        }
    }
}
