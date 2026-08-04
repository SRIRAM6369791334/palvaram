<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_batches', function (Blueprint $table) {
            $table->id();
             $table->foreignId('product_stock_id')
                ->constrained('productstocks')
                ->cascadeOnDelete();

            $table->string('batch_no')->nullable();
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->integer('quantity')->default(0);
            $table->integer('remaining_qty')->default(0);
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
        Schema::dropIfExists('product_stock_batches');
    }
}
