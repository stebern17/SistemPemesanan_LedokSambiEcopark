<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class WebhookMidtrans extends Controller
{
    public function payment(Request $request)
    {
        $order = Payment::where('order_id', $request->order_id)->firstOrFail();

        $order->update([
            'status' => $request->transaction_status,
        ]);

        if ($request->transaction_status == 'settlement') {
            Order::where('id', $request->order_id)->update([
                'is_paid' => true,
            ]);
        }
    }
}
