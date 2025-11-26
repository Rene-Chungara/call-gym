<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'suscripcion_id',
        'monto_abonado',
        'monto_total_membresia',
        'fecha_abono',
        'metodo_pago',
        'estado_pago',
        'stripe_status',
        'observaciones',
    ];

    protected $casts = [
        'fecha_abono' => 'date',
        'estado_pago' => 'boolean',
    ];

    public function detalle()
    {
        return $this->hasOne(DetallePago::class);
    }

    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'suscripcion_id');
    }

}