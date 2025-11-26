<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    use HasFactory;

    protected $table = 'membresia';

    protected $fillable = ['nombre', 'precio', 'duracion_dias'];

    protected $casts = [
        'precio' => 'float',
        'duracion_dias' => 'integer',
    ];

    public function suscripcion()
    {
        return $this->hasMany(Suscripcion::class, 'membresia_id');
    }
}
