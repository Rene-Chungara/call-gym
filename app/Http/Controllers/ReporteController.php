<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Suscripcion;
use App\Models\Membresia;
use App\Models\VentaPaquete;
use App\Models\Paquete;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // Filtros
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $tipo = $request->input('tipo', 'todos');

        try {
            // Consultas base con filtros de fecha
            $suscripcionesQuery = Suscripcion::select('usuarios.nombre', 'usuarios.email', 'membresia.nombre as membresia', 'suscripcion.fecha_inicio', 'membresia.precio')
                ->join('usuarios', 'suscripcion.usuario_id', '=', 'usuarios.id')
                ->join('membresia', 'suscripcion.membresia_id', '=', 'membresia.id')
                ->whereBetween('suscripcion.fecha_inicio', [$fechaInicio, $fechaFin])
                ->orderBy('suscripcion.fecha_inicio', 'desc');

            $paquetesQuery = VentaPaquete::select('usuarios.nombre as usuario', 'paquetes.nombre as paquete', 'venta_paquete.fecha_compra', 'paquetes.precio')
                ->join('usuarios', 'venta_paquete.usuario_id', '=', 'usuarios.id')
                ->join('paquetes', 'venta_paquete.paquete_id', '=', 'paquetes.id')
                ->whereBetween('venta_paquete.fecha_compra', [$fechaInicio, $fechaFin])
                ->orderBy('venta_paquete.fecha_compra', 'desc');

            $membresiasQuery = DB::table('suscripcion')
                ->select('membresia.nombre', DB::raw('count(*) as total'), DB::raw('sum(membresia.precio) as ingresos'))
                ->join('membresia', 'suscripcion.membresia_id', '=', 'membresia.id')
                ->whereBetween('suscripcion.fecha_inicio', [$fechaInicio, $fechaFin])
                ->groupBy('membresia.id', 'membresia.nombre')
                ->orderByDesc('total');

            // Ejecutar consultas según tipo
            $usuariosSuscritos = ($tipo === 'todos' || $tipo === 'suscripciones') ? $suscripcionesQuery->get() : [];
            $paquetesAdquiridos = ($tipo === 'todos' || $tipo === 'paquetes') ? $paquetesQuery->get() : [];
            $membresiasMasVendidas = ($tipo === 'todos' || $tipo === 'suscripciones') ? $membresiasQuery->get() : [];

            // Resumen
            $resumen = [
                'totalSuscripciones' => ($tipo === 'todos' || $tipo === 'suscripciones') ? $suscripcionesQuery->count() : 0,
                'totalPaquetesVendidos' => ($tipo === 'todos' || $tipo === 'paquetes') ? $paquetesQuery->count() : 0,
                'ingresosSuscripciones' => ($tipo === 'todos' || $tipo === 'suscripciones') ? $suscripcionesQuery->sum('membresia.precio') : 0,
                'ingresosPaquetes' => ($tipo === 'todos' || $tipo === 'paquetes') ? $paquetesQuery->sum('paquetes.precio') : 0,
            ];

            $reportes = [
                'usuariosSuscritos' => $usuariosSuscritos,
                'paquetesAdquiridos' => $paquetesAdquiridos,
                'membresiasMasVendidas' => $membresiasMasVendidas,
                'resumen' => $resumen,
            ];

        } catch (\Exception $e) {
            \Log::error('Error al cargar reportes', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $reportes = [
                'usuariosSuscritos' => [],
                'paquetesAdquiridos' => [],
                'membresiasMasVendidas' => [],
                'resumen' => [
                    'totalSuscripciones' => 0,
                    'totalPaquetesVendidos' => 0,
                    'ingresosSuscripciones' => 0,
                    'ingresosPaquetes' => 0,
                ],
            ];
        }

        return Inertia::render('Reportes/Index', [
            'reportes' => $reportes,
            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'tipo' => $tipo,
            ],
        ]);
    }

    public function exportarPDF(Request $request)
    {
        $user = Auth::user();

        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());
        $tipo = $request->input('tipo', 'todos');

        try {
            // Reutilizar lógica de consultas (idealmente mover a un servicio)
            $suscripcionesQuery = Suscripcion::select('usuarios.nombre', 'usuarios.email', 'membresia.nombre as membresia', 'suscripcion.fecha_inicio', 'membresia.precio')
                ->join('usuarios', 'suscripcion.usuario_id', '=', 'usuarios.id')
                ->join('membresia', 'suscripcion.membresia_id', '=', 'membresia.id')
                ->whereBetween('suscripcion.fecha_inicio', [$fechaInicio, $fechaFin])
                ->orderBy('suscripcion.fecha_inicio', 'desc');

            $paquetesQuery = VentaPaquete::select('usuarios.nombre as usuario', 'paquetes.nombre as paquete', 'venta_paquete.fecha_compra', 'paquetes.precio')
                ->join('usuarios', 'venta_paquete.usuario_id', '=', 'usuarios.id')
                ->join('paquetes', 'venta_paquete.paquete_id', '=', 'paquetes.id')
                ->whereBetween('venta_paquete.fecha_compra', [$fechaInicio, $fechaFin])
                ->orderBy('venta_paquete.fecha_compra', 'desc');

            $membresiasQuery = DB::table('suscripcion')
                ->select('membresia.nombre', DB::raw('count(*) as total'), DB::raw('sum(membresia.precio) as ingresos'))
                ->join('membresia', 'suscripcion.membresia_id', '=', 'membresia.id')
                ->whereBetween('suscripcion.fecha_inicio', [$fechaInicio, $fechaFin])
                ->groupBy('membresia.id', 'membresia.nombre')
                ->orderByDesc('total');

            $usuariosSuscritos = ($tipo === 'todos' || $tipo === 'suscripciones') ? $suscripcionesQuery->get() : collect([]);
            $paquetesAdquiridos = ($tipo === 'todos' || $tipo === 'paquetes') ? $paquetesQuery->get() : collect([]);
            $membresiasMasVendidas = ($tipo === 'todos' || $tipo === 'suscripciones') ? $membresiasQuery->get() : collect([]);

            $resumen = [
                'totalSuscripciones' => $usuariosSuscritos->count(),
                'totalPaquetesVendidos' => $paquetesAdquiridos->count(),
                'ingresosSuscripciones' => $usuariosSuscritos->sum('precio'),
                'ingresosPaquetes' => $paquetesAdquiridos->sum('precio'),
            ];

            $data = [
                'usuariosSuscritos' => $usuariosSuscritos,
                'paquetesAdquiridos' => $paquetesAdquiridos,
                'membresiasMasVendidas' => $membresiasMasVendidas,
                'resumen' => $resumen,
                'periodo' => Carbon::parse($fechaInicio)->format('d/m/Y') . ' - ' . Carbon::parse($fechaFin)->format('d/m/Y'),
                'fechaGeneracion' => Carbon::now()->locale('es')->isoFormat('DD [de] MMMM [de] YYYY, HH:mm'),
            ];

            $pdf = Pdf::loadView('reportes.mensual', $data);

            return $pdf->download('reporte-' . $fechaInicio . '-al-' . $fechaFin . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Error al generar el PDF: ' . $e->getMessage()]);
        }
    }
}
