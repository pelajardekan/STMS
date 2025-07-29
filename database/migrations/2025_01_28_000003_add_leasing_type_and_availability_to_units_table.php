<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                $table->string('leasing_type', 20)->default('rental')->after('description');
                $table->string('availability', 20)->default('available')->after('leasing_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('units')) {
            Schema::table('units', function (Blueprint $table) {
                $table->dropColumn(['leasing_type', 'availability']);
            });
        }
    }
}; 