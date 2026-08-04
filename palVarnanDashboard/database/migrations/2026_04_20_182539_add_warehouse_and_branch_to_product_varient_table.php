<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseAndBranchToProductVarientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_varient', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('subcategoryid');

            $table->unsignedBigInteger('warehouse_id')->nullable()->after('country_id');
            $table->unsignedBigInteger('branch_id')->nullable()->after('warehouse_id');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('warehouse_id')->references('id')->on('warehouse')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('warehouse_branch')->onDelete('set null');
 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_varient', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['branch_id']);

            $table->dropColumn(['country_id', 'warehouse_id', 'branch_id']);
       
        });
    }
}
