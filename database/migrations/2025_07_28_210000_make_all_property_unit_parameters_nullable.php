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
        if (Schema::hasTable('property_unit_parameters')) {
            Schema::table('property_unit_parameters', function (Blueprint $table) {
                // Make all fields nullable
                if (Schema::hasColumn('property_unit_parameters', 'unit_id')) {
                    $table->unsignedBigInteger('unit_id')->nullable()->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'pricing_id')) {
                    $table->unsignedBigInteger('pricing_id')->nullable()->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'amenity_id')) {
                    $table->unsignedBigInteger('amenity_id')->nullable()->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'service_id')) {
                    $table->unsignedBigInteger('service_id')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('property_unit_parameters')) {
            Schema::table('property_unit_parameters', function (Blueprint $table) {
                // Revert back to original constraints
                if (Schema::hasColumn('property_unit_parameters', 'unit_id')) {
                    $table->unsignedBigInteger('unit_id')->nullable(false)->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'pricing_id')) {
                    $table->unsignedBigInteger('pricing_id')->nullable(false)->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'amenity_id')) {
                    $table->unsignedBigInteger('amenity_id')->nullable(false)->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'service_id')) {
                    $table->unsignedBigInteger('service_id')->nullable(false)->change();
                }
            });
        }
    }
}; 