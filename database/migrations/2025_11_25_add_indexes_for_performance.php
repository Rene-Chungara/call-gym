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
        // Índices para relaciones en suscripcion
        Schema::table('suscripcion', function (Blueprint $table) {
            if (!$this->indexExists('suscripcion', 'usuario_id')) {
                $table->index('usuario_id');
            }
            if (!$this->indexExists('suscripcion', 'membresia_id')) {
                $table->index('membresia_id');
            }
            if (!$this->indexExists('suscripcion', 'estado')) {
                $table->index('estado');
            }
            if (!$this->indexExists('suscripcion', 'fecha_inicio')) {
                $table->index('fecha_inicio');
            }
            if (!$this->indexExists('suscripcion', 'fecha_fin')) {
                $table->index('fecha_fin');
            }
        });

        // Índices para relaciones en pagos
        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'suscripcion_id') && !$this->indexExists('pagos', 'suscripcion_id')) {
                $table->index('suscripcion_id');
            }
            if (!$this->indexExists('pagos', 'estado_pago')) {
                $table->index('estado_pago');
            }
            if (!$this->indexExists('pagos', 'fecha_abono')) {
                $table->index('fecha_abono');
            }
        });

        // Índices para relaciones en detalle_pago
        Schema::table('detalle_pago', function (Blueprint $table) {
            if (!$this->indexExists('detalle_pago', 'pago_id')) {
                $table->index('pago_id');
            }
            if (!$this->indexExists('detalle_pago', 'suscripcion_id')) {
                $table->index('suscripcion_id');
            }
        });

        // Índices para usuarios
        Schema::table('usuarios', function (Blueprint $table) {
            if (!$this->indexExists('usuarios', 'email')) {
                $table->index('email');
            }
            if (!$this->indexExists('usuarios', 'is_propietario')) {
                $table->index('is_propietario');
            }
            if (!$this->indexExists('usuarios', 'is_secretaria')) {
                $table->index('is_secretaria');
            }
            if (!$this->indexExists('usuarios', 'is_instructor')) {
                $table->index('is_instructor');
            }
            if (!$this->indexExists('usuarios', 'is_clientes')) {
                $table->index('is_clientes');
            }
        });
    }

    private function indexExists($table, $column)
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if (in_array($column, $index['columns'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suscripcion', function (Blueprint $table) {
            $table->dropIndex(['usuario_id']);
            $table->dropIndex(['membresia_id']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['fecha_inicio']);
            $table->dropIndex(['fecha_fin']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'suscripcion_id')) {
                $table->dropIndex(['suscripcion_id']);
            }
            $table->dropIndex(['estado_pago']);
            $table->dropIndex(['fecha_abono']);
        });

        Schema::table('detalle_pago', function (Blueprint $table) {
            $table->dropIndex(['pago_id']);
            $table->dropIndex(['suscripcion_id']);
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['is_propietario']);
            $table->dropIndex(['is_secretaria']);
            $table->dropIndex(['is_instructor']);
            $table->dropIndex(['is_clientes']);
        });
    }
};
