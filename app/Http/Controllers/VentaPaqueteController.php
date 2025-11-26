<?php

namespace App\Http\Controllers;

use App\Models\VentaPaquete;
use App\Models\Paquete;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class VentaPaqueteController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        try {
            $ventas = VentaPaquete::select('id', 'usuario_id', 'paquete_id', 'sesiones_restantes', 'fecha_compra')
                ->with(['usuario:id,nombre', 'paquete:id,nombre,num_sesiones'])
                ->orderBy('fecha_compra', 'desc')
                ->paginate(15);

            return Inertia::render('VentasPaquetes/Index', [
                'ventas' => $ventas,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener ventas de paquetes', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar ventas.']);
        }
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $usuarios = User::select('id', 'nombre')
                ->where('is_clientes', true)
                ->orderBy('nombre')
                ->get();

            $paquetes = Paquete::select('id', 'nombre', 'precio', 'num_sesiones')
                ->orderBy('nombre')
                ->get();

            return Inertia::render('VentasPaquetes/Create', [
                'usuarios' => $usuarios,
                'paquetes' => $paquetes,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de venta', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el formulario.']);
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'paquete_id' => 'required|exists:paquete,id',
            'fecha_compra' => 'required|date',
        ], [
            'usuario_id.required' => 'Debe seleccionar un cliente.',
            'usuario_id.exists' => 'El cliente no existe.',
            'paquete_id.required' => 'Debe seleccionar un paquete.',
            'paquete_id.exists' => 'El paquete no existe.',
            'fecha_compra.required' => 'La fecha es obligatoria.',
            'fecha_compra.date' => 'La fecha debe ser válida.',
        ]);

        try {
            $paquete = Paquete::findOrFail($validated['paquete_id']);

            VentaPaquete::create([
                'usuario_id' => $validated['usuario_id'],
                'paquete_id' => $validated['paquete_id'],
                'sesiones_restantes' => $paquete->num_sesiones,
                'fecha_compra' => $validated['fecha_compra'],
            ]);

            return redirect()->route('venta-paquetes.index')
                ->with('success', 'Venta registrada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear venta de paquete', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(VentaPaquete $ventaPaquete)
    {
        try {
            return Inertia::render('VentasPaquetes/Show', [
                'venta' => $ventaPaquete->load(['usuario:id,nombre', 'paquete:id,nombre,precio,num_sesiones']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener venta de paquete', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar la venta.']);
        }
    }

    public function edit(VentaPaquete $ventaPaquete)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $usuarios = User::select('id', 'nombre')
                ->where('is_clientes', true)
                ->orderBy('nombre')
                ->get();

            $paquetes = Paquete::select('id', 'nombre', 'precio', 'num_sesiones')
                ->orderBy('nombre')
                ->get();

            return Inertia::render('VentasPaquetes/Edit', [
                'venta' => $ventaPaquete,
                'usuarios' => $usuarios,
                'paquetes' => $paquetes,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar edición de venta', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el formulario.']);
        }
    }

    public function update(Request $request, VentaPaquete $ventaPaquete)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'paquete_id' => 'required|exists:paquete,id',
            'sesiones_restantes' => 'required|integer|min:0',
            'fecha_compra' => 'required|date',
        ], [
            'usuario_id.required' => 'Debe seleccionar un cliente.',
            'usuario_id.exists' => 'El cliente no existe.',
            'paquete_id.required' => 'Debe seleccionar un paquete.',
            'paquete_id.exists' => 'El paquete no existe.',
            'sesiones_restantes.required' => 'Las sesiones restantes son obligatorias.',
            'sesiones_restantes.integer' => 'Las sesiones deben ser un número entero.',
            'sesiones_restantes.min' => 'Las sesiones no pueden ser negativas.',
            'fecha_compra.required' => 'La fecha es obligatoria.',
            'fecha_compra.date' => 'La fecha debe ser válida.',
        ]);

        try {
            $ventaPaquete->update($validated);

            return redirect()->route('venta-paquetes.index')
                ->with('success', 'Venta actualizada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar venta de paquete', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(VentaPaquete $ventaPaquete)
    {
        try {
            $ventaPaquete->delete();

            return redirect()->route('venta-paquetes.index')
                ->with('success', 'Venta eliminada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar venta de paquete', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar la venta.']);
        }
    }
}
