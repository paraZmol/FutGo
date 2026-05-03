<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS — campos de notificaciones y auditoría
        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('avatar_url');
            $table->boolean('push_opt_in')->default(true)->after('fcm_token');
            $table->boolean('whatsapp_opt_in')->default(false)->after('push_opt_in');
            $table->dateTime('last_login_at')->nullable()->after('whatsapp_opt_in');
            $table->softDeletes(); // eliminación lógica
        });

        // VENUES — auditoría de aprobación + soft delete
        Schema::table('venues', function (Blueprint $table) {
            $table->dateTime('approved_at')->nullable()->after('status');
            $table->softDeletes();
        });

        // FIELDS — soft delete
        Schema::table('fields', function (Blueprint $table) {
            $table->softDeletes();
        });

        // BOOKINGS — campos de seguimiento operativo
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable()->after('user_id');
            $table->dateTime('checked_in_at')->nullable()->after('payment_method');
            $table->dateTime('no_show_at')->nullable()->after('checked_in_at');
            $table->dateTime('cancelled_at')->nullable()->after('no_show_at');
            $table->foreign('staff_id')->references('id')->on('users')->nullOnDelete();
        });

        // TRANSACTIONS — campos de pasarela de pago
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('id');           // mercadopago, yape, plin
            $table->string('gateway_reference')->nullable()->after('gateway'); // ID externo de la tx
            $table->string('currency', 3)->default('PEN')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fcm_token', 'push_opt_in', 'whatsapp_opt_in', 'last_login_at']);
            $table->dropSoftDeletes();
        });
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('approved_at');
            $table->dropSoftDeletes();
        });
        Schema::table('fields', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['staff_id', 'checked_in_at', 'no_show_at', 'cancelled_at']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'gateway_reference', 'currency']);
        });
    }
};
