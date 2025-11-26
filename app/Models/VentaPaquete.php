<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaPaquete extends Model
{
    use HasFactory;

    protected $table = 'venta_paquete';

    protected $fillable = [
        'usuario_id',
        'paquete_id',
        'sesiones_restantes',
        'sesiones_utilizadas',
        'fecha_compra',
        'fecha_ultima_asistencia',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_ultima_asistencia' => 'date',
        'sesiones_restantes' => 'integer',
        'sesiones_utilizadas' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'paquete_id');
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaSesion::class);
    }

    // Obtener cantidad total de sesiones del paquete
    public function getCantidadAttribute()
    {
        return $this->paquete->num_sesiones ?? 0;
    }

    // Verificar si el paquete está activo (tiene sesiones disponibles)
    public function getActivoAttribute()
    {
        return $this->sesiones_restantes > 0;
    }
}
