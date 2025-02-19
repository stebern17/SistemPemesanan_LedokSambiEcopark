<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Payment</title>
    @vite ('resources/css/app.css')
</head>

<body class="bg-gray-100">
    <div class="invoice max-w-4xl mx-auto bg-white p-6 shadow-md rounded-lg mt-10">
        <h2 class="text-2xl font-bold text-center mb-6">Invoice</h2>
        <div class="mb-6">
            <p class="text-gray-700"><strong>Order ID:</strong> {{ $order->id }}</p>
            <p class="text-gray-700"><strong>Table Number:</strong> {{ $order->dining_table->number }}</p>
            <p class="text-gray-700"><strong>Date:</strong> {{ $order->created_at->format('d M Y H:i:s') }}</p>
        </div>
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Item</th>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Price</th>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Quantity</th>
                    <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $detail)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $detail->menu->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ number_format($detail->price, 2) }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $detail->quantity }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ number_format($detail->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="border border-gray-300 px-4 py-2 font-bold text-right">Total:</td>
                    <td class="border border-gray-300 px-4 py-2 font-bold">{{ number_format($order->orderDetails->sum('total_amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>