<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller
{
    // POST /payments/{order_id} - Process payment
    public function store(Request $request, $order_id)
    {
        $order = Order::with('medicines')->findOrFail($order_id);

        if ($order->payment()->exists()) {
            return response()->json(['message' => 'Payment already initiated'], 400);
        }

        $lineItems = [];
        foreach ($order->medicines as $medicine) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $medicine->name,
                    ],
                    'unit_amount' => $medicine->price * 100,
                ],
                'quantity' => $medicine->pivot->quantity,
            ];
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => url('/api/payments/success?payment_id=' . $order->id),
            'cancel_url' => url('/api/payments/cancel?payment_id=' . $order->id),
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'stripe_payment_id' => $session->id,
            'status' => 'pending',
            'amount' => $order->total_price,
        ]);

        return response()->json([
            'message' => 'Payment initiated',
            'payment' => $payment->fresh(), // Ensures all fields (like stripe_payment_id) are included
            'checkout_url' => $session->url,
        ], 201);
    }

    // GET /payments/{id} - View payment status
    public function show($id)
    {
        $payment = Payment::with('order')->findOrFail($id);
        return response()->json([
            'message' => 'Payment details retrieved',
            'payment' => $payment,
        ]);
    }

    // POST /payments/{id}/refund - Refund payment
    public function refund($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'paid') {
            return response()->json(['message' => 'Cannot refund unpaid or already refunded payment'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $refund = \Stripe\Refund::create([
            'charge' => $payment->stripe_payment_id,
        ]);

        $payment->update(['status' => 'refunded']);
        return response()->json([
            'message' => 'Payment refunded',
            'payment' => $payment,
        ]);
    }

    // Success callback (not in your routes, for Stripe redirect)
    public function success(Request $request)
    {
        $payment = Payment::where('id', $request->query('payment_id'))->firstOrFail();
        $payment->update(['status' => 'paid']);
        return response()->json(['message' => 'Payment successful', 'payment' => $payment]);
    }

    // Cancel callback (not in your routes, for Stripe redirect)
    public function cancel(Request $request)
    {
        $payment = Payment::where('id', $request->query('payment_id'))->firstOrFail();
        $payment->update(['status' => 'failed']);
        return response()->json(['message' => 'Payment canceled', 'payment' => $payment]);
    }
}
