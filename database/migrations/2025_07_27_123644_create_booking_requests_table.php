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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->bigIncrements('booking_request_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('duration_type', 50);
            $table->integer('duration');
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('tenant_id')->on('tenants')->onDelete('cascade');
            $table->foreign('property_id')->references('property_id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('unit_id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
