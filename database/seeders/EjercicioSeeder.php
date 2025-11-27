<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EjercicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ejercicios')->insert([
            // Pecho
            [
                'nombre' => 'Press de Banca',
                'descripcion' => 'Ejercicio fundamental para desarrollar el pecho, tríceps y hombros',
                'grupo_muscular' => 'Pecho',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Barra, Banco',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Flexiones',
                'descripcion' => 'Ejercicio de peso corporal para pecho y tríceps',
                'grupo_muscular' => 'Pecho',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Ninguno',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Aperturas con Mancuernas',
                'descripcion' => 'Ejercicio de aislamiento para el pecho',
                'grupo_muscular' => 'Pecho',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Mancuernas, Banco',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Espalda
            [
                'nombre' => 'Dominadas',
                'descripcion' => 'Ejercicio compuesto para espalda y bíceps',
                'grupo_muscular' => 'Espalda',
                'dificultad' => 'avanzado',
                'equipo_requerido' => 'Barra de dominadas',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Remo con Barra',
                'descripcion' => 'Ejercicio fundamental para el grosor de la espalda',
                'grupo_muscular' => 'Espalda',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Barra',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Peso Muerto',
                'descripcion' => 'Ejercicio compuesto que trabaja toda la cadena posterior',
                'grupo_muscular' => 'Espalda',
                'dificultad' => 'avanzado',
                'equipo_requerido' => 'Barra',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Piernas
            [
                'nombre' => 'Sentadillas',
                'descripcion' => 'Rey de los ejercicios de pierna',
                'grupo_muscular' => 'Piernas',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Barra, Rack',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Prensa de Piernas',
                'descripcion' => 'Ejercicio de empuje para cuádriceps y glúteos',
                'grupo_muscular' => 'Piernas',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Máquina de prensa',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Zancadas',
                'descripcion' => 'Ejercicio unilateral para piernas y glúteos',
                'grupo_muscular' => 'Piernas',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Mancuernas',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Curl de Piernas',
                'descripcion' => 'Ejercicio de aislamiento para femorales',
                'grupo_muscular' => 'Piernas',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Máquina de curl',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Hombros
            [
                'nombre' => 'Press Militar',
                'descripcion' => 'Ejercicio fundamental para hombros',
                'grupo_muscular' => 'Hombros',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Barra',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Elevaciones Laterales',
                'descripcion' => 'Ejercicio de aislamiento para deltoides laterales',
                'grupo_muscular' => 'Hombros',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Mancuernas',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Remo al Mentón',
                'descripcion' => 'Ejercicio para deltoides y trapecios',
                'grupo_muscular' => 'Hombros',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Barra o Mancuernas',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Brazos
            [
                'nombre' => 'Curl de Bíceps con Barra',
                'descripcion' => 'Ejercicio clásico para bíceps',
                'grupo_muscular' => 'Brazos',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Barra',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Fondos en Paralelas',
                'descripcion' => 'Ejercicio compuesto para tríceps y pecho',
                'grupo_muscular' => 'Brazos',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Barras paralelas',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Extensiones de Tríceps',
                'descripcion' => 'Ejercicio de aislamiento para tríceps',
                'grupo_muscular' => 'Brazos',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Mancuerna o Polea',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Core/Abdomen
            [
                'nombre' => 'Plancha',
                'descripcion' => 'Ejercicio isométrico para el core',
                'grupo_muscular' => 'Abdomen',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Ninguno',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Abdominales Crunch',
                'descripcion' => 'Ejercicio básico para abdominales',
                'grupo_muscular' => 'Abdomen',
                'dificultad' => 'principiante',
                'equipo_requerido' => 'Ninguno',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Elevaciones de Piernas',
                'descripcion' => 'Ejercicio para abdominales inferiores',
                'grupo_muscular' => 'Abdomen',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Barra de dominadas',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Russian Twist',
                'descripcion' => 'Ejercicio para oblicuos',
                'grupo_muscular' => 'Abdomen',
                'dificultad' => 'intermedio',
                'equipo_requerido' => 'Disco o Mancuerna',
                'imagen_url' => null,
                'video_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
