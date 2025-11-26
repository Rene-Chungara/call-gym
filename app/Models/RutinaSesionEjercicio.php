<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RutinaSesionEjercicio extends Model
{
    use HasFactory;

    protected $table = 'rutina_sesion_ejercicio';

    protected $fillable = [
        'rutina_sesion_id',
        'ejercicio_id',
        'orden',
        'series',
        'repeticiones',
        'peso_estimado',
        'descanso_segundos',
        'notas',
    ];

    protected $casts = [
        'orden' => 'integer',
        'series' => 'integer',
        'repeticiones' => 'integer',
        'peso_estimado' => 'float',
        'descanso_segundos' => 'integer',
    ];

    public function rutinaSesion()
    {
        return $this->belongsTo(RutinaSesion::class);
    }

    public function ejercicio()
    {
        return $this->belongsTo(Ejercicio::class);
    }
}
