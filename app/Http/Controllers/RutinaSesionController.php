<?php

namespace App\Http\Controllers;

use App\Models\RutinaSesion;
use App\Models\Rutina;
use App\Models\Ejercicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RutinaSesionController extends Controller
{
    public function create(Rutina $rutina)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $ejercicios = Ejercicio::orderBy('grupo_muscular')
            ->orderBy('nombre')
            ->get();

        $sesiones = $rutina->sesiones()->count();

        return Inertia::render('RutinaSesiones/Create', [
            'rutina' => $rutina,
            'ejercicios' => $ejercicios,
            'proximaSesion' => $sesiones + 1,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'rutina_id' => 'required|exists:rutina,id',
            'numero_sesion' => 'required|integer|min:1',
            'descripcion' => 'required|string',
            'ejercicios' => 'required|array|min:1',
            'ejercicios.*.ejercicio_id' => 'required|exists:ejercicios,id',
            'ejercicios.*.series' => 'required|integer|min:1',
            'ejercicios.*.repeticiones' => 'required|integer|min:1',
            'ejercicios.*.peso_estimado' => 'nullable|numeric|min:0',
            'ejercicios.*.descanso_segundos' => 'nullable|integer|min:0',
        ]);

        try {
            $rutinaSesion = RutinaSesion::create([
                'rutina_id' => $validated['rutina_id'],
                'numero_sesion' => $validated['numero_sesion'],
                'descripcion' => $validated['descripcion'],
            ]);

            foreach ($validated['ejercicios'] as $index => $ejercicio) {
                $rutinaSesion->rutinaSesionEjercicios()->create([
                    'ejercicio_id' => $ejercicio['ejercicio_id'],
                    'orden' => $index + 1,
                    'series' => $ejercicio['series'],
                    'repeticiones' => $ejercicio['repeticiones'],
                    'peso_estimado' => $ejercicio['peso_estimado'] ?? null,
                    'descanso_segundos' => $ejercicio['descanso_segundos'] ?? null,
                ]);
            }

            return redirect()->route('rutinas.show', $validated['rutina_id'])
                ->with('success', 'Sesión creada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear sesión', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function edit(RutinaSesion $rutinaSesion)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $ejercicios = Ejercicio::orderBy('grupo_muscular')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('RutinaSesiones/Edit', [
            'rutinaSesion' => $rutinaSesion->load(['rutina', 'ejercicios']),
            'ejercicios' => $ejercicios,
        ]);
    }

    public function update(Request $request, RutinaSesion $rutinaSesion)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'descripcion' => 'required|string',
            'ejercicios' => 'required|array|min:1',
            'ejercicios.*.ejercicio_id' => 'required|exists:ejercicios,id',
            'ejercicios.*.series' => 'required|integer|min:1',
            'ejercicios.*.repeticiones' => 'required|integer|min:1',
            'ejercicios.*.peso_estimado' => 'nullable|numeric|min:0',
            'ejercicios.*.descanso_segundos' => 'nullable|integer|min:0',
        ]);

        try {
            $rutinaSesion->update(['descripcion' => $validated['descripcion']]);

            $rutinaSesion->rutinaSesionEjercicios()->delete();

            foreach ($validated['ejercicios'] as $index => $ejercicio) {
                $rutinaSesion->rutinaSesionEjercicios()->create([
                    'ejercicio_id' => $ejercicio['ejercicio_id'],
                    'orden' => $index + 1,
                    'series' => $ejercicio['series'],
                    'repeticiones' => $ejercicio['repeticiones'],
                    'peso_estimado' => $ejercicio['peso_estimado'] ?? null,
                    'descanso_segundos' => $ejercicio['descanso_segundos'] ?? null,
                ]);
            }

            return redirect()->route('rutinas.show', $rutinaSesion->rutina_id)
                ->with('success', 'Sesión actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar sesión', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(RutinaSesion $rutinaSesion)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $rutinaSesion->rutinaSesionEjercicios()->delete();
            $rutinaSesion->delete();

            return back()->with('success', 'Sesión eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar sesión', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar la sesión']);
        }
    }
}
