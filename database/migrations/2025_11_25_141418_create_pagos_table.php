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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suscripcion_id')->nullable()->constrained('suscripcion');
            $table->decimal('monto_abonado', 10, 2);
            $table->decimal('monto_total_membresia', 10, 2);
            $table->date('fecha_abono');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta'])->default('efectivo');
            $table->boolean('estado_pago')->default(true);
            
            // Campos para Stripe (pago con tarjeta)
            $table->string('stripe_payment_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_status')->nullable();
            
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('suscripcion_id');
            $table->index('fecha_abono');
            $table->index('estado_pago');
            $table->index('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
