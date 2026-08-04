<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToStockDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_distributions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'cancelled'])
              ->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_distributions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
