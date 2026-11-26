<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }

        // Separate Schema::table calls: TiDB rejects referencing a column
        // added in the same ALTER TABLE via ->after('title').
        if (!Schema::hasColumn('reviews', 'title')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('title')->nullable()->after('ratings');
            });
        }

        if (!Schema::hasColumn('reviews', 'approval')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->tinyInteger('approval')->default(0)->after('title')->comment('0=Pending, 1=Approved, 2=Rejected');
            });
        }

        // Set existing reviews to approved
        DB::table('reviews')->whereNull('approval')->orWhere('approval', 0)->update(['approval' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('reviews', 'approval')) {
                $table->dropColumn('approval');
            }
        });
    }
};
