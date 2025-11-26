<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutina_sesion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rutina_id')->constrained('rutina');
            $table->integer('numero_sesion');
            $table->text('descripcion');
            $table->timestamps();
            
            $table->unique(['rutina_id', 'numero_sesion']);
            $table->index('rutina_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutina_sesion');
    }
};
