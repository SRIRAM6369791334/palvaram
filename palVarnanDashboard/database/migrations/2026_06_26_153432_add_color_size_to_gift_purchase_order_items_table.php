<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorSizeToGiftPurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_purchase_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('color_id')->nullable()->after('unit_id');
            $table->unsignedBigInteger('size_id')->nullable()->after('color_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['color_id', 'size_id']);
        });
    }
}
