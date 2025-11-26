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
        Schema::table('venta_paquete', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_paquete', 'sesiones_utilizadas')) {
                $table->integer('sesiones_utilizadas')->default(0);
            }
            if (!Schema::hasColumn('venta_paquete', 'fecha_ultima_asistencia')) {
                $table->date('fecha_ultima_asistencia')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta_paquete', function (Blueprint $table) {
            if (Schema::hasColumn('venta_paquete', 'sesiones_utilizadas')) {
                $table->dropColumn('sesiones_utilizadas');
            }
            if (Schema::hasColumn('venta_paquete', 'fecha_ultima_asistencia')) {
                $table->dropColumn('fecha_ultima_asistencia');
            }
        });
    }
};
