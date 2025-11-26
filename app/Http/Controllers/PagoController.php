<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Suscripcion;
use App\Models\DetallePago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Inertia\Inertia;

class PagoController extends Controller
{
    public function index()
    {
        return Inertia::render('Pagos/Index', [
            'pagos' => Pago::with(['suscripcion.usuario', 'suscripcion.membresia'])
                ->orderBy('fecha_abono', 'desc')
                ->paginate(15)
                ->through(fn($pago) => [
                    'id' => $pago->id,
                    'monto_abonado' => $pago->monto_abonado,
                    'monto_total_membresia' => $pago->monto_total_membresia,
                    'fecha_abono' => $pago->fecha_abono,
                    'metodo_pago' => $pago->metodo_pago,
                    'estado_pago' => $pago->estado_pago,
                    'observaciones' => $pago->observaciones,
                    'cliente' => $pago->suscripcion?->usuario->nombre ?? 'N/A',
                    'membresia' => $pago->suscripcion?->membresia->nombre ?? 'N/A',
                ]),
        ]);
    }

    public function create(Request $request)
    {
        $suscripcionSeleccionada = null;
        
        // Si viene suscripcion_id desde la URL, cargarla directamente
        if ($request->has('suscripcion_id')) {
            $suscripcionSeleccionada = Suscripcion::with(['usuario:id,nombre', 'membresia:id,nombre,precio'])
                ->findOrFail($request->suscripcion_id);
        }

        $suscripciones = Suscripcion::select('id', 'usuario_id', 'membresia_id')
            ->with(['usuario:id,nombre', 'membresia:id,nombre,precio'])
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'usuario' => $s->usuario->nombre,
                    'membresia' => $s->membresia->nombre,
                    'precio' => $s->membresia->precio,
                ];
            });

        return Inertia::render('Pagos/Create', [
            'suscripciones' => $suscripciones,
            'suscripcionSeleccionada' => $suscripcionSeleccionada ? [
                'id' => $suscripcionSeleccionada->id,
                'usuario' => $suscripcionSeleccionada->usuario->nombre,
                'membresia' => $suscripcionSeleccionada->membresia->nombre,
                'precio' => $suscripcionSeleccionada->membresia->precio,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'suscripcion_id' => 'required|exists:suscripcion,id',
            'monto_abonado' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta',
            'observaciones' => 'nullable|string',
        ], [
            'suscripcion_id.required' => 'Debe seleccionar una suscripción.',
            'monto_abonado.required' => 'El monto a pagar es obligatorio.',
            'metodo_pago.required' => 'Debe seleccionar un método de pago.',
        ]);

        try {
            $suscripcion = Suscripcion::findOrFail($validated['suscripcion_id']);

            $pago = Pago::create([
                'suscripcion_id' => $validated['suscripcion_id'],
                'monto_abonado' => $validated['monto_abonado'],
                'monto_total_membresia' => $suscripcion->membresia->precio,
                'fecha_abono' => now()->toDateString(),
                'metodo_pago' => $validated['metodo_pago'],
                'estado_pago' => true,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            // Actualizar estado de suscripción a activa
            $suscripcion->update([
                'estado_pago' => true,
                'estado' => 1,
            ]);

            // Si el pago es con tarjeta, redirigir a Stripe
            if ($validated['metodo_pago'] === 'tarjeta') {
                return redirect()->route('pagos.stripe', [
                    'pago_id' => $pago->id,
                    'monto' => $validated['monto_abonado'],
                ]);
            }

            return redirect()->route('pagos.index')
                ->with('success', 'Pago al contado registrado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al registrar pago', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function show(Pago $pago)
    {
        return Inertia::render('Pagos/Show', [
            'pago' => $pago->load(['suscripcion.usuario', 'suscripcion.membresia']),
        ]);
    }
}
