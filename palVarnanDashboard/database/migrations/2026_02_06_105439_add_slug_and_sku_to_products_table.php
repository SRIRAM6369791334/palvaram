<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugAndSkuToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->unique()->after('product_name')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable()->after('category_id');
        });

        Schema::table('product_varient', function (Blueprint $table) {
            $table->string('sku')->unique()->after('product_id')->nullable();
            $table->string('barcode')->nullable()->after('sku');
            $table->unsignedBigInteger('unit_id')->nullable()->after('varient');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'brand_id']);
        });

        Schema::table('product_varient', function (Blueprint $table) {
            $table->dropColumn(['sku', 'barcode', 'unit_id']);
        });
    }
}
