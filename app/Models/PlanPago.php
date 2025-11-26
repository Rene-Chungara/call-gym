<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPago extends Model
{
    use HasFactory;

    protected $table = 'plan_pagos';

    protected $fillable = [
        'suscripcion_id',
        'monto_total',
        'cantidad_cuotas',
        'estado',
        'fecha_inicio',
    ];

    protected $casts = [
        'monto_total' => 'float',
        'cantidad_cuotas' => 'integer',
        'fecha_inicio' => 'date',
    ];

    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class);
    }

    public function cuotas()
    {
        return $this->hasMany(CuotaPago::class);
    }

    public function cuotasPagadas()
    {
        return $this->cuotas()->where('estado', 'pagado');
    }

    public function cuotasPendientes()
    {
        return $this->cuotas()->where('estado', 'pendiente');
    }

    public function cuotasVencidas()
    {
        return $this->cuotas()
            ->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', now()->toDateString());
    }

    public function montoPagado()
    {
        return $this->cuotasPagadas()->sum('monto');
    }

    public function montoPendiente()
    {
        return $this->cuotasPendientes()->sum('monto');
    }
}
