<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsGiftToGiftTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_gift')->default(0)->after('status');
        });
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->boolean('is_gift')->default(0)->after('status');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_gift')->default(0)->after('approval_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_gift');
        });
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn('is_gift');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_gift');
        });
    }
}
