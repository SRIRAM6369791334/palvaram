<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_stock_id');
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('from_branch_id');
            $table->unsignedBigInteger('to_warehouse_id');
            $table->unsignedBigInteger('to_branch_id');
            $table->integer('quantity');
            $table->string('transfer_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transfers');
    }
}
