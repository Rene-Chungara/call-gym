<?php

namespace App\Http\Controllers;

use App\Models\CuotaPago;
use App\Models\PlanPago;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CuotaPagoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $cuotas = CuotaPago::with(['planPago.suscripcion.usuario'])
            ->orderBy('fecha_vencimiento', 'desc')
            ->paginate(20);

        return Inertia::render('CuotasPago/Index', [
            'cuotas' => $cuotas,
        ]);
    }

    // Mostrar formulario para pagar una cuota
    public function create(CuotaPago $cuotaPago)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria && !$user->is_clientes) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $cuotaPago->load(['planPago.suscripcion.usuario', 'planPago.suscripcion.membresia']);

            if (!$cuotaPago->planPago) {
                return redirect()->back()->withErrors(['general' => 'Plan de pago no encontrado.']);
            }

            return Inertia::render('CuotasPago/Create', [
                'cuota' => [
                    'id' => $cuotaPago->id,
                    'numero_cuota' => $cuotaPago->numero_cuota,
                    'monto' => $cuotaPago->monto,
                    'fecha_vencimiento' => $cuotaPago->fecha_vencimiento,
                    'estado' => $cuotaPago->estado,
                    'plan_pago_id' => $cuotaPago->plan_pago_id,
                ],
                'planPago' => [
                    'id' => $cuotaPago->planPago->id,
                    'monto_total' => $cuotaPago->planPago->monto_total,
                    'cantidad_cuotas' => $cuotaPago->planPago->cantidad_cuotas,
                ],
                'suscripcion' => [
                    'id' => $cuotaPago->planPago->suscripcion->id,
                    'usuario' => $cuotaPago->planPago->suscripcion->usuario->nombre,
                    'membresia' => $cuotaPago->planPago->suscripcion->membresia->nombre,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al cargar formulario de pago de cuota', ['exception' => $e]);
            return redirect()->back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    // Registrar pago de una cuota
    public function store(Request $request, CuotaPago $cuotaPago)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'monto_pagado' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta',
            'observaciones' => 'nullable|string',
        ], [
            'monto_pagado.required' => 'El monto es obligatorio.',
            'monto_pagado.numeric' => 'El monto debe ser un número.',
            'metodo_pago.required' => 'Selecciona un método de pago.',
        ]);

        try {
            $montoPagado = floatval($validated['monto_pagado']);

            // Validar que no se pague más de lo debido
            if ($montoPagado > $cuotaPago->monto) {
                return back()->withErrors([
                    'monto_pagado' => "El monto no puede exceder Bs. {$cuotaPago->monto}",
                ]);
            }

            // Actualizar cuota
            $cuotaPago->update([
                'fecha_pago' => now()->toDateString(),
                'metodo_pago' => $validated['metodo_pago'],
                'estado' => $montoPagado >= $cuotaPago->monto ? 'pagado' : 'pagado_parcial',
            ]);

            // Verificar si todas las cuotas del plan están pagadas
            $planPago = $cuotaPago->planPago;
            $cuotasPendientes = $planPago->cuotas()->where('estado', '!=', 'pagado')->count();

            // Si todas están pagadas, actualizar suscripción
            if ($cuotasPendientes === 0) {
                $planPago->suscripcion->update([
                    'estado_pago' => true,
                ]);
            }

            return redirect()
                ->route('suscripciones.show', $planPago->suscripcion->id)
                ->with('success', 'Cuota pagada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error al pagar cuota', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, CuotaPago $cuotaPago)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,tarjeta',
        ]);

        try {
            $cuotaPago->update([
                'fecha_pago' => $validated['fecha_pago'],
                'metodo_pago' => $validated['metodo_pago'],
                'estado' => 'pagado',
            ]);

            return redirect()->back()
                ->with('success', 'Cuota marcada como pagada.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar cuota', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }
}
