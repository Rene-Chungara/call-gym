<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Models\User;
use App\Models\Membresia;
use App\Models\Pago;
use App\Models\PlanPago;
use App\Models\CuotaPago;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SuscripcionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Suscripcion::with(['usuario', 'membresia'])->orderBy('id', 'desc');

        // Filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('usuario', function ($q) use ($search) {
                $q->where('nombre', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Clientes solo ven sus propias suscripciones
        if ($user->is_clientes && !$user->is_propietario && !$user->is_secretaria) {
            $query->where('usuario_id', $user->id);
        } elseif (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $suscripciones = $query->paginate(10)
            ->withQueryString()
            ->through(fn($item) => [
                'id' => $item->id,
                'usuario' => $item->usuario,
                'membresia' => $item->membresia,
                'fecha_inicio' => $item->fecha_inicio,
                'fecha_fin' => $item->fecha_fin,
                'estado' => $item->estado,
            ]);

        return Inertia::render('Suscripciones/Index', [
            'suscripciones' => $suscripciones,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return Inertia::render('Suscripciones/Create', [
            'usuarios' => User::select('id', 'nombre', 'email')->orderBy('nombre')->get(),
            'membresias' => Membresia::select('id', 'nombre', 'precio', 'duracion_dias')->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'membresia_id' => 'required|exists:membresia,id',
            'fecha_inicio' => 'required|date',
            'tipo_pago' => 'required|in:contado,credito',
            'cantidad_cuotas' => 'nullable|integer|min:1|max:12',
            'fechas' => 'nullable|array',
            'fechas.*' => 'nullable|date',
            'montos' => 'nullable|array',
            'montos.*' => 'nullable|numeric|min:0.01',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'exists' => 'El valor seleccionado para :attribute no existe.',
            'fechas.*.date' => 'Las fechas deben ser válidas.',
            'montos.*.numeric' => 'Los montos deben ser números.',
        ]);

        try {
            $membresia = Membresia::findOrFail($validated['membresia_id']);

            $fecha_fin = Carbon::parse($validated['fecha_inicio'])
                ->addDays($membresia->duracion_dias);

            // Crear suscripción (inactiva por defecto)
            $suscripcion = Suscripcion::create([
                'usuario_id' => $validated['usuario_id'],
                'membresia_id' => $validated['membresia_id'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'fecha_fin' => $fecha_fin,
                'estado' => 1,
                'estado_pago' => false,
                'tipo_pago' => $validated['tipo_pago'],
                'fecha_estado' => now(),
            ]);

            // Si es pago al contado, crear registro de pago pendiente
            if ($validated['tipo_pago'] === 'contado') {
                Pago::create([
                    'suscripcion_id' => $suscripcion->id,
                    'monto_abonado' => 0,
                    'monto_total_membresia' => $membresia->precio,
                    'fecha_abono' => now()->toDateString(),
                    'metodo_pago' => 'pendiente', // Cambiar de null a 'pendiente'
                    'estado_pago' => false,
                    'observaciones' => 'Pago pendiente',
                ]);
            }

            // Si es pago a crédito, crear plan de pagos con cuotas
            if ($validated['tipo_pago'] === 'credito') {
                $fechas = is_array($validated['fechas']) ? array_filter($validated['fechas']) : [];
                $montos = is_array($validated['montos']) ? array_filter($validated['montos']) : [];

                Log::info('=== CREANDO PLAN DE PAGOS ===', [
                    'suscripcion_id' => $suscripcion->id,
                    'cantidad_cuotas' => $validated['cantidad_cuotas'],
                    'fechas_count' => count($fechas),
                    'montos_count' => count($montos),
                    'fechas' => $fechas,
                    'montos' => $montos,
                    'membresia_precio' => $membresia->precio,
                ]);

                $planPago = PlanPago::create([
                    'suscripcion_id' => $suscripcion->id,
                    'monto_total' => $membresia->precio,
                    'cantidad_cuotas' => count($fechas) > 0 ? count($fechas) : $validated['cantidad_cuotas'],
                    'estado' => 'activo',
                    'fecha_inicio' => now()->toDateString(),
                ]);

                Log::info('Plan de pagos creado', ['plan_pago_id' => $planPago->id]);

                // Crear cuotas individuales
                if (count($fechas) > 0 && count($montos) > 0) {
                    $cantidadCuotas = min(count($fechas), count($montos));

                    for ($i = 0; $i < $cantidadCuotas; $i++) {
                        $fecha = $fechas[$i];
                        $monto = floatval($montos[$i]);

                        Log::info('Creando cuota', [
                            'plan_pago_id' => $planPago->id,
                            'numero_cuota' => $i + 1,
                            'monto' => $monto,
                            'fecha_vencimiento' => $fecha,
                        ]);

                        CuotaPago::create([
                            'plan_pago_id' => $planPago->id,
                            'numero_cuota' => $i + 1,
                            'monto' => $monto,
                            'fecha_vencimiento' => $fecha,
                            'estado' => 'pendiente',
                        ]);
                    }

                    Log::info('=== CUOTAS CREADAS EXITOSAMENTE ===', ['total' => $cantidadCuotas]);
                } else {
                    Log::warning('=== NO HAY FECHAS O MONTOS ===', [
                        'fechas_count' => count($fechas),
                        'montos_count' => count($montos),
                    ]);
                }
            }

            return redirect()
                ->route('suscripciones.index')
                ->with('success', 'Suscripción creada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error creando suscripción', ['error' => $e]);

            return back()->withErrors([
                'general' => 'Ocurrió un problema al crear la suscripción.',
            ]);
        }
    }

    public function edit(Suscripcion $suscripcione)
    {
        // Breeze/RouteModelBinding singulariza mal... suscripcione = OK
        return Inertia::render('Suscripciones/Edit', [
            'suscripcion' => $suscripcione,
            'usuarios' => User::select('id', 'nombre', 'email')->orderBy('nombre')->get(),
            'membresias' => Membresia::select('id', 'nombre', 'precio', 'duracion_dias')->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Suscripcion $suscripcione)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'membresia_id' => 'required|exists:membresia,id',
            'fecha_inicio' => 'required|date',
            'estado' => 'required|in:0,1',
        ]);

        try {
            $membresia = Membresia::findOrFail($validated['membresia_id']);

            $validated['fecha_fin'] = Carbon::parse($validated['fecha_inicio'])
                ->addDays($membresia->duracion_dias);

            $validated['fecha_estado'] = now();

            $suscripcione->update($validated);

            return redirect()->route('suscripciones.index')
                ->with('success', 'Suscripción actualizada correctamente.');

        } catch (\Throwable $e) {
            Log::error('Error actualizando suscripción', ['error' => $e]);

            return back()->withErrors([
                'general' => 'Ocurrió un problema al actualizar la suscripción.',
            ]);
        }
    }

    public function show(Suscripcion $suscripcione)
    {
        $suscripcione->load([
            'usuario',
            'membresia',
            'pagos' => function ($query) {
                $query->orderBy('fecha_abono', 'desc');
            },
            'planesPago' => function ($query) {
                $query->with([
                    'cuotas' => function ($q) {
                        $q->orderBy('numero_cuota');
                    }
                ]);
            }
        ]);

        $tipoPago = $suscripcione->obtenerTipoPago();
        $estadoPago = $suscripcione->obtenerEstadoPago();
        $montoTotal = $suscripcione->obtenerMontoTotal();
        $montoPagado = $suscripcione->obtenerMontoPagado();
        $montoPendiente = $suscripcione->obtenerMontoPendiente();

        return Inertia::render('Suscripciones/Show', [
            'suscripcion' => [
                'id' => $suscripcione->id,
                'usuario' => $suscripcione->usuario,
                'membresia' => $suscripcione->membresia,
                'fecha_inicio' => $suscripcione->fecha_inicio,
                'fecha_fin' => $suscripcione->fecha_fin,
                'estado' => $suscripcione->estado,
                'estado_pago' => $estadoPago,
                'tipo_pago' => $tipoPago,
                'monto_total' => $montoTotal,
                'monto_pagado' => $montoPagado,
                'monto_pendiente' => $montoPendiente,
                'porcentaje_pagado' => $montoTotal > 0 ? round(($montoPagado / $montoTotal) * 100) : 0,
            ],
            'pagos' => $suscripcione->pagos->map(fn($pago) => [
                'id' => $pago->id,
                'monto_abonado' => $pago->monto_abonado,
                'fecha_abono' => $pago->fecha_abono,
                'metodo_pago' => $pago->metodo_pago,
                'estado_pago' => $pago->estado_pago,
                'observaciones' => $pago->observaciones,
            ]),
            'planesPago' => $suscripcione->planesPago->map(fn($plan) => [
                'id' => $plan->id,
                'monto_total' => $plan->monto_total,
                'cantidad_cuotas' => $plan->cantidad_cuotas,
                'estado' => $plan->estado,
                'fecha_inicio' => $plan->fecha_inicio,
                'cuotas' => $plan->cuotas->map(fn($cuota) => [
                    'id' => $cuota->id,
                    'numero_cuota' => $cuota->numero_cuota,
                    'monto' => $cuota->monto,
                    'fecha_vencimiento' => $cuota->fecha_vencimiento,
                    'fecha_pago' => $cuota->fecha_pago,
                    'estado' => $cuota->estado,
                    'metodo_pago' => $cuota->metodo_pago,
                ]),
            ]),
        ]);
    }

    public function destroy(Suscripcion $suscripcione)
    {
        $suscripcione->delete();

        return redirect()
            ->route('suscripciones.index')
            ->with('success', 'Suscripción eliminada correctamente.');
    }

    /**
     * Confirmar pago al contado para una suscripción.
     */
    public function confirmarPagoContado(Suscripcion $suscripcion)
    {
        $user = auth()->user();
        if (!$user->is_propietario && !$user->is_secretaria) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        try {
            // Verificar si ya está pagada
            if ($suscripcion->estado_pago) {
                return back()->with('error', 'Esta suscripción ya está pagada.');
            }

            // Buscar el pago pendiente asociado
            $pago = Pago::where('suscripcion_id', $suscripcion->id)
                ->where('estado_pago', false)
                ->first();

            if (!$pago) {
                // Si no existe, crearlo
                $pago = Pago::create([
                    'suscripcion_id' => $suscripcion->id,
                    'monto_abonado' => $suscripcion->membresia->precio,
                    'monto_total_membresia' => $suscripcion->membresia->precio,
                    'fecha_abono' => now()->toDateString(),
                    'metodo_pago' => 'efectivo', // Asumimos efectivo por defecto al confirmar rápido
                    'estado_pago' => true,
                    'observaciones' => 'Pago al contado confirmado',
                ]);
            } else {
                // Actualizar pago existente
                $pago->update([
                    'monto_abonado' => $suscripcion->membresia->precio,
                    'fecha_abono' => now()->toDateString(),
                    'metodo_pago' => 'efectivo',
                    'estado_pago' => true,
                    'observaciones' => 'Pago al contado confirmado',
                ]);
            }

            // Actualizar estado de la suscripción
            $suscripcion->update([
                'estado_pago' => true,
                'estado' => 1, // Activa
            ]);

            return back()->with('success', 'Pago confirmado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al confirmar pago contado', ['error' => $e]);
            return back()->withErrors(['general' => 'Error al confirmar el pago.']);
        }
    }
}
