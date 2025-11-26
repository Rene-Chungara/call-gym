<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Suscripcion;
use App\Models\Rutina;
use App\Models\AsistenciaSesion;
use App\Models\Membresia;
use App\Models\VentaPaquete;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = null;
        $charts = null;

        // Solo el propietario ve estadísticas completas
        if ($user->is_propietario) {
            try {
                // Estadísticas básicas
                $stats = [
                    'totalUsuarios' => User::where('is_clientes', true)->count(),
                    'suscripcionesActivas' => Suscripcion::where('estado', 1)->count(),
                    'totalRutinas' => Rutina::count(),
                    'asistenciasHoy' => AsistenciaSesion::whereDate('fecha_asistencia', Carbon::today())
                        ->where('estado', 'asistio')
                        ->count(),
                    'totalIngresos' => Suscripcion::where('estado', 1)
                        ->join('membresia', 'suscripcion.membresia_id', '=', 'membresia.id')
                        ->sum('membresia.precio'),
                ];

                // Datos para gráficos
                $charts = [
                    // Membresías más populares
                    'membresiasPopulares' => DB::table('membresia')
                        ->select('membresia.nombre', DB::raw('count(suscripcion.id) as total'))
                        ->leftJoin('suscripcion', 'membresia.id', '=', 'suscripcion.membresia_id')
                        ->groupBy('membresia.id', 'membresia.nombre')
                        ->orderByDesc('total')
                        ->limit(5)
                        ->get(),

                    // Suscripciones por estado
                    'suscripcionesPorEstado' => DB::table('suscripcion')
                        ->select(
                            DB::raw("CASE WHEN estado = 1 THEN 'Activa' ELSE 'Inactiva' END as estado_nombre"),
                            DB::raw('count(*) as total')
                        )
                        ->groupBy('estado')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'estado' => $item->estado_nombre,
                                'total' => $item->total
                            ];
                        }),

                    // Asistencias últimos 7 días
                    'asistenciasUltimos7Dias' => DB::table('asistencia_sesion')
                        ->select(
                            DB::raw('DATE(fecha_asistencia) as fecha'),
                            DB::raw('count(*) as total')
                        )
                        ->where('estado', 'asistio')
                        ->where('fecha_asistencia', '>=', Carbon::now()->subDays(7))
                        ->groupBy(DB::raw('DATE(fecha_asistencia)'))
                        ->orderBy('fecha')
                        ->get(),

                    // Nuevos usuarios por mes (últimos 6 meses)
                    'nuevosUsuariosPorMes' => DB::table('usuarios')
                        ->select(
                            DB::raw("TO_CHAR(fecha_registro, 'YYYY-MM') as mes"),
                            DB::raw('count(*) as total')
                        )
                        ->where('is_clientes', true)
                        ->where('fecha_registro', '>=', Carbon::now()->subMonths(6))
                        ->groupBy(DB::raw("TO_CHAR(fecha_registro, 'YYYY-MM')"))
                        ->orderBy('mes')
                        ->get(),

                    // Paquetes más vendidos
                    'paquetesMasVendidos' => DB::table('venta_paquete')
                        ->select('paquetes.nombre', DB::raw('count(venta_paquete.id) as total'))
                        ->join('paquetes', 'venta_paquete.paquete_id', '=', 'paquetes.id')
                        ->groupBy('paquetes.id', 'paquetes.nombre')
                        ->orderByDesc('total')
                        ->limit(5)
                        ->get(),
                ];

            } catch (\Exception $e) {
                \Log::error('Error al cargar estadísticas del dashboard', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Si hay error, devolver stats en 0
                $stats = [
                    'totalUsuarios' => 0,
                    'suscripcionesActivas' => 0,
                    'totalRutinas' => 0,
                    'asistenciasHoy' => 0,
                    'totalIngresos' => 0,
                ];
                $charts = null;
            }
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'charts' => $charts,
        ]);
    }

    /**
     * Exportar reporte en PDF
     */
    public function exportPDF(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // TODO: Implementar exportación PDF con DomPDF o similar
        return response()->json(['message' => 'Funcionalidad de exportación PDF en desarrollo']);
    }

    /**
     * Exportar reporte en Excel
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        if (!$user->is_propietario) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        // TODO: Implementar exportación Excel con Laravel Excel
        return response()->json(['message' => 'Funcionalidad de exportación Excel en desarrollo']);
    }
}
