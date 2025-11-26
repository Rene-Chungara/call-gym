<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaSesion;
use App\Models\VentaPaquete;
use App\Models\RutinaSesion;
use App\Models\Rutina;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaSesionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AsistenciaSesion::with(['ventaPaquete.usuario', 'rutinaSesion.rutina']);

        // Filtro por búsqueda (nombre de usuario)
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('ventaPaquete.usuario', function ($q) use ($search) {
                $q->where('nombre', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Filtro por fecha
        if ($request->has('fecha')) {
            $query->whereDate('fecha_asistencia', $request->input('fecha'));
        }

        // Si es cliente, solo ve sus asistencias
        if ($user->is_clientes && !$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            $query->whereHas('ventaPaquete', function ($q) use ($user) {
                $q->where('usuario_id', $user->id);
            });
        }

        $asistencias = $query->orderBy('fecha_asistencia', 'desc')
            ->paginate(20)
            ->through(fn($item) => [
                'id' => $item->id,
                'venta_paquete' => $item->ventaPaquete,
                'rutina_sesion' => $item->rutinaSesion,
                'numero_sesion' => $item->numero_sesion,
                'fecha_asistencia' => Carbon::parse($item->fecha_asistencia)->format('d-m-Y'),
                'hora_entrada' => $item->hora_entrada ? Carbon::parse($item->hora_entrada)->format('H:i') : null,
                'hora_salida' => $item->hora_salida ? Carbon::parse($item->hora_salida)->format('H:i') : null,
                'estado' => $item->estado,
                'observaciones' => $item->observaciones,
            ]);

        return Inertia::render('AsistenciaSesion/Index', [
            'asistencias' => $asistencias,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // Cargar ventas de paquetes activos con sus usuarios y paquetes
        $ventasPaquetes = VentaPaquete::with(['usuario', 'paquete'])
            ->where('sesiones_restantes', '>', 0)
            ->orderBy('fecha_compra', 'desc')
            ->get();

        // Cargar todas las rutinas con sus sesiones para poder filtrar por usuario en el frontend
        $rutinas = Rutina::with([
            'sesiones' => function ($query) {
                $query->orderBy('numero_sesion');
            },
            'usuario:id,nombre'
        ])
            ->get();

        return Inertia::render('AsistenciaSesion/Create', [
            'ventasPaquetes' => $ventasPaquetes,
            'rutinas' => $rutinas,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'venta_paquete_id' => 'required|exists:venta_paquete,id',
            'rutina_sesion_id' => 'nullable|exists:rutina_sesion,id',
            'numero_sesion' => 'required|integer|min:1',
            'fecha_asistencia' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i',
            'estado' => 'required|in:asistio,no_asistio,cancelada',
            'observaciones' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el paquete tenga sesiones disponibles
            $ventaPaquete = VentaPaquete::findOrFail($validated['venta_paquete_id']);

            if ($validated['estado'] === 'asistio' && $ventaPaquete->sesiones_restantes <= 0) {
                return back()->withErrors(['venta_paquete_id' => 'Este paquete no tiene sesiones disponibles.']);
            }

            // Crear la asistencia
            $asistencia = AsistenciaSesion::create($validated);

            // Actualizar el paquete solo si asistió
            if ($validated['estado'] === 'asistio') {
                $ventaPaquete->update([
                    'sesiones_utilizadas' => $ventaPaquete->sesiones_utilizadas + 1,
                    'sesiones_restantes' => $ventaPaquete->sesiones_restantes - 1,
                    'fecha_ultima_asistencia' => $validated['fecha_asistencia'],
                ]);
            }

            DB::commit();

            return redirect()->route('asistencia-sesion.index')
                ->with('success', 'Asistencia registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al registrar asistencia', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(AsistenciaSesion $asistenciaSesion)
    {
        $user = Auth::user();

        // Verificar si el cliente puede ver esta asistencia
        if ($user->is_clientes && !$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            $asistenciaSesion->load('ventaPaquete');
            if ($asistenciaSesion->ventaPaquete->usuario_id !== $user->id) {
                abort(403, 'No tienes permiso para ver esta asistencia.');
            }
        }

        $asistenciaSesion->load(['ventaPaquete.usuario', 'rutinaSesion.rutina']);

        // Formatear fechas para la vista
        $asistenciaFormatted = [
            'id' => $asistenciaSesion->id,
            'venta_paquete' => $asistenciaSesion->ventaPaquete,
            'rutina_sesion' => $asistenciaSesion->rutinaSesion,
            'numero_sesion' => $asistenciaSesion->numero_sesion,
            'fecha_asistencia' => Carbon::parse($asistenciaSesion->fecha_asistencia)->format('d-m-Y'),
            'hora_entrada' => $asistenciaSesion->hora_entrada ? Carbon::parse($asistenciaSesion->hora_entrada)->format('H:i') : null,
            'hora_salida' => $asistenciaSesion->hora_salida ? Carbon::parse($asistenciaSesion->hora_salida)->format('H:i') : null,
            'estado' => $asistenciaSesion->estado,
            'observaciones' => $asistenciaSesion->observaciones,
            'venta_paquete_id' => $asistenciaSesion->venta_paquete_id, // Necesario para edición
            'rutina_sesion_id' => $asistenciaSesion->rutina_sesion_id, // Necesario para edición
        ];

        return Inertia::render('AsistenciaSesion/Show', [
            'asistencia' => $asistenciaFormatted,
        ]);
    }

    public function edit(AsistenciaSesion $asistenciaSesion)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $ventasPaquetes = VentaPaquete::with(['usuario', 'paquete'])->get();
        $rutinaSesiones = RutinaSesion::with(['rutina', 'ejercicios'])->get();

        return Inertia::render('AsistenciaSesion/Edit', [
            'asistencia' => $asistenciaSesion,
            'ventasPaquetes' => $ventasPaquetes,
            'rutinaSesiones' => $rutinaSesiones,
        ]);
    }

    public function update(Request $request, AsistenciaSesion $asistenciaSesion)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'estado' => 'required|in:asistio,no_asistio,cancelada',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $asistenciaSesion->update($validated);

            return redirect()->route('asistencia-sesion.index')
                ->with('success', 'Asistencia actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar asistencia', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(AsistenciaSesion $asistenciaSesion)
    {
        $user = Auth::user();
        if (!$user->is_propietario && !$user->is_instructor && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $asistenciaSesion->delete();

            return redirect()->route('asistencia-sesion.index')
                ->with('success', 'Asistencia eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar asistencia', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar la asistencia']);
        }
    }
}
