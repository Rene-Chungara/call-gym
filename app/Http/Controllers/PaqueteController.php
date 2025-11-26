<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class PaqueteController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        try {
            $paquetes = Paquete::select('id', 'nombre', 'precio', 'num_sesiones')
                ->orderBy('id', 'desc')
                ->paginate(15);

            return Inertia::render('Paquetes/Index', [
                'paquetes' => $paquetes,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener paquetes', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar paquetes.']);
        }
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Paquetes/Create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0.01',
            'num_sesiones' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio debe ser mayor a 0.',
            'num_sesiones.required' => 'El número de sesiones es obligatorio.',
            'num_sesiones.integer' => 'El número de sesiones debe ser un entero.',
            'num_sesiones.min' => 'Debe tener al menos 1 sesión.',
        ]);

        try {
            Paquete::create($validated);

            return redirect()->route('paquetes.index')
                ->with('success', 'Paquete creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear paquete', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(Paquete $paquete)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        try {
            return Inertia::render('Paquetes/Show', [
                'paquete' => $paquete,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener paquete', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el paquete.']);
        }
    }

    public function edit(Paquete $paquete)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Paquetes/Edit', [
            'paquete' => $paquete,
        ]);
    }

    public function update(Request $request, Paquete $paquete)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0.01',
            'num_sesiones' => 'required|integer|min:1',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio debe ser mayor a 0.',
            'num_sesiones.required' => 'El número de sesiones es obligatorio.',
            'num_sesiones.integer' => 'El número de sesiones debe ser un entero.',
            'num_sesiones.min' => 'Debe tener al menos 1 sesión.',
        ]);

        try {
            $paquete->update($validated);

            return redirect()->route('paquetes.index')
                ->with('success', 'Paquete actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar paquete', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(Paquete $paquete)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $paquete->delete();

            return redirect()->route('paquetes.index')
                ->with('success', 'Paquete eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar paquete', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar el paquete.']);
        }
    }
}
