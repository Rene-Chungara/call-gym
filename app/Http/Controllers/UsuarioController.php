<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Routing\Controller;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rules(?int $id = null): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios', 'email')->ignore($id),
            ],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:6', 'confirmed'],

            'is_propietario' => ['boolean'],
            'is_secretaria' => ['boolean'],
            'is_instructor' => ['boolean'],
            'is_clientes' => ['boolean'],
        ];
    }

    private function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'max' => 'El campo :attribute no puede superar los :max caracteres.',
            'unique' => 'El :attribute ya está registrado.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'confirmed' => 'La confirmación de :attribute no coincide.',

            'is_propietario.boolean' => 'El campo propietario debe ser verdadero o falso.',
            'is_secretaria.boolean' => 'El campo secretaria debe ser verdadero o falso.',
            'is_instructor.boolean' => 'El campo instructor debe ser verdadero o falso.',
            'is_clientes.boolean' => 'El campo cliente debe ser verdadero o falso.',
        ];
    }

    public function index()
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        try {
            $usuarios = User::orderBy('id', 'desc')
                ->paginate(15)
                ->through(function ($u) {
                    $u->role_name = $this->getRoleName($u);
                    return $u;
                });

            return Inertia::render('Usuarios/Index', [
                'usuarios' => $usuarios,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener usuarios', ['exception' => $e]);

            return back()->withErrors([
                'general' => 'Ocurrió un error al cargar la lista de usuarios.',
            ]);
        }
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Usuarios/Create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate($this->rules(), $this->messages());

        try {
            $usuario = new User();
            $usuario->nombre = $validated['nombre'];
            $usuario->email = $validated['email'];
            $usuario->contrasena = $validated['password']; // mutator la encripta
            $usuario->fecha_registro = now();

            $usuario->is_propietario = $request->boolean('is_propietario');
            $usuario->is_secretaria = $request->boolean('is_secretaria');
            $usuario->is_instructor = $request->boolean('is_instructor');
            $usuario->is_clientes = $request->boolean('is_clientes');

            $usuario->save();

            return redirect()
                ->route('usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear usuario', ['exception' => $e]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Ocurrió un error al crear el usuario. Intenta nuevamente.']);
        }
    }

    public function edit(User $usuario)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Usuarios/Edit', [
            'usuario' => $usuario,
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate($this->rules($usuario->id), $this->messages());

        try {
            $usuario->nombre = $validated['nombre'];
            $usuario->email = $validated['email'];

            if (!empty($validated['password'])) {
                $usuario->contrasena = $validated['password'];
            }

            $usuario->is_propietario = $request->boolean('is_propietario');
            $usuario->is_secretaria = $request->boolean('is_secretaria');
            $usuario->is_instructor = $request->boolean('is_instructor');
            $usuario->is_clientes = $request->boolean('is_clientes');

            $usuario->save();

            return redirect()
                ->route('usuarios.index')
                ->with('success', 'Usuario actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar usuario', ['exception' => $e]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Ocurrió un error al actualizar el usuario.']);
        }
    }

    public function destroy(User $usuario)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $usuario->delete();

            return redirect()
                ->route('usuarios.index')
                ->with('success', 'Usuario eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar usuario', ['exception' => $e]);

            return back()->withErrors([
                'general' => 'Ocurrió un error al eliminar el usuario.',
            ]);
        }
    }

    private function getRoleName($u)
    {
        if ($u->is_propietario)
            return 'Admin';
        if ($u->is_secretaria)
            return 'Secretaria';
        if ($u->is_instructor)
            return 'Instructor';
        if ($u->is_clientes)
            return 'Cliente';
        return 'Sin rol';
    }
}
