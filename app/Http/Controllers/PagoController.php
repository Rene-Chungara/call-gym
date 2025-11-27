<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Models\Pago;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    // Mostrar formulario para pagar suscripción al contado
    public function create(Request $request)
    {
        $suscripcionId = $request->query('suscripcion_id');
        $suscripcion = Suscripcion::with(['usuario', 'membresia'])->findOrFail($suscripcionId);

        $user = auth()->user();
        // Permitir acceso a clientes si es su propia suscripción
        if ($user->is_clientes && $suscripcion->usuario_id !== $user->id) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        if (!$user->is_propietario && !$user->is_secretaria && !$user->is_clientes) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Pagos/Create', [
            'suscripcion' => [
                'id' => $suscripcion->id,
                'usuario' => $suscripcion->usuario->nombre,
                'membresia' => $suscripcion->membresia->nombre,
                'monto_total' => $suscripcion->obtenerMontoTotal(),
                'monto_pendiente' => $suscripcion->obtenerMontoPendiente(),
                'fecha_fin' => $suscripcion->fecha_fin,
            ]
        ]);
    }

    // Registrar pago
    public function store(Request $request, \App\Services\PagoFacilService $pagoFacilService)
    {
        $validated = $request->validate([
            'suscripcion_id' => 'required|exists:suscripcion,id',
            'monto_pagado' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,qr',
            'observaciones' => 'nullable|string',
        ]);

        $suscripcion = Suscripcion::with(['usuario', 'membresia'])->findOrFail($validated['suscripcion_id']);
        $montoPendiente = $suscripcion->obtenerMontoPendiente();

        // Validar monto
        if ($validated['monto_pagado'] > $montoPendiente) {
            return back()->withErrors([
                'monto_pagado' => "El monto no puede exceder Bs. {$montoPendiente}",
            ]);
        }

        // Si es tarjeta, Stripe
        if ($validated['metodo_pago'] === 'tarjeta') {
            return $this->createStripeCheckoutSession($suscripcion, floatval($validated['monto_pagado']), $validated['observaciones'] ?? null);
        }

        // Si es QR, PagoFácil
        if ($validated['metodo_pago'] === 'qr') {
            try {
                $pedidoId = 'SUS-' . $suscripcion->id . '-' . time();
                $callbackUrl = route('pagos.pagofacil.callback');

                // Asegurar que tenemos precio de membresía
                $precioMembresia = 0;
                if ($suscripcion->membresia) {
                    $precioMembresia = $suscripcion->membresia->precio;
                } else {
                    $membresia = \App\Models\Membresia::find($suscripcion->membresia_id);
                    $precioMembresia = $membresia ? $membresia->precio : 0;
                }

                // Formatear detalles del pedido según API v2
                $detalles = [
                    [
                        'serial' => 1,
                        'product' => 'Pago Suscripción ' . ($suscripcion->membresia->nombre ?? 'Servicio'),
                        'quantity' => 1,
                        'price' => floatval($validated['monto_pagado']),
                        'discount' => 0,
                        'total' => floatval($validated['monto_pagado'])
                    ]
                ];

                $resultado = $pagoFacilService->generarQr(
                    $suscripcion->usuario,
                    floatval($validated['monto_pagado']),
                    $pedidoId,
                    $callbackUrl,
                    $detalles
                );

                // Guardar intento de pago pendiente
                $pago = Pago::create([
                    'suscripcion_id' => $suscripcion->id,
                    'monto_abonado' => $validated['monto_pagado'],
                    'monto_total_membresia' => $precioMembresia,
                    'fecha_abono' => now(),
                    'metodo_pago' => 'qr',
                    'observaciones' => $validated['observaciones'],
                    'estado_pago' => false, // Pendiente
                    'pagofacil_transaction_id' => $resultado['transactionId'],
                    'qr_image' => $resultado['qrBase64'],
                ]);

                return Inertia::render('Pagos/Qr', [
                    'qrImage' => 'data:image/png;base64,' . $resultado['qrBase64'],
                    'transactionId' => $resultado['transactionId'],
                    'pagoId' => $pago->id,
                    'suscripcionId' => $suscripcion->id,
                    'monto' => $validated['monto_pagado'],
                ]);

            } catch (\Exception $e) {
                Log::error('Error generando QR', ['error' => $e->getMessage()]);
                return back()->withErrors(['general' => 'Error al generar QR: ' . $e->getMessage()]);
            }
        }

        // Efectivo
        try {
            Pago::create([
                'suscripcion_id' => $suscripcion->id,
                'monto_abonado' => $validated['monto_pagado'],
                'monto_total_membresia' => $suscripcion->membresia->precio,
                'fecha_abono' => now(),
                'metodo_pago' => 'efectivo',
                'observaciones' => $validated['observaciones'],
                'estado_pago' => true,
            ]);

            $nuevoPendiente = $montoPendiente - $validated['monto_pagado'];

            // Actualizar estado de la suscripción
            $suscripcion->update([
                'estado_pago' => $nuevoPendiente <= 0 ? true : false,
            ]);

            return redirect()->route('suscripciones.show', $suscripcion->id)->with('success', 'Pago registrado correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error al registrar pago', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error: ' . $e->getMessage()]);
        }
    }

    // Stripe Session
    private function createStripeCheckoutSession(Suscripcion $suscripcion, float $monto, ?string $observaciones)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $tasaCambio = 6.96;
            $montoUSD = $monto / $tasaCambio;

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Pago Suscripción - ' . $suscripcion->membresia->nombre,
                                'description' => 'Cliente: ' . $suscripcion->usuario->nombre . ' | Monto: Bs. ' . number_format($monto, 2),
                            ],
                            'unit_amount' => intval($montoUSD * 100),
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('pagos.stripe.success', ['suscripcion' => $suscripcion->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('pagos.create', ['suscripcion_id' => $suscripcion->id]),
                'metadata' => [
                    'suscripcion_id' => $suscripcion->id,
                    'monto_pagado' => $monto,
                    'observaciones' => $observaciones ?? '',
                ],
            ]);

            return redirect()->away($session->url);
        } catch (\Throwable $e) {
            Log::error('Error Stripe', ['exception' => $e]);
            return back()->withErrors(['general' => 'Error Stripe: ' . $e->getMessage()]);
        }
    }

    public function stripeSuccess(Request $request, Suscripcion $suscripcion)
    {
        $sessionId = $request->query('session_id');
        Log::info('Stripe Success Callback Iniciado', ['suscripcion_id' => $suscripcion->id, 'session_id' => $sessionId]);

        if (!$sessionId) {
            Log::warning('No session_id provided in callback');
            return redirect()->route('suscripciones.show', $suscripcion->id);
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $suscripcion->load('membresia');

        Log::info('Datos de Membresía Debug', [
            'suscripcion_id' => $suscripcion->id,
            'membresia_id' => $suscripcion->membresia_id,
            'membresia_exists' => $suscripcion->membresia ? 'YES' : 'NO',
            'precio' => $suscripcion->membresia ? $suscripcion->membresia->precio : 'N/A',
        ]);

        try {
            $session = Session::retrieve($sessionId);
            Log::info('Stripe Session Retrieved', ['status' => $session->payment_status, 'metadata' => $session->metadata]);

            if ($session->payment_status === 'paid') {
                $monto = floatval($session->metadata->monto_pagado);
                $obs = $session->metadata->observaciones;

                // Obtener precio de membresía de forma defensiva
                $precioMembresia = 0;
                if ($suscripcion->membresia) {
                    $precioMembresia = $suscripcion->membresia->precio;
                } else {
                    $membresia = \App\Models\Membresia::find($suscripcion->membresia_id);
                    $precioMembresia = $membresia ? $membresia->precio : 0;
                }

                Log::info('Precio final a guardar en Pago', ['precio' => $precioMembresia]);

                Pago::create([
                    'suscripcion_id' => $suscripcion->id,
                    'monto_abonado' => $monto,
                    'monto_total_membresia' => $precioMembresia,
                    'fecha_abono' => now(),
                    'metodo_pago' => 'tarjeta',
                    'observaciones' => $obs,
                    'estado_pago' => true,
                    'stripe_session_id' => $session->id,
                    'stripe_payment_id' => $session->payment_intent,
                    'stripe_status' => $session->payment_status,
                ]);

                // Recalcular estado
                $montoTotal = $suscripcion->obtenerMontoTotal();
                // Forzar recarga de la relación pagos para incluir el nuevo pago
                $suscripcion->load('pagos');
                $pagos = $suscripcion->pagos->sum('monto_abonado');
                $pendiente = $montoTotal - $pagos;

                Log::info('Recalculando estado', [
                    'monto_total' => $montoTotal,
                    'pagos_total' => $pagos,
                    'pendiente' => $pendiente
                ]);

                $updated = $suscripcion->update([
                    'estado_pago' => $pendiente <= 0.5 ? true : false, // Margen de error por decimales
                ]);

                Log::info('Suscripción actualizada', ['updated' => $updated, 'nuevo_estado_pago' => $suscripcion->estado_pago]);

                return redirect()->route('suscripciones.show', $suscripcion->id)->with('success', 'Pago con tarjeta exitoso.');
            } else {
                Log::warning('El estado del pago no es paid', ['status' => $session->payment_status]);
            }
        } catch (\Throwable $e) {
            Log::error('Error Stripe Success', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('suscripciones.show', $suscripcion->id)->withErrors(['general' => 'Error al verificar pago: ' . $e->getMessage()]);
        }
    }

    public function callbackPagoFacil(Request $request)
    {
        try {
            Log::info('Callback recibido de PagoFácil', ['data' => $request->all()]);

            // Validar que se recibieron todos los datos necesarios
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

            Log::info('Buscando pago con referencia externa', ['pedido_id' => $pedidoId]);

            // Extraer ID suscripción: SUS-{id}-{timestamp}
            if (preg_match('/SUS-(\\d+)-/', $pedidoId, $matches)) {
                $suscripcionId = $matches[1];
                $suscripcion = Suscripcion::find($suscripcionId);

                if ($suscripcion) {
                    // Buscar pago pendiente asociado
                    $pago = Pago::where('suscripcion_id', $suscripcion->id)
                        ->where('metodo_pago', 'qr')
                        ->where('estado_pago', false)
                        ->latest()
                        ->first();

                    if ($pago) {
                        // Mapear estado de PagoFácil a estado interno
                        $estadoInterno = $this->mapearEstadoPago($estado);

                        Log::info('Estado mapeado', [
                            'estado_pagofacil' => $estado,
                            'estado_interno' => $estadoInterno
                        ]);

                        // Actualizar el pago en nuestra base de datos
                        $pago->update([
                            'estado_pago' => $estadoInterno === 'completado',
                            'fecha_abono' => now(),
                            'observaciones' => 'Pago QR confirmado. Ref: ' . $pedidoId . ' | Método: ' . $metodoPago . ' | Fecha: ' . $fecha . ' ' . $hora,
                        ]);

                        // Si el pago fue completado, actualizar también el estado de la suscripción
                        if ($estadoInterno === 'completado') {
                            $this->actualizarEstadoSuscripcion($suscripcion);
                        }

                        Log::info('Pago actualizado exitosamente desde callback', [
                            'pago_id' => $pago->id,
                            'pedido_id' => $pedidoId,
                            'estado_nuevo' => $estadoInterno,
                            'metodo_pago' => $metodoPago,
                            'fecha_pago' => $fecha . ' ' . $hora
                        ]);

                        // Respuesta exitosa según especificación de PagoFácil
                        return response()->json([
                            'error' => 0,
                            'status' => 1,
                            'message' => "Pago procesado correctamente",
                            'values' => true
                        ]);
                    }
                }
            }

            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => "Pago no encontrado en el sistema",
                'values' => false
            ]);

        } catch (\Exception $e) {
            Log::error('Error en callback de PagoFácil', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'data' => $request->all()
            ]);

            return response()->json([
                'error' => 1,
                'status' => 0,
                'message' => "No se pudo procesar el pago, por favor intente de nuevo",
                'values' => false
            ]);
        }
    }

    /**
     * Mapear estado de PagoFácil a estado interno
     * Estados según documentación PagoFácil:
     * 0 = Pendiente
     * 1 = Iniciado
     * 2 = Completado/Pagado
     * 3 = Rechazado/Cancelado
     */
    private function mapearEstadoPago($estado)
    {
        $estadoLower = strtolower((string) $estado);

        // Estados completados - SOLO aceptar estado 2 o texto "completado"/"pagado"
        if (
            $estadoLower === 'completado' ||
            $estadoLower === 'pagado' ||
            $estado === '2' ||
            $estado === 2
        ) {
            return 'completado';
        }

        // Estados rechazados
        if (
            $estadoLower === 'rechazado' ||
            $estadoLower === 'cancelado' ||
            $estado === '3' ||
            $estado === 3
        ) {
            return 'rechazado';
        }

        // Estado por defecto: pendiente (incluye 0 y 1)
        return 'pendiente';
    }

    /**
     * Consultar estado de pago en PagoFácil
     * Usando lógica del ejemplo que funciona
     */
    public function consultarEstadoPagoFacil(Request $request, \App\Services\PagoFacilService $service)
    {
        set_time_limit(120);

        try {
            $transactionId = $request->transactionId;
            $pagoId = $request->pagoId;
            $suscripcionId = $request->suscripcionId;

            if (!$transactionId) {
                return response()->json(['success' => false, 'message' => 'Transaction ID es requerido'], 400);
            }

            $pago = Pago::find($pagoId);
            if ($pago->estado_pago) {
                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'redirect' => route('suscripciones.show', $suscripcionId)
                ]);
            }

            // Obtener token
            try {
                $tokenResponse = $service->obtenerToken();
            } catch (\Exception $e) {
                Log::error('Fallo al obtener token en consultarEstado', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'Error de conexión con pasarela'], 500);
            }

            if (!isset($tokenResponse['values']['accessToken'])) {
                return response()->json(['success' => false, 'message' => 'No se pudo autenticar con PagoFácil'], 500);
            }

            $accessToken = $tokenResponse['values']['accessToken'];
            $client = new \GuzzleHttp\Client();

            // Realizar la petición
            $response = $client->post(config('pagofacil.base_url') . '/query-transaction', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                'json' => [
                    'pagofacilTransactionId' => (int) $transactionId
                ],
                'http_errors' => false,
                'timeout' => 90,
                'connect_timeout' => 10
            ]);

            $responseContent = $response->getBody()->getContents();
            $result = json_decode($responseContent, true);

            Log::info('Respuesta cruda consultarEstado', ['content' => $result]);

            // Validar JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['success' => false, 'message' => 'Respuesta inválida del proveedor'], 500);
            }

            // Validar errores de la API
            if (isset($result['error']) && $result['error'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Error en la transacción'
                ], 400);
            }

            if (!isset($result['values'])) {
                return response()->json(['success' => false, 'message' => 'Datos no encontrados'], 404);
            }

            $values = $result['values'];
            $paymentStatus = $values['paymentStatus'] ?? null;
            $paymentDate = $values['paymentDate'] ?? null;
            $paymentTime = $values['paymentTime'] ?? null;

            Log::info('Estado de pago desde PagoFácil', [
                'paymentStatus' => $paymentStatus,
                'paymentDate' => $paymentDate,
                'paymentTime' => $paymentTime,
                'all_values' => $values
            ]);

            // Solo confirmar si paymentStatus es 2 (completado)
            if ($paymentStatus === 2) {
                $pago->update([
                    'estado_pago' => true,
                    'fecha_abono' => now(),
                    'observaciones' => 'Pago QR verificado. Status: ' . $paymentStatus . ' | Fecha: ' . $paymentDate . ' ' . $paymentTime,
                ]);

                $suscripcion = Suscripcion::find($suscripcionId);
                $this->actualizarEstadoSuscripcion($suscripcion);

                Log::info('Pago actualizado como completado', [
                    'pago_id' => $pago->id,
                    'transaction_id' => $transactionId,
                    'payment_status' => $paymentStatus
                ]);

                return response()->json([
                    'success' => true,
                    'paid' => true,
                    'redirect' => route('suscripciones.show', $suscripcionId)
                ]);
            }

            // Pago aún pendiente
            Log::info('Pago aún no completado', [
                'pago_id' => $pago->id,
                'paymentStatus' => $paymentStatus
            ]);

            return response()->json([
                'success' => true,
                'paid' => false,
                'data' => [
                    'paymentStatus' => $paymentStatus,
                    'paymentDate' => $paymentDate,
                    'paymentTime' => $paymentTime
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Excepción crítica en consultarEstado', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    private function actualizarEstadoSuscripcion(Suscripcion $suscripcion)
    {
        $montoTotal = $suscripcion->obtenerMontoTotal();
        $suscripcion->load('pagos');
        $pagos = $suscripcion->pagos->where('estado_pago', true)->sum('monto_abonado');
        $pendiente = $montoTotal - $pagos;

        $suscripcion->update([
            'estado_pago' => $pendiente <= 0.5 ? true : false,
        ]);
    }
}
