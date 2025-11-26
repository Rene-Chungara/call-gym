<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptimizeQueries
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Log queries en desarrollo
        if (config('app.debug')) {
            DB::listen(function ($query) {
                // Aquí puedes agregar logging de queries lentas
                if ($query->time > 1000) { // queries > 1 segundo
                    \Log::warning('Slow query detected', [
                        'query' => $query->sql,
                        'time' => $query->time,
                    ]);
                }
            });
        }

        return $next($request);
    }
}
