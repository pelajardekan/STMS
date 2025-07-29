<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'debit_card', 'online_payment'])->after('amount');
                $table->string('reference_number')->nullable()->after('payment_date');
                $table->text('notes')->nullable()->after('reference_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn(['payment_method', 'reference_number', 'notes']);
            });
        }
    }
}; 