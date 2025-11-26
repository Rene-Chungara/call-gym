<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\Pago;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $request->header('Stripe-Signature'),
                $endpointSecret
            );
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $pago = Pago::where('stripe_session_id', $session->id)->first();

            if ($pago) {
                $montoPagado = $session->amount_total / 100;

                $nuevoSaldo = max(0, $pago->monto_total_membresia - $montoPagado);

                $pago->update([
                    'stripe_payment_id' => $session->payment_intent,
                    'stripe_status' => 'paid',
                    'monto_abonado' => $montoPagado,
                    'saldo_pendiente' => $nuevoSaldo,
                    'estado_pago' => $nuevoSaldo == 0,
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
