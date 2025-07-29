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
        // Convert properties table enum to varchar
        if (Schema::hasTable('properties') && Schema::hasColumn('properties', 'status')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('status', 20)->default('active')->change();
            });
        }

        // Convert users table enum to varchar
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('tenant')->change();
            });
        }

        // Convert units table enum to varchar
        if (Schema::hasTable('units') && Schema::hasColumn('units', 'status')) {
            Schema::table('units', function (Blueprint $table) {
                $table->string('status', 20)->default('available')->change();
            });
        }

        // Convert pricings table enum to varchar
        if (Schema::hasTable('pricings') && Schema::hasColumn('pricings', 'pricing_type')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->string('pricing_type', 20)->change();
            });
        }

        // Convert rentals table enum to varchar
        if (Schema::hasTable('rentals') && Schema::hasColumn('rentals', 'status')) {
            Schema::table('rentals', function (Blueprint $table) {
                $table->string('status', 20)->default('active')->change();
            });
        }

        // Convert bookings table enum to varchar
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('status', 20)->default('active')->change();
            });
        }

        // Convert rental_requests table enum to varchar
        if (Schema::hasTable('rental_requests') && Schema::hasColumn('rental_requests', 'status')) {
            Schema::table('rental_requests', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->change();
            });
        }

        // Convert booking_requests table enum to varchar
        if (Schema::hasTable('booking_requests') && Schema::hasColumn('booking_requests', 'status')) {
            Schema::table('booking_requests', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->change();
            });
        }

        // Convert invoices table enum to varchar
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'status')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('status', 20)->default('unpaid')->change();
            });
        }

        // Convert payments table enum to varchar
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'status')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert properties table varchar to enum
        if (Schema::hasTable('properties') && Schema::hasColumn('properties', 'status')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->change();
            });
        }

        // Revert users table varchar to enum
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin', 'tenant'])->default('tenant')->change();
            });
        }

        // Revert units table varchar to enum
        if (Schema::hasTable('units') && Schema::hasColumn('units', 'status')) {
            Schema::table('units', function (Blueprint $table) {
                $table->enum('status', ['available', 'unavailable'])->default('available')->change();
            });
        }

        // Revert pricings table varchar to enum
        if (Schema::hasTable('pricings') && Schema::hasColumn('pricings', 'pricing_type')) {
            Schema::table('pricings', function (Blueprint $table) {
                $table->enum('pricing_type', ['rental', 'booking'])->change();
            });
        }

        // Revert rentals table varchar to enum
        if (Schema::hasTable('rentals') && Schema::hasColumn('rentals', 'status')) {
            Schema::table('rentals', function (Blueprint $table) {
                $table->enum('status', ['active', 'completed', 'terminated'])->default('active')->change();
            });
        }

        // Revert bookings table varchar to enum
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('status', ['active', 'completed', 'cancelled'])->default('active')->change();
            });
        }

        // Revert rental_requests table varchar to enum
        if (Schema::hasTable('rental_requests') && Schema::hasColumn('rental_requests', 'status')) {
            Schema::table('rental_requests', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
            });
        }

        // Revert booking_requests table varchar to enum
        if (Schema::hasTable('booking_requests') && Schema::hasColumn('booking_requests', 'status')) {
            Schema::table('booking_requests', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
            });
        }

        // Revert invoices table varchar to enum
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'status')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid')->change();
            });
        }

        // Revert payments table varchar to enum
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'status')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('status', ['completed', 'failed', 'pending'])->default('pending')->change();
            });
        }
    }
}; 