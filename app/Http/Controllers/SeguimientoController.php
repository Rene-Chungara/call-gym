<?php

namespace App\Http\Controllers;

use App\Models\Seguimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class SeguimientoController extends Controller
{
    public function index()
    {
        try {
            $seguimientos = Seguimiento::select('id', 'usuario_id', 'fecha', 'peso', 'medidas', 'observaciones')
                ->with(['usuario:id,nombre'])
                ->orderBy('fecha', 'desc')
                ->paginate(15);

            return Inertia::render('Seguimientos/Index', [
                'seguimientos' => $seguimientos,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener seguimientos', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar seguimientos.']);
        }
    }

    public function create()
    {
        try {
            $usuarios = User::select('id', 'nombre')
                ->where('is_clientes', true)
                ->orderBy('nombre')
                ->get();

            return Inertia::render('Seguimientos/Create', [
                'usuarios' => $usuarios,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de seguimiento', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el formulario.']);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'fecha' => 'required|date',
            'peso' => 'required|numeric|min:0',
            'medidas' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ], [
            'usuario_id.required' => 'Debe seleccionar un cliente.',
            'usuario_id.exists' => 'El cliente no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'peso.required' => 'El peso es obligatorio.',
            'peso.numeric' => 'El peso debe ser un número.',
            'medidas.required' => 'Las medidas son obligatorias.',
            'medidas.numeric' => 'Las medidas deben ser un número.',
        ]);

        try {
            Seguimiento::create($validated);

            return redirect()->route('seguimientos.index')
                ->with('success', 'Seguimiento registrado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear seguimiento', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(Seguimiento $seguimiento)
    {
        try {
            return Inertia::render('Seguimientos/Show', [
                'seguimiento' => $seguimiento->load(['usuario:id,nombre']),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener seguimiento', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el seguimiento.']);
        }
    }

    public function edit(Seguimiento $seguimiento)
    {
        try {
            $usuarios = User::select('id', 'nombre')
                ->where('is_clientes', true)
                ->orderBy('nombre')
                ->get();

            return Inertia::render('Seguimientos/Edit', [
                'seguimiento' => $seguimiento,
                'usuarios' => $usuarios,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar edición de seguimiento', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al cargar el formulario.']);
        }
    }

    public function update(Request $request, Seguimiento $seguimiento)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'fecha' => 'required|date',
            'peso' => 'required|numeric|min:0',
            'medidas' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ], [
            'usuario_id.required' => 'Debe seleccionar un cliente.',
            'usuario_id.exists' => 'El cliente no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'peso.required' => 'El peso es obligatorio.',
            'peso.numeric' => 'El peso debe ser un número.',
            'medidas.required' => 'Las medidas son obligatorias.',
            'medidas.numeric' => 'Las medidas deben ser un número.',
        ]);

        try {
            $seguimiento->update($validated);

            return redirect()->route('seguimientos.index')
                ->with('success', 'Seguimiento actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar seguimiento', ['exception' => $e]);
            return back()
                ->withInput()
                ->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy(Seguimiento $seguimiento)
    {
        try {
            $seguimiento->delete();

            return redirect()->route('seguimientos.index')
                ->with('success', 'Seguimiento eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar seguimiento', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar el seguimiento.']);
        }
    }
}
