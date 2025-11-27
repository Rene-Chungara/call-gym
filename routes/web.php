<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\RutinaController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\VentaPaqueteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EjercicioController;
use App\Http\Controllers\PlanPagoController;
use App\Http\Controllers\CuotaPagoController;
use App\Http\Controllers\AsistenciaSesionController;
use App\Http\Controllers\RutinaSesionController;

use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\GlobalSearchController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas accesibles para todos los roles autenticados
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Reportes (Solo Propietario)
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/exportar-pdf', [ReporteController::class, 'exportarPDF'])->name('reportes.exportar-pdf');

    // Rutas para PROPIETARIO (acceso completo)
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('membresias', MembresiaController::class);
    Route::resource('suscripciones', SuscripcionController::class);
    Route::post('/suscripciones/{suscripcion}/confirmar-pago-contado', [SuscripcionController::class, 'confirmarPagoContado'])->name('suscripciones.confirmar-pago-contado');
    Route::resource('rutinas', RutinaController::class);
    Route::resource('seguimientos', SeguimientoController::class);
    Route::resource('paquetes', PaqueteController::class);
    Route::resource('venta-paquetes', VentaPaqueteController::class);
    Route::resource('ejercicios', EjercicioController::class);
    Route::resource('plan-pagos', PlanPagoController::class);
    Route::resource('cuotas-pago', CuotaPagoController::class);
    Route::resource('asistencia-sesion', AsistenciaSesionController::class);
    Route::resource('rutina-sesion', RutinaSesionController::class);

    // Rutas personalizadas para pago de cuotas
    Route::get('/cuotas-pago/{cuotaPago}/pagar', [CuotaPagoController::class, 'create'])->name('cuotas-pago.create');
    Route::post('/cuotas-pago/{cuotaPago}/pagar', [CuotaPagoController::class, 'store'])->name('cuotas-pago.store');
    Route::get('/cuotas-pago/{cuotaPago}/stripe/success', [CuotaPagoController::class, 'stripeSuccess'])->name('cuotas-pago.stripe.success');

    // Rutas para pagos al contado (Suscripciones completas)
    Route::get('/pagos/crear', [PagoController::class, 'create'])->name('pagos.create');
    Route::post('/pagos/store', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos/store', function () {
        return redirect()->route('pagos.create');
    }); // Redirect GET to create form
    Route::get('/pagos/{suscripcion}/stripe/success', [PagoController::class, 'stripeSuccess'])->name('pagos.stripe.success');

    Route::post('/rutina-sesion/store', [RutinaSesionController::class, 'store'])->name('rutina-sesion.store');
    Route::get('/rutinas/{rutina}/sesion/crear', [RutinaSesionController::class, 'create'])->name('rutina-sesion.create');
    Route::get('/rutina-sesion/{rutinaSesion}/editar', [RutinaSesionController::class, 'edit'])->name('rutina-sesion.edit');
    Route::put('/rutina-sesion/{rutinaSesion}', [RutinaSesionController::class, 'update'])->name('rutina-sesion.update');
    Route::delete('/rutina-sesion/{rutinaSesion}', [RutinaSesionController::class, 'destroy'])->name('rutina-sesion.destroy');

    // Ruta para que clientes vean su progreso de rutina
    Route::get('/mi-progreso', [RutinaController::class, 'miProgreso'])->name('rutinas.mi-progreso');

    // Buscador Global
    Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global.search');

});


// PAGOS CON STRIPE (fuera de Inertia para evitar CORS)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/cuotas-pago/{cuotaPago}/pagar-tarjeta', [CuotaPagoController::class, 'store'])->name('cuotas-pago.store-card')->withoutMiddleware(\App\Http\Middleware\HandleInertiaRequests::class);
    Route::post('/pagos/pagar-tarjeta', [PagoController::class, 'store'])->name('pagos.store-card')->withoutMiddleware(\App\Http\Middleware\HandleInertiaRequests::class);

    // PagoFácil - Suscripciones
    Route::post('/pagos/pagofacil/callback', [PagoController::class, 'callbackPagoFacil'])
        ->name('pagos.pagofacil.callback')
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

    Route::post('/pagos/pagofacil/consultar', [PagoController::class, 'consultarEstadoPagoFacil'])
        ->name('pagos.pagofacil.consultar');

    // PagoFácil - Cuotas
    Route::post('/cuotas-pago/pagofacil/callback', [CuotaPagoController::class, 'callbackPagoFacil'])
        ->name('cuotas-pago.pagofacil.callback')
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

    Route::post('/cuotas-pago/pagofacil/consultar', [CuotaPagoController::class, 'consultarEstadoPagoFacil'])
        ->name('cuotas-pago.pagofacil.consultar');
});

// PAGOS CON STRIPE
Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('pagos.stripe');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
Route::get('/stripe/success', [StripeController::class, 'success'])->name('pagos.success');
Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('pagos.cancel');

require __DIR__ . '/auth.php';
