<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToProductOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('product_orders', 'order_id')) {
                $table->string('order_id')->nullable()->after('order_number');
            }
            if (!Schema::hasColumn('product_orders', 'delivery_person_id')) {
                $table->unsignedBigInteger('delivery_person_id')->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('product_orders', 'is_delivery_assigned')) {
                $table->boolean('is_delivery_assigned')->default(0)->after('delivery_person_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_orders', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'delivery_person_id', 'is_delivery_assigned']);
        });
    }
}
