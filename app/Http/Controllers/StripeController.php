<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Pago;
use Illuminate\Support\Facades\Log;


class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        // Validación de la solicitud
        $validated = $request->validate([
            'suscripcion_id' => 'required|exists:suscripcion,id',
            'monto_total' => 'required|numeric|min:1',
            'monto_abono' => 'required|numeric|min:1',
        ]);

        // Configura Stripe con tu clave secreta
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Crear sesión de pago de Stripe
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'bob', 
                    'product_data' => [
                        'name' => 'Pago para suscripción',
                    ],
                    'unit_amount' => intval($validated['monto_abono'] * 100),  // La cantidad debe ser en centavos
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('pagos.success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
            'cancel_url' => route('pagos.cancel'),
        ]);

        // Redirigir a Stripe
        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        return view('pagos.success');
    }

    public function cancel(Request $request)
    {
        return view('pagos.cancel');
    }
}
