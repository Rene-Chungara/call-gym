<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaSesion extends Model
{
    use HasFactory;

    protected $table = 'asistencia_sesion';

    protected $fillable = [
        'venta_paquete_id',
        'rutina_sesion_id',
        'numero_sesion',
        'fecha_asistencia',
        'hora_entrada',
        'hora_salida',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_asistencia' => 'date',
        'hora_entrada' => 'datetime:H:i',
        'hora_salida' => 'datetime:H:i',
    ];

    public function ventaPaquete()
    {
        return $this->belongsTo(VentaPaquete::class);
    }

    public function rutinaSesion()
    {
        return $this->belongsTo(RutinaSesion::class);
    }

    public function duracionSesion()
    {
        if ($this->hora_entrada && $this->hora_salida) {
            return $this->hora_salida->diffInMinutes($this->hora_entrada);
        }
        return null;
    }
}
