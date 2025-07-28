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
        Schema::table('users', function (Blueprint $table) {
            $table->unique('phone_number', 'users_phone_number_unique');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->unique('IC_number', 'tenants_ic_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_phone_number_unique');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique('tenants_ic_number_unique');
        });
    }
}; 