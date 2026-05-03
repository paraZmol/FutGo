<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->string('name');
            $table->enum('sport_type', ['futbol5', 'futbol7', 'futbol11']);
            $table->enum('surface', ['sintetico', 'natural'])->default('sintetico');
            $table->boolean('is_covered')->default(false);
            $table->json('amenities')->nullable();
            $table->enum('status', ['active', 'maintenance'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
