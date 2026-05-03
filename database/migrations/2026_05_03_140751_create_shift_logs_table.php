<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // staff
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('expected_cash', 10, 2)->nullable();   // calculado al cerrar
            $table->decimal('delivered_cash', 10, 2)->nullable();  // lo que entrega el staff
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['venue_id', 'opened_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_logs');
    }
};
