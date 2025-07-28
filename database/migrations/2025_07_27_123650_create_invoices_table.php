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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('invoice_id');
            $table->unsignedBigInteger('rental_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('issue_date');
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'paid', 'overdue']);
            $table->timestamps();

            $table->foreign('rental_id')->references('rental_id')->on('rentals')->onDelete('set null');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
