<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ejercicio extends Model
{
    use HasFactory;

    protected $table = 'ejercicios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'grupo_muscular',
        'dificultad',
        'equipo_requerido',
        'imagen_url',
        'video_url',
    ];

    public function rutinaSesionEjercicios()
    {
        return $this->hasMany(RutinaSesionEjercicio::class);
    }

    public function rutinaSesiones()
    {
        return $this->belongsToMany(
            RutinaSesion::class,
            'rutina_sesion_ejercicio',
            'ejercicio_id',
            'rutina_sesion_id'
        )->withPivot('orden', 'series', 'repeticiones', 'peso_estimado', 'descanso_segundos', 'notas');
    }
}
