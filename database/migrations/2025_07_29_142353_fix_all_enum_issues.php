<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Convert payments table payment_method enum to varchar
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'payment_method')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('payment_method', 50)->change();
            });
        }

        // 2. Convert units table status enum to varchar first, then update values
        if (Schema::hasTable('units') && Schema::hasColumn('units', 'status')) {
            // First convert enum to varchar
            Schema::table('units', function (Blueprint $table) {
                $table->string('status', 20)->change();
            });
            
            // Then update the values
            DB::table('units')->where('status', 'available')->update(['status' => 'active']);
            DB::table('units')->where('status', 'unavailable')->update(['status' => 'unactive']);
        }

        // 3. Ensure properties table status is varchar (should already be done by previous migration)
        if (Schema::hasTable('properties') && Schema::hasColumn('properties', 'status')) {
            // The properties table should already be using varchar after the previous migration
            // But let's ensure it's varchar just in case
            Schema::table('properties', function (Blueprint $table) {
                $table->string('status', 20)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert payments table payment_method varchar to enum
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'payment_method')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'debit_card', 'online_payment'])->change();
            });
        }

        // 2. Revert unit status values and convert back to enum
        if (Schema::hasTable('units') && Schema::hasColumn('units', 'status')) {
            // First revert the values
            DB::table('units')->where('status', 'active')->update(['status' => 'available']);
            DB::table('units')->where('status', 'unactive')->update(['status' => 'unavailable']);
            
            // Then convert back to enum
            Schema::table('units', function (Blueprint $table) {
                $table->enum('status', ['available', 'unavailable'])->change();
            });
        }

        // 3. Revert properties table status to enum
        if (Schema::hasTable('properties') && Schema::hasColumn('properties', 'status')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->change();
            });
        }
    }
}; 