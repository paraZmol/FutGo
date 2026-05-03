<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');       // quien reclama
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', ['noshow', 'reembolso', 'pago', 'otro']);
            $table->enum('estado', ['abierta', 'en_revision', 'resuelta', 'rechazada'])->default('abierta');
            $table->enum('prioridad', ['alta', 'media', 'baja'])->default('alta');
            $table->text('motivo');
            $table->text('resolucion')->nullable();
            $table->decimal('monto_reclamado', 10, 2)->default(0);
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
