<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarcodeFieldsToPurchaseOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('product_id');
            $table->string('item_code')->nullable()->after('barcode');
            $table->string('value')->nullable()->after('item_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'item_code', 'value']);
        });
    }
}
