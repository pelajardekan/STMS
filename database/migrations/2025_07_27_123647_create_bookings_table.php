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
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('booking_id');
            $table->unsignedBigInteger('booking_request_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('duration_type', 50);
            $table->integer('duration');
            $table->enum('status', ['active', 'completed', 'cancelled']);
            $table->timestamps();

            $table->foreign('booking_request_id')->references('booking_request_id')->on('booking_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
