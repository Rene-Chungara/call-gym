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
        Schema::create('rutina_sesion_ejercicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_sesion_id')->constrained('rutina_sesion');
            $table->foreignId('ejercicio_id')->constrained('ejercicios');
            $table->integer('orden');
            $table->integer('series');
            $table->integer('repeticiones');
            $table->decimal('peso_estimado', 10, 2)->nullable();
            $table->integer('descanso_segundos')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->unique(['rutina_sesion_id', 'ejercicio_id']);
            $table->index('rutina_sesion_id');
            $table->index('ejercicio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rutina_sesion_ejercicio');
    }
};
