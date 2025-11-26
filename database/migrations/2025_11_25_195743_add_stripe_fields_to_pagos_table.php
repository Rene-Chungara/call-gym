<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Nota: Esta migración es para compatibilidad con versiones anteriores.
     * Los campos de Stripe ya están incluidos en la tabla pagos desde su creación.
     * Si los campos ya existen, esta migración no hace nada.
     */
    public function up(): void
    {
        // Solo agregar si no existen (para compatibilidad con BD existentes)
        if (Schema::hasTable('pagos')) {
            Schema::table('pagos', function (Blueprint $table) {
                if (!Schema::hasColumn('pagos', 'stripe_payment_id')) {
                    $table->string('stripe_payment_id')->nullable();
                }
                if (!Schema::hasColumn('pagos', 'stripe_session_id')) {
                    $table->string('stripe_session_id')->nullable();
                }
                if (!Schema::hasColumn('pagos', 'stripe_status')) {
                    $table->string('stripe_status')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pagos')) {
            Schema::table('pagos', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('pagos', 'stripe_payment_id')) {
                    $columns[] = 'stripe_payment_id';
                }
                if (Schema::hasColumn('pagos', 'stripe_session_id')) {
                    $columns[] = 'stripe_session_id';
                }
                if (Schema::hasColumn('pagos', 'stripe_status')) {
                    $columns[] = 'stripe_status';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
