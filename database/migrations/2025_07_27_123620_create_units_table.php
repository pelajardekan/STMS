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
        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('unit_id');
            $table->unsignedBigInteger('property_id');
            $table->string('name', 100);
            $table->string('type', 50);
            $table->enum('status', ['available', 'unavailable']);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('property_id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
