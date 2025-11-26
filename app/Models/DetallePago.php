<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePago extends Model
{
    use HasFactory;

    protected $table = 'detalle_pago';

    protected $fillable = [
        'pago_id',
        'suscripcion_id'
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    public function suscripcion()
    {
        return $this->belongsTo(Suscripcion::class, 'suscripcion_id');
    }
}