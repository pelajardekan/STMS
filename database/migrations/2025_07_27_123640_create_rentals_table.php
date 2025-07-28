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
        Schema::create('rentals', function (Blueprint $table) {
            $table->bigIncrements('rental_id');
            $table->unsignedBigInteger('rental_request_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration');
            $table->enum('status', ['active', 'completed', 'terminated']);
            $table->timestamps();

            $table->foreign('rental_request_id')->references('rental_request_id')->on('rental_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
