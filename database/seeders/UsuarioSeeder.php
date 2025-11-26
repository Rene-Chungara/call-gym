<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            // --- ADMIN ---
            [
                'nombre' => 'Admin Principal',
                'email' => 'admin@gym.com',
                'contrasena' => bcrypt('admin123'),
                'fecha_registro' => now(),
                'is_propietario' => true,
                'is_secretaria' => false,
                'is_instructor' => false,
                'is_clientes' => false,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- SECRETARIA ---
            [
                'nombre' => 'Secretaria 1',
                'email' => 'secretaria@gym.com',
                'contrasena' => bcrypt('secretaria123'),
                'fecha_registro' => now(),
                'is_propietario' => false,
                'is_secretaria' => true,
                'is_instructor' => false,
                'is_clientes' => false,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- INSTRUCTOR ---
            [
                'nombre' => 'Instructor 1',
                'email' => 'instructor@gym.com',
                'contrasena' => bcrypt('instructor123'),
                'fecha_registro' => now(),
                'is_propietario' => false,
                'is_secretaria' => false,
                'is_instructor' => true,
                'is_clientes' => false,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // --- CLIENTE ---
            [
                'nombre' => 'Cliente 1',
                'email' => 'cliente@gym.com',
                'contrasena' => bcrypt('cliente123'),
                'fecha_registro' => now(),
                'is_propietario' => false,
                'is_secretaria' => false,
                'is_instructor' => false,
                'is_clientes' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
