<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\Suscripcion;
use App\Models\Pago;
use App\Models\PlanPago;
use App\Models\CuotaPago;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MembresiaClienteController extends Controller
{
    /**
     * Mostrar catálogo de membresías disponibles para clientes
     */
    public function index()
    {
        $user = auth()->user();

        // Solo clientes pueden acceder
        if (!$user->is_clientes) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Obtener todas las membresías activas
        $membresias = Membresia::orderBy('precio', 'asc')->get();

        // Verificar si el cliente ya tiene una suscripción activa
        $suscripcionActiva = Suscripcion::where('usuario_id', $user->id)
            ->where('estado', 1)
            ->where('fecha_fin', '>=', now())
            ->first();

        return Inertia::render('Membresias/Catalogo', [
            'membresias' => $membresias,
            'tieneSuscripcionActiva' => $suscripcionActiva ? true : false,
            'suscripcionActiva' => $suscripcionActiva ? [
                'id' => $suscripcionActiva->id,
                'membresia' => $suscripcionActiva->membresia->nombre,
                'fecha_fin' => $suscripcionActiva->fecha_fin,
            ] : null,
        ]);
    }

    /**
     * Mostrar página de checkout para una membresía
     */
    public function checkout(Membresia $membresia)
    {
        $user = auth()->user();

        // Solo clientes pueden acceder
        if (!$user->is_clientes) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Verificar si el cliente ya tiene una suscripción activa
        $suscripcionActiva = Suscripcion::where('usuario_id', $user->id)
            ->where('estado', 1)
            ->where('fecha_fin', '>=', now())
            ->first();

        if ($suscripcionActiva) {
            return redirect()->route('membresias.catalogo')
                ->with('error', 'Ya tienes una suscripción activa. No puedes comprar otra membresía hasta que expire.');
        }

        return Inertia::render('Membresias/Checkout', [
            'membresia' => [
                'id' => $membresia->id,
                'nombre' => $membresia->nombre,
                'precio' => $membresia->precio,
                'duracion_dias' => $membresia->duracion_dias,
            ],
        ]);
    }

    /**
     * Procesar la compra de una membresía
     */
    public function procesarCompra(Request $request)
    {
        $user = auth()->user();

        // Solo clientes pueden acceder
        if (!$user->is_clientes) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'membresia_id' => 'required|exists:membresia,id',
            'tipo_pago' => 'required|in:contado,credito',
            'metodo_pago' => 'required|in:qr,tarjeta',
            'cantidad_cuotas' => 'nullable|integer|min:2|max:12',
            'fechas' => 'nullable|array',
            'fechas.*' => 'nullable|date',
            'montos' => 'nullable|array',
            'montos.*' => 'nullable|numeric|min:0.01',
        ]);

        try {
            // Verificar si el cliente ya tiene una suscripción activa
            $suscripcionActiva = Suscripcion::where('usuario_id', $user->id)
                ->where('estado', 1)
                ->where('fecha_fin', '>=', now())
                ->first();

            if ($suscripcionActiva) {
                return back()->withErrors([
                    'general' => 'Ya tienes una suscripción activa. No puedes comprar otra membresía hasta que expire.',
                ]);
            }

            $membresia = Membresia::findOrFail($validated['membresia_id']);

            // Crear suscripción con fecha de inicio hoy
            $fecha_inicio = now()->toDateString();
            $fecha_fin = Carbon::parse($fecha_inicio)->addDays($membresia->duracion_dias);

            $suscripcion = Suscripcion::create([
                'usuario_id' => $user->id,
                'membresia_id' => $membresia->id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'estado' => 1, // Activa (se activará al confirmar pago)
                'estado_pago' => false,
                'tipo_pago' => $validated['tipo_pago'],
                'fecha_estado' => now(),
            ]);

            // Si es pago al contado
            if ($validated['tipo_pago'] === 'contado') {
                $pago = Pago::create([
                    'suscripcion_id' => $suscripcion->id,
                    'monto_abonado' => 0,
                    'monto_total_membresia' => $membresia->precio,
                    'fecha_abono' => now()->toDateString(),
                    'metodo_pago' => 'pendiente',
                    'estado_pago' => false,
                    'observaciones' => 'Pago pendiente - Compra desde cliente',
                ]);

                // Redirigir según método de pago
                if ($validated['metodo_pago'] === 'qr') {
                    // Redirigir a PagoController para generar QR
                    return redirect()->route('pagos.create', [
                        'suscripcion_id' => $suscripcion->id,
                        'metodo' => 'qr'
                    ]);
                } else {
                    // Redirigir a Stripe
                    return redirect()->route('pagos.create', [
                        'suscripcion_id' => $suscripcion->id,
                        'metodo' => 'tarjeta'
                    ]);
                }
            }

            // Si es pago a crédito
            if ($validated['tipo_pago'] === 'credito') {
                $fechas = is_array($validated['fechas']) ? array_filter($validated['fechas']) : [];
                $montos = is_array($validated['montos']) ? array_filter($validated['montos']) : [];

                $planPago = PlanPago::create([
                    'suscripcion_id' => $suscripcion->id,
                    'monto_total' => $membresia->precio,
                    'cantidad_cuotas' => count($fechas) > 0 ? count($fechas) : $validated['cantidad_cuotas'],
                    'estado' => 'activo',
                    'fecha_inicio' => now()->toDateString(),
                ]);

                // Crear cuotas individuales
                if (count($fechas) > 0 && count($montos) > 0) {
                    $cantidadCuotas = min(count($fechas), count($montos));

                    for ($i = 0; $i < $cantidadCuotas; $i++) {
                        CuotaPago::create([
                            'plan_pago_id' => $planPago->id,
                            'numero_cuota' => $i + 1,
                            'monto' => floatval($montos[$i]),
                            'fecha_vencimiento' => $fechas[$i],
                            'estado' => 'pendiente',
                        ]);
                    }
                }

                // Redirigir a la primera cuota para pagar
                $primeraCuota = CuotaPago::where('plan_pago_id', $planPago->id)
                    ->orderBy('numero_cuota')
                    ->first();

                if ($primeraCuota) {
                    if ($validated['metodo_pago'] === 'qr') {
                        return redirect()->route('cuotas-pago.create', [
                            'cuotaPago' => $primeraCuota->id,
                            'metodo' => 'qr'
                        ]);
                    } else {
                        return redirect()->route('cuotas-pago.create', [
                            'cuotaPago' => $primeraCuota->id,
                            'metodo' => 'tarjeta'
                        ]);
                    }
                }
            }

            return redirect()->route('suscripciones.index')
                ->with('success', 'Suscripción creada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error procesando compra de membresía', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'membresia_id' => $validated['membresia_id'] ?? null,
            ]);

            return back()->withErrors([
                'general' => 'Ocurrió un problema al procesar tu compra. Por favor, intenta nuevamente.',
            ]);
        }
    }
}
