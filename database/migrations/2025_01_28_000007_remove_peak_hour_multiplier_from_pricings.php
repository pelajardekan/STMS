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
        if (Schema::hasTable('pricings') && Schema::hasColumn('pricings', 'peak_hour_multiplier')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->dropColumn('peak_hour_multiplier');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pricings')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->decimal('peak_hour_multiplier', 3, 2)->default(1.0);
            });
        }
    }
}; 