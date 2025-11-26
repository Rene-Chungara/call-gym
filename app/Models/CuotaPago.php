<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuotaPago extends Model
{
    use HasFactory;

    protected $table = 'cuotas_pago';

    protected $fillable = [
        'plan_pago_id',
        'numero_cuota',
        'monto',
        'fecha_vencimiento',
        'fecha_pago',
        'estado',
        'metodo_pago',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
    ];

    public function planPago()
    {
        return $this->belongsTo(PlanPago::class);
    }

    public function estaVencida()
    {
        return $this->estado === 'pendiente' && 
               $this->fecha_vencimiento < now()->toDateString();
    }
}
