<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('requests') && !Schema::hasColumn('requests', 'franchise_promo_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->uuid('franchise_promo_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('requests') && Schema::hasColumn('requests', 'franchise_promo_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropColumn('franchise_promo_id');
            });
        }
    }
};
