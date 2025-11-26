<?php

namespace App\Http\Traits;

trait HasRoleAuthorization
{
    /**
     * Verificar si el usuario es propietario
     */
    protected function requirePropietario()
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    /**
     * Verificar si el usuario es propietario o secretaria
     */
    protected function requirePropietarioOrSecretaria()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    /**
     * Verificar si el usuario es propietario o instructor
     */
    protected function requirePropietarioOrInstructor()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    /**
     * Verificar si el usuario es propietario, instructor o secretaria
     */
    protected function requireStaff()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }
}
