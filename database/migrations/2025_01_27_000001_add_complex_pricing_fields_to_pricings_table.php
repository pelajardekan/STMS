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
        // Only add columns if the pricings table exists
        if (Schema::hasTable('pricings')) {
            Schema::table('pricings', function (Blueprint $table) {
                // Base pricing fields - add in sequence to avoid dependency issues
                $table->decimal('base_hourly_rate', 10, 2)->nullable();
                $table->decimal('base_daily_rate', 10, 2)->unsigned()->nullable();
                $table->decimal('base_monthly_rate', 10, 2)->nullable();
                $table->decimal('base_yearly_rate', 10, 2)->nullable();
                
                // Complex pricing fields
                $table->integer('daily_hours_threshold')->nullable();
                $table->decimal('daily_discount_percentage', 5, 2)->nullable();
                
                // Special rates for different customer types
                $table->decimal('educational_discount_percentage', 5, 2)->nullable();
                $table->decimal('corporate_discount_percentage', 5, 2)->nullable();
                $table->decimal('student_discount_percentage', 5, 2)->nullable();
                
                // Peak/off-peak pricing
                $table->decimal('off_peak_discount_percentage', 5, 2)->nullable();
                
                // Minimum booking requirements
                $table->integer('minimum_booking_hours')->nullable();
                $table->integer('maximum_booking_hours')->nullable();
                
                // Advanced settings
                $table->json('special_rates')->nullable();
                $table->tinyInteger('is_active')->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop columns if the pricings table exists
        if (Schema::hasTable('pricings')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->dropColumn([
                    'base_hourly_rate',
                    'base_daily_rate', 
                    'base_monthly_rate',
                    'base_yearly_rate',
                    'daily_hours_threshold',
                    'daily_discount_percentage',
                    'educational_discount_percentage',
                    'corporate_discount_percentage',
                    'student_discount_percentage',
                    'off_peak_discount_percentage',
                    'minimum_booking_hours',
                    'maximum_booking_hours',
                    'special_rates',
                    'is_active'
                ]);
            });
        }
    }
}; 