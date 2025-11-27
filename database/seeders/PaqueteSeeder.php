<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('paquete')->insert([
            [
                'nombre' => 'Paquete Básico',
                'precio' => 100.00,
                'num_sesiones' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Paquete Estándar',
                'precio' => 180.00,
                'num_sesiones' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Paquete Premium',
                'precio' => 320.00,
                'num_sesiones' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Paquete Intensivo',
                'precio' => 500.00,
                'num_sesiones' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Paquete Principiante',
                'precio' => 60.00,
                'num_sesiones' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Paquete Avanzado',
                'precio' => 650.00,
                'num_sesiones' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
