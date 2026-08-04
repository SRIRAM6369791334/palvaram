<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfferPriceToProductStockBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_stock_batches', function (Blueprint $table) {
            $table->decimal('offer_price', 10, 2)->nullable()->after('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_stock_batches', function (Blueprint $table) {
            $table->dropColumn('offer_price');
        });
    }
}
