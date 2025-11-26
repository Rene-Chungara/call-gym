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
        Schema::create('asistencia_sesion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_paquete_id')->constrained('venta_paquete');
            $table->foreignId('rutina_sesion_id')->nullable()->constrained('rutina_sesion');
            $table->integer('numero_sesion');
            $table->date('fecha_asistencia');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->enum('estado', ['asistio', 'no_asistio', 'cancelada'])->default('asistio');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('venta_paquete_id');
            $table->index('fecha_asistencia');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_sesion');
    }
};
