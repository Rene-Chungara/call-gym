<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutinaSesion extends Model
{
    use HasFactory;

    protected $table = 'rutina_sesion';

    protected $fillable = [
        'rutina_id',
        'numero_sesion',
        'descripcion',
    ];

    protected $casts = [
        'numero_sesion' => 'integer',
    ];

    public function rutina()
    {
        return $this->belongsTo(Rutina::class);
    }

    public function ejercicios()
    {
        return $this->belongsToMany(
            Ejercicio::class,
            'rutina_sesion_ejercicio',
            'rutina_sesion_id',
            'ejercicio_id'
        )->withPivot('orden', 'series', 'repeticiones', 'peso_estimado', 'descanso_segundos', 'notas')
         ->orderBy('rutina_sesion_ejercicio.orden');
    }

    public function rutinaSesionEjercicios()
    {
        return $this->hasMany(RutinaSesionEjercicio::class);
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaSesion::class);
    }
}
