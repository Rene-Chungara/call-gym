<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'contrasena',
        'fecha_registro',
        'is_propietario',
        'is_secretaria',
        'is_instructor',
        'is_clientes',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'is_propietario' => 'boolean',
        'is_secretaria'  => 'boolean',
        'is_instructor'  => 'boolean',
        'is_clientes'    => 'boolean',
    ];

    // Para que Laravel use 'contrasena' como password
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // Hashear contraseña automáticamente
    public function setContrasenaAttribute($value)
    {
        if ($value) {
            $this->attributes['contrasena'] = bcrypt($value);
        }
    }
}

