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
                // Make pricing_id and amenity_id nullable
                if (Schema::hasColumn('property_unit_parameters', 'pricing_id')) {
                    $table->unsignedBigInteger('pricing_id')->nullable()->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'amenity_id')) {
                    $table->unsignedBigInteger('amenity_id')->nullable()->change();
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
                // Revert back to not nullable
                if (Schema::hasColumn('property_unit_parameters', 'pricing_id')) {
                    $table->unsignedBigInteger('pricing_id')->nullable(false)->change();
                }
                if (Schema::hasColumn('property_unit_parameters', 'amenity_id')) {
                    $table->unsignedBigInteger('amenity_id')->nullable(false)->change();
                }
            });
        }
    }
}; 