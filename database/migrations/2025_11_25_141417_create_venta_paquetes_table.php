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
        Schema::create('venta_paquete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios'); // 1 usuario – N ventas
            $table->foreignId('paquete_id')->constrained('paquete');  // 1 paquete – N ventas
            $table->integer('sesiones_restantes');
            $table->date('fecha_compra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_paquete');
    }
};
