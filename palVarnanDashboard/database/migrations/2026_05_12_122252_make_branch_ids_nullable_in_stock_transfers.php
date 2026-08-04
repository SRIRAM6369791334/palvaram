<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeBranchIdsNullableInStockTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('from_branch_id')->nullable()->change();
            $table->unsignedBigInteger('to_branch_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('from_branch_id')->nullable(false)->change();
            $table->unsignedBigInteger('to_branch_id')->nullable(false)->change();
        });
    }
}
