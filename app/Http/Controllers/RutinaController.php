<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\User;
use App\Models\Ejercicio;
use App\Models\RutinaSesion;
use App\Models\RutinaSesionEjercicio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RutinaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Rutina::select('id', 'nombre', 'descripcion', 'usuario_id')
                ->with(['usuario:id,nombre']);

            // Filtro por búsqueda
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'ilike', "%{$search}%")
                        ->orWhere('descripcion', 'ilike', "%{$search}%")
                        ->orWhereHas('usuario', function ($q2) use ($search) {
                            $q2->where('nombre', 'ilike', "%{$search}%");
                        });
                });
            }

            // Si es cliente, solo ve sus rutinas
            if ($user->is_clientes && !$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
                $query->where('usuario_id', $user->id);
            }

            $rutinas = $query->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();

            return Inertia::render('Rutinas/Index', [
                'rutinas' => $rutinas,
                'filters' => $request->only(['search']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener rutinas', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar rutinas.']);
        }
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $usuarios = User::select('id', 'nombre')
                ->where('is_clientes', true)
                ->orderBy('nombre')
                ->get();

            $ejercicios = Ejercicio::select('id', 'nombre', 'grupo_muscular', 'dificultad')
                ->orderBy('grupo_muscular')
                ->orderBy('nombre')
                ->get();

            return Inertia::render('Rutinas/Create', [
                'usuarios' => $usuarios,
                'ejercicios' => $ejercicios,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de rutina', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el formulario.']);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'sesiones' => 'required|array|min:1',
            'sesiones.*.numero_sesion' => 'required|integer|min:1',
            'sesiones.*.descripcion' => 'required|string',
            'sesiones.*.ejercicios' => 'required|array|min:1',
            'sesiones.*.ejercicios.*.ejercicio_id' => 'required|exists:ejercicios,id',
            'sesiones.*.ejercicios.*.orden' => 'required|integer|min:1',
            'sesiones.*.ejercicios.*.series' => 'required|integer|min:1',
            'sesiones.*.ejercicios.*.repeticiones' => 'required|integer|min:1',
            'sesiones.*.ejercicios.*.peso_estimado' => 'nullable|numeric|min:0',
            'sesiones.*.ejercicios.*.descanso_segundos' => 'nullable|integer|min:0',
            'sesiones.*.ejercicios.*.notas' => 'nullable|string',
        ], [
            'usuario_id.required' => 'Debe seleccionar un cliente.',
            'usuario_id.exists' => 'El cliente no existe.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'sesiones.required' => 'Debe agregar al menos una sesión.',
            'sesiones.*.ejercicios.required' => 'Cada sesión debe tener al menos un ejercicio.',
        ]);

        try {
            DB::beginTransaction();

            // Crear la rutina
            $rutina = Rutina::create([
                'usuario_id' => $validated['usuario_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
            ]);

            // Crear las sesiones y sus ejercicios
            foreach ($validated['sesiones'] as $sesionData) {
                $sesion = RutinaSesion::create([
                    'rutina_id' => $rutina->id,
                    'numero_sesion' => $sesionData['numero_sesion'],
                    'descripcion' => $sesionData['descripcion'],
                ]);

                // Crear los ejercicios de la sesión
                foreach ($sesionData['ejercicios'] as $ejercicioData) {
                    RutinaSesionEjercicio::create([
                        'rutina_sesion_id' => $sesion->id,
                        'ejercicio_id' => $ejercicioData['ejercicio_id'],
                        'orden' => $ejercicioData['orden'],
                        'series' => $ejercicioData['series'],
                        'repeticiones' => $ejercicioData['repeticiones'],
                        'peso_estimado' => $ejercicioData['peso_estimado'] ?? null,
                        'descanso_segundos' => $ejercicioData['descanso_segundos'] ?? null,
                        'notas' => $ejercicioData['notas'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('rutinas.show', $rutina->id)
                ->with('success', 'Rutina creada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear rutina', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(Rutina $rutina)
    {
        $user = Auth::user();
        // Verificar si el cliente puede ver esta rutina
        if ($user->is_clientes && !$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            if ($rutina->usuario_id !== $user->id) {
                abort(403, 'No tienes permiso para ver esta rutina.');
            }
        }

        try {
            $rutina->load([
                'usuario:id,nombre',
                'sesiones' => function ($query) {
                    $query->orderBy('numero_sesion');
                },
                'sesiones.rutinaSesionEjercicios' => function ($query) {
                    $query->orderBy('orden');
                },
                'sesiones.rutinaSesionEjercicios.ejercicio:id,nombre,grupo_muscular,dificultad'
            ]);

            return Inertia::render('Rutinas/Show', [
                'rutina' => $rutina,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener rutina', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar la rutina.']);
        }
    }

    public function edit(Rutina $rutina)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $usuarios = User::select('id', 'nombre')
                ->where('is_clientes', true)
                ->orderBy('nombre')
                ->get();

            $ejercicios = Ejercicio::select('id', 'nombre', 'grupo_muscular', 'dificultad')
                ->orderBy('grupo_muscular')
                ->orderBy('nombre')
                ->get();

            $rutina->load([
                'sesiones' => function ($query) {
                    $query->orderBy('numero_sesion');
                },
                'sesiones.rutinaSesionEjercicios' => function ($query) {
                    $query->orderBy('orden');
                },
                'sesiones.rutinaSesionEjercicios.ejercicio:id,nombre,grupo_muscular,dificultad'
            ]);

            return Inertia::render('Rutinas/Edit', [
                'rutina' => $rutina,
                'usuarios' => $usuarios,
                'ejercicios' => $ejercicios,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar edición de rutina', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el formulario.']);
        }
    }

    public function update(Request $request, Rutina $rutina)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'sesiones' => 'required|array|min:1',
            'sesiones.*.numero_sesion' => 'required|integer|min:1',
            'sesiones.*.descripcion' => 'required|string',
            'sesiones.*.ejercicios' => 'required|array|min:1',
            'sesiones.*.ejercicios.*.ejercicio_id' => 'required|exists:ejercicios,id',
            'sesiones.*.ejercicios.*.orden' => 'required|integer|min:1',
            'sesiones.*.ejercicios.*.series' => 'required|integer|min:1',
            'sesiones.*.ejercicios.*.repeticiones' => 'required|integer|min:1',
            'sesiones.*.ejercicios.*.peso_estimado' => 'nullable|numeric|min:0',
            'sesiones.*.ejercicios.*.descanso_segundos' => 'nullable|integer|min:0',
            'sesiones.*.ejercicios.*.notas' => 'nullable|string',
        ], [
            'usuario_id.required' => 'Debe seleccionar un cliente.',
            'usuario_id.exists' => 'El cliente no existe.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'sesiones.required' => 'Debe agregar al menos una sesión.',
            'sesiones.*.ejercicios.required' => 'Cada sesión debe tener al menos un ejercicio.',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar la rutina
            $rutina->update([
                'usuario_id' => $validated['usuario_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
            ]);

            // Eliminar sesiones anteriores (y sus ejercicios por cascada si está configurado)
            $rutina->sesiones()->delete();

            // Crear las nuevas sesiones y sus ejercicios
            foreach ($validated['sesiones'] as $sesionData) {
                $sesion = RutinaSesion::create([
                    'rutina_id' => $rutina->id,
                    'numero_sesion' => $sesionData['numero_sesion'],
                    'descripcion' => $sesionData['descripcion'],
                ]);

                // Crear los ejercicios de la sesión
                foreach ($sesionData['ejercicios'] as $ejercicioData) {
                    RutinaSesionEjercicio::create([
                        'rutina_sesion_id' => $sesion->id,
                        'ejercicio_id' => $ejercicioData['ejercicio_id'],
                        'orden' => $ejercicioData['orden'],
                        'series' => $ejercicioData['series'],
                        'repeticiones' => $ejercicioData['repeticiones'],
                        'peso_estimado' => $ejercicioData['peso_estimado'] ?? null,
                        'descanso_segundos' => $ejercicioData['descanso_segundos'] ?? null,
                        'notas' => $ejercicioData['notas'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('rutinas.show', $rutina->id)
                ->with('success', 'Rutina actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al actualizar rutina', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(Rutina $rutina)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $rutina->delete();

            return redirect()->route('rutinas.index')
                ->with('success', 'Rutina eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar rutina', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar la rutina.']);
        }
    }

    /**
     * Vista de progreso para clientes
     */
    public function miProgreso()
    {
        $user = Auth::user();

        try {
            // Obtener la rutina activa del usuario
            $rutina = Rutina::where('usuario_id', $user->id)
                ->with([
                    'sesiones' => function ($query) {
                        $query->orderBy('numero_sesion');
                    },
                    'sesiones.rutinaSesionEjercicios' => function ($query) {
                        $query->orderBy('orden');
                    },
                    'sesiones.rutinaSesionEjercicios.ejercicio:id,nombre,grupo_muscular,dificultad'
                ])
                ->latest()
                ->first();

            if (!$rutina) {
                return Inertia::render('Rutinas/MiProgreso', [
                    'rutina' => null,
                    'asistencias' => [],
                ]);
            }

            // Obtener asistencias del usuario con rutina_sesion_id
            $asistencias = \App\Models\AsistenciaSesion::whereHas('ventaPaquete', function ($query) use ($user) {
                $query->where('usuario_id', $user->id);
            })
                ->whereNotNull('rutina_sesion_id')
                ->where('estado', 'asistio')
                ->pluck('rutina_sesion_id')
                ->toArray();

            return Inertia::render('Rutinas/MiProgreso', [
                'rutina' => $rutina,
                'sesionesCompletadas' => $asistencias,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar progreso', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el progreso.']);
        }
    }
}
