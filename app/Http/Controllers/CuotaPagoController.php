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
    public function store(Request $request, CuotaPago $cuotaPago, \App\Services\PagoFacilService $pagoFacilService)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria && !$user->is_clientes) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'monto_pagado' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,qr',
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

            // Si el método de pago es tarjeta, redirigir a Stripe Checkout
            if ($validated['metodo_pago'] === 'tarjeta') {
                return $this->createStripeCheckoutSession($cuotaPago, $montoPagado, $validated['observaciones'] ?? null);
            }

            // Si es QR, PagoFácil
            if ($validated['metodo_pago'] === 'qr') {
                try {
                    $cuotaPago->load(['planPago.suscripcion.usuario', 'planPago.suscripcion.membresia']);

                    $pedidoId = 'CUOTA-' . $cuotaPago->id . '-' . time();
                    $callbackUrl = route('cuotas-pago.pagofacil.callback');

                    // Formatear detalles del pedido según API v2
                    $detalles = [
                        [
                            'serial' => 1,
                            'product' => 'Cuota #' . $cuotaPago->numero_cuota . ' - ' . $cuotaPago->planPago->suscripcion->membresia->nombre,
                            'quantity' => 1,
                            'price' => floatval($montoPagado),
                            'discount' => 0,
                            'total' => floatval($montoPagado)
                        ]
                    ];

                    $resultado = $pagoFacilService->generarQr(
                        $cuotaPago->planPago->suscripcion->usuario,
                        floatval($montoPagado),
                        $pedidoId,
                        $callbackUrl,
                        $detalles
                    );

                    // Actualizar cuota con información de pago pendiente
                    $cuotaPago->update([
                        'pagofacil_transaction_id' => $resultado['transactionId'],
                        'qr_image' => $resultado['qrBase64'],
                        'estado' => 'pendiente',
                    ]);

                    return Inertia::render('CuotasPago/Qr', [
                        'qrImage' => 'data:image/png;base64,' . $resultado['qrBase64'],
                        'transactionId' => $resultado['transactionId'],
                        'cuotaId' => $cuotaPago->id,
                        'suscripcionId' => $cuotaPago->planPago->suscripcion->id,
                        'monto' => $montoPagado,
                        'numeroCuota' => $cuotaPago->numero_cuota,
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error generando QR para cuota', ['error' => $e->getMessage()]);
                    return back()->withErrors(['general' => 'Error al generar QR: ' . $e->getMessage()]);
                }
            }

            // Pago en efectivo: registrar directamente
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

    // Crear sesión de Stripe Checkout
    private function createStripeCheckoutSession(CuotaPago $cuotaPago, float $monto, ?string $observaciones)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $cuotaPago->load(['planPago.suscripcion.usuario', 'planPago.suscripcion.membresia']);

        try {
            // Convertir de Bolivianos a USD (tasa aproximada: 1 USD = 6.96 BOB)
            $tasaCambio = 6.96;
            $montoUSD = $monto / $tasaCambio;

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Cuota #' . $cuotaPago->numero_cuota . ' - ' . $cuotaPago->planPago->suscripcion->membresia->nombre,
                                'description' => 'Cliente: ' . $cuotaPago->planPago->suscripcion->usuario->nombre . ' | Monto: Bs. ' . number_format($monto, 2),
                            ],
                            'unit_amount' => intval($montoUSD * 100), // Stripe usa centavos
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('cuotas-pago.stripe.success', ['cuotaPago' => $cuotaPago->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cuotas-pago.create', ['cuotaPago' => $cuotaPago->id]),
                'metadata' => [
                    'cuota_pago_id' => $cuotaPago->id,
                    'monto_pagado' => $monto,
                    'monto_bolivianos' => $monto,
                    'observaciones' => $observaciones ?? '',
                ],
            ]);

            // Hacer redirección HTTP estándar (no Inertia)
            return redirect()->away($session->url);
        } catch (\Throwable $e) {
            Log::error('Error al crear sesión de Stripe', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error al procesar el pago: ' . $e->getMessage()]);
        }
    }

    // Manejar éxito de pago de Stripe
    public function stripeSuccess(Request $request, CuotaPago $cuotaPago)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('cuotas-pago.create', ['cuotaPago' => $cuotaPago->id])
                ->withErrors(['general' => 'Sesión de pago no válida.']);
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $montoPagado = floatval($session->metadata->monto_pagado);

                // Actualizar cuota
                $cuotaPago->update([
                    'fecha_pago' => now()->toDateString(),
                    'metodo_pago' => 'tarjeta',
                    'estado' => $montoPagado >= $cuotaPago->monto ? 'pagado' : 'pagado_parcial',
                ]);

                // Verificar si todas las cuotas del plan están pagadas
                $planPago = $cuotaPago->planPago;
                $cuotasPendientes = $planPago->cuotas()->where('estado', '!=', 'pagado')->count();

                if ($cuotasPendientes === 0) {
                    $planPago->suscripcion->update([
                        'estado_pago' => true,
                    ]);
                }

                return redirect()
                    ->route('suscripciones.show', $planPago->suscripcion->id)
                    ->with('success', 'Pago con tarjeta procesado correctamente.');
            }

            return redirect()->route('cuotas-pago.create', ['cuotaPago' => $cuotaPago->id])
                ->withErrors(['general' => 'El pago no se completó.']);

        } catch (\Throwable $e) {
            Log::error('Error al verificar pago de Stripe', ['exception' => $e]);
            return redirect()->route('cuotas-pago.create', ['cuotaPago' => $cuotaPago->id])
                ->withErrors(['general' => 'Error al verificar el pago: ' . $e->getMessage()]);
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

    /**
     * Callback para notificaciones de PagoFácil (cuotas)
     */
    public function callbackPagoFacil(Request $request)
    {
        try {
            Log::info('Callback recibido de PagoFácil (Cuota)', ['data' => $request->all()]);

            $pedidoId = $request->input('PedidoID');
            $fecha = $request->input('Fecha');
            $hora = $request->input('Hora');
            $metodoPago = $request->input('MetodoPago');
            $estado = $request->input('Estado');

            if (!$pedidoId) {
                Log::error('Callback sin PedidoID', ['data' => $request->all()]);
                return response()->json([
                    'error' => 1,
                    'status' => 0,
                    'message' => "PedidoID es requerido",
                    'values' => false
                ]);
            }

            // Extraer ID de cuota: CUOTA-{id}-{timestamp}
            if (preg_match('/CUOTA-(\\d+)-/', $pedidoId, $matches)) {
                $cuotaId = $matches[1];
                $cuota = CuotaPago::find($cuotaId);

                if ($cuota && $cuota->estado !== 'pagado') {
                    // Mapear estado
                    $estadoInterno = $this->mapearEstadoPago($estado);

                    Log::info('Estado mapeado (Cuota)', [
                        'estado_pagofacil' => $estado,
                        'estado_interno' => $estadoInterno
                    ]);

                    if ($estadoInterno === 'completado') {
                        // Actualizar cuota
                        $cuota->update([
                            'fecha_pago' => now()->toDateString(),
                            'metodo_pago' => 'qr',
                            'estado' => 'pagado',
                        ]);

                        // Verificar si todas las cuotas están pagadas
                        $this->verificarPlanCompletado($cuota);

                        Log::info('Cuota actualizada exitosamente desde callback', [
                            'cuota_id' => $cuota->id,
                            'pedido_id' => $pedidoId,
                            'metodo_pago' => $metodoPago
                        ]);
                    }

                    return response()->json([
                        'error' => 0,
                        'status' => 1,
                        'message' => "Pago procesado correctamente",
                        'values' => true
                    ]);
                }
            }

            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => "Cuota no encontrada",
                'values' => false
            ]);

        } catch (\Exception $e) {
            Log::error('Error en callback de PagoFácil (Cuota)', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => "Error al procesar el pago",
                'values' => false
            ]);
        }
    }

    /**
     * Consultar estado de pago de cuota en PagoFácil
     */
    public function consultarEstadoPagoFacil(Request $request, \App\Services\PagoFacilService $service)
    {
        $request->validate([
            'transactionId' => 'required',
            'cuotaId' => 'required|exists:cuotas_pago,id',
            'suscripcionId' => 'required|exists:suscripcion,id',
        ]);

        try {
            $statusData = $service->consultarTransaccion($request->transactionId);
            Log::info('Consulta Manual PagoFacil (Cuota)', ['data' => $statusData]);

            $cuota = CuotaPago::find($request->cuotaId);
            if ($cuota->estado === 'pagado') {
                return redirect()->route('suscripciones.show', $request->suscripcionId)->with('success', 'Pago confirmado.');
            }

            // Verificar respuesta API
            if ($statusData && isset($statusData['values'])) {
                $values = $statusData['values'];
                $paymentTime = $values['paymentTime'] ?? null;
                $paymentDate = $values['paymentDate'] ?? null;
                $paymentStatus = $values['paymentStatus'] ?? null;

                Log::info('Estado de pago desde PagoFácil (Cuota)', [
                    'paymentTime' => $paymentTime,
                    'paymentDate' => $paymentDate,
                    'paymentStatus' => $paymentStatus
                ]);

                // Solo confirmar si paymentStatus es 2 Y tiene fecha/hora válidas
                $estadoCompletado = ($paymentStatus === 2);
                $tieneFechaHoraPago = (
                    !empty($paymentTime) &&
                    !empty($paymentDate) &&
                    $paymentTime !== null &&
                    $paymentDate !== null &&
                    $paymentTime !== '0000-00-00 00:00:00' &&
                    $paymentDate !== '0000-00-00'
                );

                if ($estadoCompletado && $tieneFechaHoraPago) {
                    $cuota->update([
                        'fecha_pago' => now()->toDateString(),
                        'metodo_pago' => 'qr',
                        'estado' => 'pagado',
                    ]);

                    // Verificar si todas las cuotas están pagadas
                    $this->verificarPlanCompletado($cuota);

                    Log::info('Cuota actualizada como pagada desde consulta', [
                        'cuota_id' => $cuota->id,
                        'payment_status' => $paymentStatus
                    ]);

                    return redirect()->route('suscripciones.show', $request->suscripcionId)->with('success', 'Pago confirmado exitosamente.');
                }
            }

            // No hacer redirect, devolver JSON para que Inertia no haga nada
            return response()->json(['status' => 'pending'], 200);

        } catch (\Exception $e) {
            Log::error('Error consultando estado (Cuota)', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mapear estado de PagoFácil
     */
    private function mapearEstadoPago($estado)
    {
        $estadoLower = strtolower((string) $estado);

        if ($estadoLower === 'completado' || $estadoLower === 'pagado' || $estado === '2' || $estado === 2) {
            return 'completado';
        }

        if ($estadoLower === 'rechazado' || $estadoLower === 'cancelado' || $estado === '3' || $estado === 3) {
            return 'rechazado';
        }

        return 'pendiente';
    }

    /**
     * Verificar si todas las cuotas del plan están pagadas
     */
    private function verificarPlanCompletado(CuotaPago $cuota)
    {
        $planPago = $cuota->planPago;
        $cuotasPendientes = $planPago->cuotas()->where('estado', '!=', 'pagado')->count();

        if ($cuotasPendientes === 0) {
            $planPago->suscripcion->update([
                'estado_pago' => true,
            ]);
            Log::info('Plan de pago completado', ['plan_pago_id' => $planPago->id]);
        }
    }
}
