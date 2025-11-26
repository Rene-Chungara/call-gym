<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'seguimiento';

    protected $fillable = [
        'usuario_id',
        'fecha',
        'peso',
        'medidas',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'peso' => 'float',
        'medidas' => 'float',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
