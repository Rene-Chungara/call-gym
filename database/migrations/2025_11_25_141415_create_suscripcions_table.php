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
        Schema::create('suscripcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');   // 1 usuario – N suscripciones
            $table->foreignId('membresia_id')->constrained('membresia');// 1 membresia – N suscripciones

            $table->date('fecha_inicio'); 
            $table->date('fecha_fin');    
            $table->integer('estado');   // 0 = inactivo, 1 = activo
            $table->date('fecha_estado'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripcion');
    }
};
