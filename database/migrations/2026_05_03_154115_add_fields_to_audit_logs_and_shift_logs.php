<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // audit_logs — trazabilidad forense completa
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('actor_role')->nullable()->after('user_id'); // user|partner|staff|admin
            $table->string('user_agent')->nullable()->after('ip_address');
        });

        // shift_logs — cuadre de caja con apertura y diferencia
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->decimal('opening_cash', 10, 2)->default(0.00)->after('user_id'); // efectivo al abrir turno
            $table->decimal('difference', 10, 2)->nullable()->after('delivered_cash'); // expected - delivered
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['actor_role', 'user_agent']);
        });
        Schema::table('shift_logs', function (Blueprint $table) {
            $table->dropColumn(['opening_cash', 'difference']);
        });
    }
};
