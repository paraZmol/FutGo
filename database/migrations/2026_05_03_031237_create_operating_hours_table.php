<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operating_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=domingo ... 6=sábado
            $table->time('opens_at');
            $table->time('closes_at');
            $table->decimal('price_day', 10, 2);   // hasta las 18:00
            $table->decimal('price_night', 10, 2); // desde las 18:00
            $table->decimal('deposit_amount', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['field_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operating_hours');
    }
};
