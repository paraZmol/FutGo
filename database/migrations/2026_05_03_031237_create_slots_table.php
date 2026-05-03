<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('status', [
                'available',
                'pending_payment',
                'reserved',
                'event_occupied',
                'completed',
                'expired',
            ])->default('available');
            $table->decimal('unit_price', 10, 2);
            $table->dateTime('lock_expires_at')->nullable();
            $table->timestamps();

            $table->index(['field_id', 'starts_at']);
            $table->index('status');
            $table->index('lock_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
