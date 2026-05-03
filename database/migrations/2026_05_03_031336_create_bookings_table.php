<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->restrictOnDelete();
            $table->string('qr_token')->unique();
            $table->enum('status', [
                'pending', 'confirmed', 'checked_in',
                'no_show', 'cancelled', 'completed',
            ])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->decimal('deposit_amount', 10, 2);
            $table->decimal('balance_due', 10, 2);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->enum('payment_method', ['yape', 'plin', 'tarjeta', 'efectivo'])->nullable();
            $table->boolean('is_walkin')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('qr_token');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
