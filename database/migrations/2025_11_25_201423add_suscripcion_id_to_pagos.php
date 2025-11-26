<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Agregar columna suscripcion_id si no existe
            if (!Schema::hasColumn('pagos', 'suscripcion_id')) {
                $table->foreignId('suscripcion_id')
                    ->nullable()
                    ->constrained('suscripcion')
                    ->onDelete('cascade');
            }

            // Agregar columna stripe_status si no existe
            if (!Schema::hasColumn('pagos', 'stripe_status')) {
                $table->string('stripe_status')->nullable()->default('pending');
            }

            // Cambiar tipos de datos a decimal para precisión monetaria
            if (Schema::hasColumn('pagos', 'monto_abonado')) {
                $table->decimal('monto_abonado', 10, 2)->change();
            }
            if (Schema::hasColumn('pagos', 'saldo_pendiente')) {
                $table->decimal('saldo_pendiente', 10, 2)->change();
            }
            if (Schema::hasColumn('pagos', 'monto_total_membresia')) {
                $table->decimal('monto_total_membresia', 10, 2)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'suscripcion_id')) {
                $table->dropForeign(['suscripcion_id']);
                $table->dropColumn('suscripcion_id');
            }

            if (Schema::hasColumn('pagos', 'stripe_status')) {
                $table->dropColumn('stripe_status');
            }
        });
    }
};
