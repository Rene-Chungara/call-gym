<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;

    protected $table = 'paquete';

    protected $fillable = [
        'nombre',
        'precio',
        'num_sesiones',
    ];

    protected $casts = [
        'precio' => 'float',
        'num_sesiones' => 'integer',
    ];

    public function ventasPaquete()
    {
        return $this->hasMany(VentaPaquete::class, 'paquete_id');
    }
}
