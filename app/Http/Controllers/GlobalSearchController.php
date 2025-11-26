<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Suscripcion;
use App\Models\Rutina;
use App\Models\Paquete;
use App\Models\Ejercicio;
use App\Models\Membresia;
use Illuminate\Support\Facades\Route;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Usuarios
        $usuarios = User::where('nombre', 'ilike', "%{$query}%")
            ->orWhere('email', 'ilike', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'title' => $user->nombre,
                    'subtitle' => $user->email,
                    'type' => 'Usuario',
                    'url' => route('usuarios.index', ['search' => $user->nombre]),
                    'category' => 'Usuarios'
                ];
            });

        // Suscripciones (por nombre de usuario o membresía)
        $suscripciones = Suscripcion::select('suscripcion.*')
            ->join('usuarios', 'suscripcion.usuario_id', '=', 'usuarios.id')
            ->join('membresia', 'suscripcion.membresia_id', '=', 'membresia.id')
            ->where('usuarios.nombre', 'ilike', "%{$query}%")
            ->orWhere('membresia.nombre', 'ilike', "%{$query}%")
            ->limit(3)
            ->with(['usuario', 'membresia'])
            ->get()
            ->map(function ($suscripcion) {
                return [
                    'id' => $suscripcion->id,
                    'title' => $suscripcion->usuario->nombre,
                    'subtitle' => $suscripcion->membresia->nombre . ' (' . ($suscripcion->estado_pago ? 'Pagado' : 'Pendiente') . ')',
                    'type' => 'Suscripción',
                    'url' => route('suscripciones.index', ['search' => $suscripcion->usuario->nombre]),
                    'category' => 'Suscripciones'
                ];
            });

        // Rutinas
        $rutinas = Rutina::where('nombre', 'ilike', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($rutina) {
                return [
                    'id' => $rutina->id,
                    'title' => $rutina->nombre,
                    'subtitle' => $rutina->descripcion ? substr($rutina->descripcion, 0, 30) . '...' : 'Rutina de entrenamiento',
                    'type' => 'Rutina',
                    'url' => route('rutinas.index', ['search' => $rutina->nombre]),
                    'category' => 'Rutinas'
                ];
            });

        // Paquetes
        $paquetes = Paquete::where('nombre', 'ilike', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($paquete) {
                return [
                    'id' => $paquete->id,
                    'title' => $paquete->nombre,
                    'subtitle' => '$' . $paquete->precio,
                    'type' => 'Paquete',
                    'url' => route('paquetes.index', ['search' => $paquete->nombre]),
                    'category' => 'Paquetes'
                ];
            });

        // Ejercicios
        $ejercicios = Ejercicio::where('nombre', 'ilike', "%{$query}%")
            ->orWhere('grupo_muscular', 'ilike', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($ejercicio) {
                return [
                    'id' => $ejercicio->id,
                    'title' => $ejercicio->nombre,
                    'subtitle' => $ejercicio->grupo_muscular,
                    'type' => 'Ejercicio',
                    'url' => route('ejercicios.index', ['search' => $ejercicio->nombre]),
                    'category' => 'Ejercicios'
                ];
            });

        // Membresias
        $membresias = Membresia::where('nombre', 'ilike', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($membresia) {
                return [
                    'id' => $membresia->id,
                    'title' => $membresia->nombre,
                    'subtitle' => '$' . $membresia->precio,
                    'type' => 'Membresía',
                    'url' => route('membresias.index', ['search' => $membresia->nombre]),
                    'category' => 'Membresías'
                ];
            });

        return response()->json([
            ...$usuarios,
            ...$suscripciones,
            ...$rutinas,
            ...$paquetes,
            ...$ejercicios,
            ...$membresias
        ]);
    }
}
