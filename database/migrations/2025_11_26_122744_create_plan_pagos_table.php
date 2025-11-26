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
        Schema::create('plan_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suscripcion_id')->constrained('suscripcion');
            $table->decimal('monto_total', 10, 2);
            $table->integer('cantidad_cuotas');
            $table->enum('estado', ['activo', 'completado', 'cancelado'])->default('activo');
            $table->date('fecha_inicio');
            $table->timestamps();
            
            $table->index('suscripcion_id');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_pagos');
    }
};
