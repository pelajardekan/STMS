<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add column if the pricings table exists
        if (Schema::hasTable('pricings')) {
            Schema::table('pricings', function (Blueprint $table) {
                // Check if base_yearly_rate exists, if not add at the end
                if (Schema::hasColumn('pricings', 'base_yearly_rate')) {
                    $table->integer('rental_duration_months')->nullable()->after('base_yearly_rate');
                } else {
                    $table->integer('rental_duration_months')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Only drop column if the pricings table exists
        if (Schema::hasTable('pricings')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->dropColumn('rental_duration_months');
            });
        }
    }
}; 