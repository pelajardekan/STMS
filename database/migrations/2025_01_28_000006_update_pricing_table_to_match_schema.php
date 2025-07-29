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
        if (Schema::hasTable('pricings')) {
            Schema::table('pricings', function (Blueprint $table) {
                // Add missing columns that should be in the pricing table
                
                // Check and add base_hourly_rate if it doesn't exist
                if (!Schema::hasColumn('pricings', 'base_hourly_rate')) {
                    $table->decimal('base_hourly_rate', 10, 2)->nullable();
                }
                
                // Check and add base_daily_rate if it doesn't exist
                if (!Schema::hasColumn('pricings', 'base_daily_rate')) {
                    $table->decimal('base_daily_rate', 10, 2)->unsigned()->nullable();
                }
                
                // Check and add base_monthly_rate if it doesn't exist
                if (!Schema::hasColumn('pricings', 'base_monthly_rate')) {
                    $table->decimal('base_monthly_rate', 10, 2)->nullable();
                }
                
                // Check and add base_yearly_rate if it doesn't exist
                if (!Schema::hasColumn('pricings', 'base_yearly_rate')) {
                    $table->decimal('base_yearly_rate', 10, 2)->nullable();
                }
                
                // Check and add rental_duration_months if it doesn't exist
                if (!Schema::hasColumn('pricings', 'rental_duration_months')) {
                    $table->integer('rental_duration_months')->nullable();
                }
                
                // Check and add daily_hours_threshold if it doesn't exist
                if (!Schema::hasColumn('pricings', 'daily_hours_threshold')) {
                    $table->integer('daily_hours_threshold')->nullable();
                }
                
                // Check and add daily_discount_percentage if it doesn't exist
                if (!Schema::hasColumn('pricings', 'daily_discount_percentage')) {
                    $table->decimal('daily_discount_percentage', 5, 2)->nullable();
                }
                
                // Check and add educational_discount_percentage if it doesn't exist
                if (!Schema::hasColumn('pricings', 'educational_discount_percentage')) {
                    $table->decimal('educational_discount_percentage', 5, 2)->nullable();
                }
                
                // Check and add corporate_discount_percentage if it doesn't exist
                if (!Schema::hasColumn('pricings', 'corporate_discount_percentage')) {
                    $table->decimal('corporate_discount_percentage', 5, 2)->nullable();
                }
                
                // Check and add student_discount_percentage if it doesn't exist
                if (!Schema::hasColumn('pricings', 'student_discount_percentage')) {
                    $table->decimal('student_discount_percentage', 5, 2)->nullable();
                }
                

                
                // Check and add off_peak_discount_percentage if it doesn't exist
                if (!Schema::hasColumn('pricings', 'off_peak_discount_percentage')) {
                    $table->decimal('off_peak_discount_percentage', 5, 2)->nullable();
                }
                
                // Check and add minimum_booking_hours if it doesn't exist
                if (!Schema::hasColumn('pricings', 'minimum_booking_hours')) {
                    $table->integer('minimum_booking_hours')->nullable();
                }
                
                // Check and add maximum_booking_hours if it doesn't exist
                if (!Schema::hasColumn('pricings', 'maximum_booking_hours')) {
                    $table->integer('maximum_booking_hours')->nullable();
                }
                
                // Check and add special_rates if it doesn't exist
                if (!Schema::hasColumn('pricings', 'special_rates')) {
                    $table->json('special_rates')->nullable();
                }
                
                // Check and add is_active if it doesn't exist
                if (!Schema::hasColumn('pricings', 'is_active')) {
                    $table->tinyInteger('is_active')->default(1);
                }
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
                // Drop all the columns we added
                $columnsToDrop = [
                    'base_hourly_rate',
                    'base_daily_rate',
                    'base_monthly_rate',
                    'base_yearly_rate',
                    'rental_duration_months',
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
                ];
                
                foreach ($columnsToDrop as $column) {
                    if (Schema::hasColumn('pricings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
}; 