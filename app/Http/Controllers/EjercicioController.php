<?php

namespace App\Http\Controllers;

use App\Models\Ejercicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EjercicioController extends Controller
{
    public function index(Request $request)
    {
        $query = Ejercicio::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'ilike', "%{$search}%")
                ->orWhere('descripcion', 'ilike', "%{$search}%");
        }

        if ($request->has('grupo_muscular') && $request->input('grupo_muscular')) {
            $query->where('grupo_muscular', $request->input('grupo_muscular'));
        }

        if ($request->has('dificultad') && $request->input('dificultad')) {
            $query->where('dificultad', $request->input('dificultad'));
        }

        $ejercicios = $query->orderBy('grupo_muscular')
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        // Obtener grupos musculares únicos para el filtro
        $gruposMusculares = Ejercicio::select('grupo_muscular')->distinct()->orderBy('grupo_muscular')->pluck('grupo_muscular');

        return Inertia::render('Ejercicios/Index', [
            'ejercicios' => $ejercicios,
            'gruposMusculares' => $gruposMusculares,
            'filters' => $request->only(['search', 'grupo_muscular', 'dificultad']),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Ejercicios/Create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|unique:ejercicios',
            'descripcion' => 'nullable|string',
            'grupo_muscular' => 'required|string',
            'dificultad' => 'required|in:principiante,intermedio,avanzado',
            'equipo_requerido' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Este ejercicio ya existe.',
            'grupo_muscular.required' => 'El grupo muscular es obligatorio.',
            'dificultad.required' => 'La dificultad es obligatoria.',
        ]);

        try {
            Ejercicio::create($validated);

            return redirect()->route('ejercicios.index')
                ->with('success', 'Ejercicio creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear ejercicio', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(Ejercicio $ejercicio)
    {
        return Inertia::render('Ejercicios/Show', [
            'ejercicio' => $ejercicio,
        ]);
    }

    public function edit(Ejercicio $ejercicio)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Ejercicios/Edit', [
            'ejercicio' => $ejercicio,
        ]);
    }

    public function update(Request $request, Ejercicio $ejercicio)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|unique:ejercicios,nombre,' . $ejercicio->id,
            'descripcion' => 'nullable|string',
            'grupo_muscular' => 'required|string',
            'dificultad' => 'required|in:principiante,intermedio,avanzado',
            'equipo_requerido' => 'nullable|string',
        ]);

        try {
            $ejercicio->update($validated);

            return redirect()->route('ejercicios.index')
                ->with('success', 'Ejercicio actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar ejercicio', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(Ejercicio $ejercicio)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $ejercicio->delete();

            return redirect()->route('ejercicios.index')
                ->with('success', 'Ejercicio eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar ejercicio', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar el ejercicio']);
        }
    }
}
