<!DOCTYPE html>
<html>

<head>
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> -->
    <title>Order Receipt</title>
    <link rel="stylesheet" href="{{ public_path('receipt.css') }}" type="text/css">
</head>

<body>
    <div class="header text-white">
        <img src="{{ public_path('images/Logo Ledok Sambi.png') }}" alt="Ledok Sambi Ecopark" class="logo">
        <p class="ReceiptNumber">{{ $receipt_number }}</p>
    </div>

    <div class="receipt-info">
        <p><strong>Date:</strong> {{ $date }}</p>
        <p><strong>Order ID:</strong> {{ $order->id }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ ucfirst($item->menu->name) }}</td>
                <td>{{ ucfirst($item->menu->category) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rp. {{ number_format($item->menu->price, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($item->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: Rp. {{ number_format($total, 0, ',', '.') }}
    </div>
</body>

</html>