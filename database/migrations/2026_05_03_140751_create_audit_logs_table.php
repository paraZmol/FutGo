<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');                    // PARTNER_APROBADO, BOOKING_REVERTIDO, etc.
            $table->string('target_type')->nullable();   // App\Models\Venue
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('payload')->nullable();         // estado antes y después
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent(); // INMUTABLE — sin updated_at

            $table->index('action');
            $table->index(['target_type', 'target_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
