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
        Schema::table('suscripcion', function (Blueprint $table) {
            // Agregar columnas si no existen
            if (!Schema::hasColumn('suscripcion', 'estado_pago')) {
                $table->boolean('estado_pago')->default(false)->after('estado');
            }
            if (!Schema::hasColumn('suscripcion', 'tipo_pago')) {
                $table->enum('tipo_pago', ['contado', 'credito'])->default('contado')->after('estado_pago');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suscripcion', function (Blueprint $table) {
            if (Schema::hasColumn('suscripcion', 'estado_pago')) {
                $table->dropColumn('estado_pago');
            }
            if (Schema::hasColumn('suscripcion', 'tipo_pago')) {
                $table->dropColumn('tipo_pago');
            }
        });
    }
};
