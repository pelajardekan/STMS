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
        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                // Add leasing_type if it doesn't exist
                if (!Schema::hasColumn('units', 'leasing_type')) {
                    $table->string('leasing_type', 20)->default('rental')->after('description');
                }
                
                // Add availability if it doesn't exist
                if (!Schema::hasColumn('units', 'availability')) {
                    $table->string('availability', 20)->default('available')->after('leasing_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                // Drop columns if they exist
                if (Schema::hasColumn('units', 'leasing_type')) {
                    $table->dropColumn('leasing_type');
                }
                
                if (Schema::hasColumn('units', 'availability')) {
                    $table->dropColumn('availability');
                }
            });
        }
    }
}; 