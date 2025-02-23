<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class WebhookMidtrans extends Controller
{
    public function payment(Request $request)
    {

        $order = Order::where('unique_id', $request->order_id)->firstOrFail();
        $payment = Payment::where('order_id', $order->id)->firstOrFail();

        $payment->update([
            'status' => $request->transaction_status,
        ]);

        if ($request->transaction_status == 'settlement') {
            $order->update([
                'is_paid' => true,
            ]);
        }
    }
}
