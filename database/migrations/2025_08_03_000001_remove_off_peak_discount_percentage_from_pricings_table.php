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
        if (Schema::hasTable('pricings') && Schema::hasColumn('pricings', 'off_peak_discount_percentage')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->dropColumn('off_peak_discount_percentage');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pricings') && !Schema::hasColumn('pricings', 'off_peak_discount_percentage')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->decimal('off_peak_discount_percentage', 5, 2)->nullable();
            });
        }
    }
};
