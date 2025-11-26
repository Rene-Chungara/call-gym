<?php

namespace Database\Seeders;

use App\Models\Ejercicio;
use Illuminate\Database\Seeder;

class EjercicioSeeder extends Seeder
{
    public function run(): void
    {
        $ejercicios = [
            // Pecho
            ['nombre' => 'Press de Banca', 'grupo_muscular' => 'Pecho', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio fundamental para el desarrollo del pecho', 'equipo_requerido' => 'Barra y Banco'],
            ['nombre' => 'Aperturas con Mancuernas', 'grupo_muscular' => 'Pecho', 'dificultad' => 'intermedio', 'descripcion' => 'Aislamiento de pecho con mancuernas', 'equipo_requerido' => 'Mancuernas'],
            ['nombre' => 'Press Inclinado', 'grupo_muscular' => 'Pecho', 'dificultad' => 'intermedio', 'descripcion' => 'Enfatiza la parte superior del pecho', 'equipo_requerido' => 'Barra y Banco Inclinado'],
            ['nombre' => 'Fondos en Paralelas', 'grupo_muscular' => 'Pecho', 'dificultad' => 'avanzado', 'descripcion' => 'Ejercicio de peso corporal para pecho y tríceps', 'equipo_requerido' => 'Paralelas'],
            
            // Espalda
            ['nombre' => 'Remo con Barra', 'grupo_muscular' => 'Espalda', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio fundamental para la espalda', 'equipo_requerido' => 'Barra'],
            ['nombre' => 'Dominadas', 'grupo_muscular' => 'Espalda', 'dificultad' => 'avanzado', 'descripcion' => 'Ejercicio de peso corporal para espalda', 'equipo_requerido' => 'Barra de Dominadas'],
            ['nombre' => 'Jalón Lateral', 'grupo_muscular' => 'Espalda', 'dificultad' => 'principiante', 'descripcion' => 'Aislamiento de espalda en máquina', 'equipo_requerido' => 'Máquina de Jalón'],
            ['nombre' => 'Remo Máquina', 'grupo_muscular' => 'Espalda', 'dificultad' => 'principiante', 'descripcion' => 'Remo en máquina para principiantes', 'equipo_requerido' => 'Máquina de Remo'],
            
            // Hombros
            ['nombre' => 'Press de Hombros', 'grupo_muscular' => 'Hombros', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio fundamental para hombros', 'equipo_requerido' => 'Barra o Mancuernas'],
            ['nombre' => 'Elevaciones Laterales', 'grupo_muscular' => 'Hombros', 'dificultad' => 'principiante', 'descripcion' => 'Aislamiento de deltoides laterales', 'equipo_requerido' => 'Mancuernas'],
            ['nombre' => 'Elevaciones Frontales', 'grupo_muscular' => 'Hombros', 'dificultad' => 'principiante', 'descripcion' => 'Aislamiento de deltoides frontales', 'equipo_requerido' => 'Mancuernas o Barra'],
            
            // Brazos - Bíceps
            ['nombre' => 'Curl de Bíceps', 'grupo_muscular' => 'Bíceps', 'dificultad' => 'principiante', 'descripcion' => 'Ejercicio fundamental para bíceps', 'equipo_requerido' => 'Mancuernas o Barra'],
            ['nombre' => 'Curl Predicador', 'grupo_muscular' => 'Bíceps', 'dificultad' => 'intermedio', 'descripcion' => 'Aislamiento de bíceps en banco predicador', 'equipo_requerido' => 'Banco Predicador y Barra'],
            ['nombre' => 'Curl Máquina', 'grupo_muscular' => 'Bíceps', 'dificultad' => 'principiante', 'descripcion' => 'Curl en máquina para principiantes', 'equipo_requerido' => 'Máquina de Curl'],
            
            // Brazos - Tríceps
            ['nombre' => 'Extensión de Tríceps', 'grupo_muscular' => 'Tríceps', 'dificultad' => 'principiante', 'descripcion' => 'Ejercicio fundamental para tríceps', 'equipo_requerido' => 'Mancuernas o Barra'],
            ['nombre' => 'Fondos de Banco', 'grupo_muscular' => 'Tríceps', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio de peso corporal para tríceps', 'equipo_requerido' => 'Banco'],
            ['nombre' => 'Extensión Francesa', 'grupo_muscular' => 'Tríceps', 'dificultad' => 'intermedio', 'descripcion' => 'Aislamiento de tríceps con barra', 'equipo_requerido' => 'Barra EZ'],
            
            // Piernas - Cuádriceps
            ['nombre' => 'Sentadilla', 'grupo_muscular' => 'Cuádriceps', 'dificultad' => 'avanzado', 'descripcion' => 'Ejercicio fundamental para piernas', 'equipo_requerido' => 'Barra y Rack'],
            ['nombre' => 'Prensa de Piernas', 'grupo_muscular' => 'Cuádriceps', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio en máquina para cuádriceps', 'equipo_requerido' => 'Máquina de Prensa'],
            ['nombre' => 'Extensión de Cuádriceps', 'grupo_muscular' => 'Cuádriceps', 'dificultad' => 'principiante', 'descripcion' => 'Aislamiento de cuádriceps en máquina', 'equipo_requerido' => 'Máquina de Extensión'],
            
            // Piernas - Isquiotibiales
            ['nombre' => 'Peso Muerto', 'grupo_muscular' => 'Isquiotibiales', 'dificultad' => 'avanzado', 'descripcion' => 'Ejercicio fundamental para espalda baja e isquiotibiales', 'equipo_requerido' => 'Barra'],
            ['nombre' => 'Flexión de Isquiotibiales', 'grupo_muscular' => 'Isquiotibiales', 'dificultad' => 'principiante', 'descripcion' => 'Aislamiento de isquiotibiales en máquina', 'equipo_requerido' => 'Máquina de Flexión'],
            ['nombre' => 'Buenos Días', 'grupo_muscular' => 'Isquiotibiales', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio para isquiotibiales y espalda baja', 'equipo_requerido' => 'Barra'],
            
            // Piernas - Glúteos
            ['nombre' => 'Sentadilla Búlgara', 'grupo_muscular' => 'Glúteos', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio unilateral para glúteos', 'equipo_requerido' => 'Banco y Mancuernas'],
            ['nombre' => 'Hip Thrust', 'grupo_muscular' => 'Glúteos', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio específico para glúteos', 'equipo_requerido' => 'Banco y Barra'],
            ['nombre' => 'Patada de Glúteos', 'grupo_muscular' => 'Glúteos', 'dificultad' => 'principiante', 'descripcion' => 'Aislamiento de glúteos en máquina', 'equipo_requerido' => 'Máquina de Patada'],
            
            // Abdominales
            ['nombre' => 'Abdominales en Máquina', 'grupo_muscular' => 'Abdominales', 'dificultad' => 'principiante', 'descripcion' => 'Aislamiento de abdominales en máquina', 'equipo_requerido' => 'Máquina de Abdominales'],
            ['nombre' => 'Crunch', 'grupo_muscular' => 'Abdominales', 'dificultad' => 'principiante', 'descripcion' => 'Ejercicio básico de abdominales', 'equipo_requerido' => 'Colchoneta'],
            ['nombre' => 'Plancha', 'grupo_muscular' => 'Abdominales', 'dificultad' => 'intermedio', 'descripcion' => 'Ejercicio de peso corporal para core', 'equipo_requerido' => 'Colchoneta'],
        ];

        foreach ($ejercicios as $ejercicio) {
            Ejercicio::create($ejercicio);
        }
    }
}
