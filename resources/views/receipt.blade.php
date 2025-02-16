<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('images/Logo Ledok Sambi.png') }}" alt="Ledok Sambi Ecopark">
        <p>{{ $receipt_number }}</p>
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