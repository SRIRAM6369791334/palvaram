<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure reviews.title and reviews.approval exist on TiDB production.
     * Safe & idempotent: uses separate ALTER TABLE statements for TiDB compatibility.
     */
    public function up(): void
    {
        if (!Schema::hasTable('reviews')) {
            return;
        }

        if (!Schema::hasColumn('reviews', 'title')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->string('title')->nullable()->after('ratings');
            });
        }

        if (!Schema::hasColumn('reviews', 'approval')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->tinyInteger('approval')->default(0)->after('title')->comment('0=Pending, 1=Approved, 2=Rejected');
            });

            DB::table('reviews')->whereNull('approval')->orWhere('approval', 0)->update(['approval' => 1]);
        }
    }

    public function down(): void
    {
        // Columns are owned by 2026_11_20_121000_add_title_and_approval_to_reviews_table.
    }
};
