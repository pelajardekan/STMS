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
        Schema::create('property_unit_parameters', function (Blueprint $table) {
            $table->bigIncrements('pup_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('pricing_id');
            $table->unsignedBigInteger('amenity_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('property_id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('unit_id')->on('units')->onDelete('set null');
            $table->foreign('pricing_id')->references('pricing_id')->on('pricings')->onDelete('cascade');
            $table->foreign('amenity_id')->references('amenity_id')->on('amenities')->onDelete('cascade');
            $table->foreign('service_id')->references('service_id')->on('services')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_unit_parameters');
    }
};
