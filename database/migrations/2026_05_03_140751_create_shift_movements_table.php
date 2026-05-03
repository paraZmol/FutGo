<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_log_id')->constrained('shift_logs')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->enum('type', ['checkin', 'walkin', 'noshow_retention', 'manual']);
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent(); // sin updated_at — registro histórico
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_movements');
    }
};
