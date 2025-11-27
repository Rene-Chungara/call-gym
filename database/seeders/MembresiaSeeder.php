<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembresiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('membresia')->insert([
            [
                'nombre' => 'Membresía Mensual',
                'precio' => 150.00,
                'duracion_dias' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Membresía Trimestral',
                'precio' => 400.00,
                'duracion_dias' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Membresía Semestral',
                'precio' => 750.00,
                'duracion_dias' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Membresía Anual',
                'precio' => 1400.00,
                'duracion_dias' => 365,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Membresía Semanal',
                'precio' => 50.00,
                'duracion_dias' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Membresía Quincenal',
                'precio' => 85.00,
                'duracion_dias' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
