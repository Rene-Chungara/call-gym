<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Intentar eliminar el constraint de check si existe (Postgres)
        try {
            DB::statement("ALTER TABLE pagos DROP CONSTRAINT IF EXISTS pagos_metodo_pago_check");
        } catch (\Exception $e) {
            // Ignorar si falla (ej. MySQL no usa check constraints nombrados igual)
        }

        // Si es MySQL, modificar la columna enum
        // Si es Postgres, agregar el constraint nuevo

        // Detectar driver
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE pagos ADD CONSTRAINT pagos_metodo_pago_check CHECK (metodo_pago IN ('efectivo', 'tarjeta', 'qr'))");
        } else {
            // MySQL
            Schema::table('pagos', function (Blueprint $table) {
                $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'qr'])->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir es complicado sin perder datos 'qr', así que lo dejaremos así o simplificado.
    }
};
