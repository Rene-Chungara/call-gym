<?php

namespace App\Http\Controllers;

use App\Models\PlanPago;
use App\Models\CuotaPago;
use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class PlanPagoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $planes = PlanPago::with(['suscripcion.usuario', 'suscripcion.membresia'])
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        return Inertia::render('PlanPagos/Index', [
            'planes' => $planes,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $suscripciones = Suscripcion::with(['usuario', 'membresia'])
            ->where('estado_pago', false)
            ->get();

        return Inertia::render('PlanPagos/Create', [
            'suscripciones' => $suscripciones,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'suscripcion_id' => 'required|exists:suscripcion,id',
            'monto_total' => 'required|numeric|min:0.01',
            'cantidad_cuotas' => 'required|integer|min:1|max:12',
            'fechas' => 'required|array|min:1',
            'fechas.*' => 'required|date|after:today',
        ]);

        try {
            $planPago = PlanPago::create([
                'suscripcion_id' => $validated['suscripcion_id'],
                'monto_total' => $validated['monto_total'],
                'cantidad_cuotas' => $validated['cantidad_cuotas'],
                'estado' => 'activo',
                'fecha_inicio' => now()->toDateString(),
            ]);

            $montoPorCuota = $validated['monto_total'] / $validated['cantidad_cuotas'];

            foreach ($request->fechas as $index => $fecha) {
                CuotaPago::create([
                    'plan_pago_id' => $planPago->id,
                    'numero_cuota' => $index + 1,
                    'monto' => round($montoPorCuota, 2),
                    'fecha_vencimiento' => $fecha,
                    'estado' => 'pendiente',
                ]);
            }

            return redirect()->route('plan-pagos.show', $planPago->id)
                ->with('success', 'Plan de pagos creado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al crear plan de pagos', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(PlanPago $planPago)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            // Si es cliente, verificar si es su plan
            if ($user->is_clientes) {
                $planPago->load('suscripcion');
                if ($planPago->suscripcion->usuario_id !== $user->id) {
                    abort(403, 'No tienes permiso para ver este plan.');
                }
            } else {
                abort(403, 'No tienes permiso para acceder a esta sección.');
            }
        }

        return Inertia::render('PlanPagos/Show', [
            'plan' => $planPago->load(['suscripcion.usuario', 'suscripcion.membresia', 'cuotas']),
        ]);
    }

    public function destroy(PlanPago $planPago)
    {
        $user = auth()->user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            $planPago->cuotas()->delete();
            $planPago->delete();

            return redirect()->route('plan-pagos.index')
                ->with('success', 'Plan de pagos eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar plan de pagos', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al eliminar el plan']);
        }
    }
}
