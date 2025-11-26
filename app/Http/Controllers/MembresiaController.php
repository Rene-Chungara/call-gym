<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\Membresia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class MembresiaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'duracion_dias' => ['required', 'integer', 'min:1'],
        ];
    }

    private function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser un texto.',
            'max' => 'El campo :attribute no puede superar los :max caracteres.',
            'numeric' => 'El campo :attribute debe ser un número.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El valor mínimo para :attribute es :min.',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        try {
            $query = Membresia::orderBy('id', 'desc');

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('nombre', 'ilike', "%{$search}%");
            }

            $membresias = $query->paginate(10)->withQueryString();

            return Inertia::render('Membresias/Index', [
                'membresias' => $membresias,
                'filters' => $request->only(['search']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al listar membresías', ['exception' => $e]);

            return back()->withErrors([
                'general' => 'Ocurrió un error al cargar las membresías.',
            ]);
        }
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Membresias/Create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate($this->rules(), $this->messages());

        try {
            Membresia::create($validated);

            return redirect()
                ->route('membresias.index')
                ->with('success', 'Membresía creada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear membresía', ['exception' => $e]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Ocurrió un error al crear la membresía.']);
        }
    }

    public function edit(Membresia $membresia)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Membresias/Edit', [
            'membresia' => $membresia,
        ]);
    }

    public function update(Request $request, Membresia $membresia)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate($this->rules(), $this->messages());

        try {
            $membresia->update($validated);

            return redirect()
                ->route('membresias.index')
                ->with('success', 'Membresía actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar membresía', ['exception' => $e]);

            return back()
                ->withInput()
                ->withErrors(['general' => 'Ocurrió un error al actualizar la membresía.']);
        }
    }

    public function destroy(Membresia $membresia)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $membresia->delete();

            return redirect()
                ->route('membresias.index')
                ->with('success', 'Membresía eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar membresía', ['exception' => $e]);

            return back()->withErrors([
                'general' => 'Ocurrió un error al eliminar la membresía.',
            ]);
        }
    }
}