<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryIdToProductsAndVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
            // Assuming countries table exists with id
            // $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });

        Schema::table('product_varient', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('id');
            // $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });

        Schema::table('product_varient', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
    }
}
