<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVarient;
use App\Models\SubCategory;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow
{
    protected $categoryId;
    protected $subcategoryId;
    protected $imagePath;
    protected $brandId;

    public function __construct($categoryId, $subcategoryId, $imagePath = null, $brandId = null)
    {
        $this->categoryId = $categoryId;
        $this->subcategoryId = $subcategoryId;
        $this->imagePath = $imagePath;
        $this->brandId = $brandId;
    }

    public function collection(Collection $rows)
    {
        // Group rows by product name to create products and their variants correctly
        $groupedProducts = $rows->groupBy('product_name');

        foreach ($groupedProducts as $productName => $variants) {
            if (empty($productName)) continue;

            $firstVariant = $variants->first();

            // Find or create Brand
            $brandId = $this->brandId;
            
            if (!$brandId && !empty($firstVariant['brand'])) {
                $brand = Brand::where('brand_name', 'LIKE', trim($firstVariant['brand']))->first();
                if (!$brand) {
                    $brandName = trim($firstVariant['brand']);
                    $brand = Brand::create([
                        'brand_name' => $brandName,
                        'brand_slug' => Str::slug($brandName) . '-' . time()
                    ]);
                }
                $brandId = $brand->id;
            }

            // Get Category and Subcategory Names
            $category = Category::find($this->categoryId);
            $subcategory = $this->subcategoryId ? SubCategory::find($this->subcategoryId) : null;
            $categoryName = $category ? $category->category_name : '';
            $subcategoryName = $subcategory ? $subcategory->subcategory_name : '';

            // Handle Product Image
            $productImagePath = null;
            if (!empty($firstVariant['product_image']) && $this->imagePath) {
                $productImagePath = $this->moveImage($firstVariant['product_image'], 'product_images');
            }

            // Create Product
            $product = Product::create([
                'category_id' => $this->categoryId,
                'brand_id' => $brandId,
                'product_name' => $productName,
                'slug' => Str::slug($productName) . '-' . time(),
                'product_description' => $firstVariant['product_ingredients'] ?? $firstVariant['ingredients'] ?? '',
                'product_specification' => $firstVariant['product_description'] ?? $firstVariant['description'] ?? '',
                'product_image' => $productImagePath,
                'subcategory_id' => $this->subcategoryId,
                'cate_name' => $categoryName,
                'subcate_name' => $subcategoryName,
            ]);

            foreach ($variants as $v) {
                // Find Unit
                $unitId = null;
                if (!empty($v['unit'])) {
                    $unit = Unit::where('unit_name', 'LIKE', trim($v['unit']))
                        ->orWhere('short_name', 'LIKE', trim($v['unit']))
                        ->first();
                    $unitId = $unit ? $unit->id : null;
                }

                // Handle Variant Image
                $variantImagePath = null;
                if (!empty($v['variant_image']) && $this->imagePath) {
                    $variantImagePath = $this->moveImage($v['variant_image'], 'varient_images');
                }

                $sku = !empty($v['sku']) ? $v['sku'] : (strtoupper(substr($productName, 0, 3)) . '-' . rand(1000, 9999));
                $barcode = !empty($v['barcode']) ? $v['barcode'] : (rand(10000000, 99999999) . rand(1000, 9999));

                // Create Product Variant
                $variant = ProductVarient::create([
                    'categoryid' => $this->categoryId,
                    'subcategoryid' => $this->subcategoryId,
                    'subcatename' => $subcategoryName,
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'barcode' => $barcode,
                    'unit_id' => $unitId,
                    'varient' => $v['variant_value'] ?? $v['value'] ?? '',
                    'varient_img' => $variantImagePath,
                    'varient_name' => $v['variant_name'] ?? '',
                    'value' => $v['variant_value'] ?? $v['value'] ?? '',
                    'offer_price' => $v['offer_price'] ?? 0,
                    'mrp_price' => $v['mrp_price'] ?? 0,
                    'product_qty' => $v['stock_quantity'] ?? $v['qty'] ?? 0,
                    'low_stock' => $v['low_stock'] ?? 5,
                    'product_gst' => $v['gst'] ?? 0,
                    'size_value' => 0, 
                ]);

                // Create Stock entry
                ProductStock::create([
                    "productid"             => $product->id,
                    "category_id"           => $this->categoryId,
                    "subcategory_id"        => $this->subcategoryId,
                    "pro_ver_id"            => $variant->id,
                    "productname"           => $productName,
                    "overallstock"          => $variant->product_qty,
                    "availablestock"        => $variant->product_qty,
                    "salestock"             => 0,
                    "low_stocks"            => $variant->low_stock,
                    "last_stockupdate_date" => date("Y-m-d"),
                ]);
            }
        }
    }

    protected function moveImage($filename, $folder)
    {
        $sourcePath = $this->imagePath . '/' . $filename;
        
        // If exact path doesn't exist, try to search for the filename recursively
        if (!File::exists($sourcePath)) {
            $allFiles = File::allFiles($this->imagePath);
            foreach ($allFiles as $file) {
                if ($file->getFilename() === $filename) {
                    $sourcePath = $file->getPathname();
                    break;
                }
            }
        }

        if (File::exists($sourcePath)) {
            $extension = File::extension($sourcePath);
            $newName = $folder . '/' . Str::random(10) . '_' . time() . '.' . $extension;
            Storage::disk('public')->put($newName, File::get($sourcePath));
            return $newName;
        }
        return null;
    }
}
