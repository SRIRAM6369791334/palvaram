<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeColorToProductVarientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_varient', function (Blueprint $table) {
            $table->unsignedBigInteger('size_id')->nullable()->after('unit_id');
            $table->unsignedBigInteger('color_id')->nullable()->after('size_id');
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
            $table->dropColumn(['size_id', 'color_id']);
        });
    }
}
