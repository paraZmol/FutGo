<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['deposit', 'balance', 'refund', 'walkin']);
            $table->enum('payment_method', ['yape', 'plin', 'tarjeta', 'efectivo']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'refunded'])->default('approved');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
