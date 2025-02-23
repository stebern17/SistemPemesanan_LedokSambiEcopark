<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\DiningTable;
use App\Models\Payment;
use App\Models\User;
use Midtrans\Config;
use Midtrans\Snap;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;

class MenuUserController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // Page Welcome
    public function index(Request $request)
    {
        // Ambil kategori dari query string, default ke 'food'
        $category = $request->query('category', 'food');

        // Ambil data menu berdasarkan kategori
        $menus = Menu::where('category', $category)->get();

        // Kembalikan view dengan data menu dan kategori
        return view('welcome', compact('menus', 'category'));
    }

    // Page Checkout
    public function checkout(Request $request)
    {
        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null // Tambahkan tableId ke dalam cartData
        ]);

        // Retrieve dining tables from the query string, default to an empty array
        $diningTable = DiningTable::all();

        // Pass cart data and dining table data to the checkout view
        return view('checkout', compact('cartData', 'diningTable'));
    }

    // Page Welcome
    public function addToCart(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        // Ambil data keranjang dari session, atau inisialisasi jika tidak ada
        $cartData = session('cartData')     ?? [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null
        ];

        // Ambil detail item dari request
        $itemName = $request->input('name');
        $itemPrice = $request->input('price');

        // Cek apakah item sudah ada di keranjang
        $itemIndex = array_search($itemName, array_column($cartData['items'], 'name'));

        if ($itemIndex !== false) {
            // Jika item sudah ada, tingkatkan kuantitas
            $cartData['items'][$itemIndex]['quantity']++;
        } else {
            // Jika item belum ada, tambahkan ke keranjang
            $cartData['items'][] = [
                'name' => $itemName,
                'price' => $itemPrice,
                'quantity' => 1
            ];
        }

        // Perbarui jumlah item dan total harga
        $cartData['itemCount']++;
        $cartData['totalPrice'] += $itemPrice;

        // Simpan data keranjang yang diperbarui ke session
        session(['cartData' => $cartData]);
        // Kembalikan respons JSON dengan data keranjang yang diperbarui
        return response()->json([
            'itemCount' => $cartData['itemCount'],
            'totalPrice' => $cartData['totalPrice'],
            'items' => $cartData['items'] // Menyertakan detail item jika diperlukan
        ]);
    }

    // Page Checkout
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null
        ]);

        $itemName = $request->input('name');

        // Find the item index in the cart
        $itemIndex = array_search($itemName, array_column($cartData['items'], 'name'));

        if ($itemIndex !== false) {
            // Remove the item from the cart
            $itemQuantity = $cartData['items'][$itemIndex]['quantity'];
            $itemPrice = $cartData['items'][$itemIndex]['price'];

            // Update item count and total price
            $cartData['itemCount'] -= $itemQuantity;
            $cartData['totalPrice'] -= $itemPrice * $itemQuantity;

            // Remove the item from the items array
            array_splice($cartData['items'], $itemIndex, 1);
        }

        // Save updated cart data to session
        session(['cartData' => $cartData]);

        return response()->json([
            'success' => true,
            'cartData' => $cartData
        ]);
    }

    // Get Cart Data Page Welcome
    public function getCartData()
    {
        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null
        ]);

        return response()->json($cartData);
    }

    // Update Cart Quantity page Checkout
    public function updateCartQuantity(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric'
        ]);

        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null
        ]);

        $totalItems = 0;
        $totalPrice = 0;

        // Update the quantity and recalculate totals
        foreach ($cartData['items'] as &$item) {
            if ($item['name'] === $request->name) {
                $item['quantity'] = $request->quantity;
            }
            $totalItems += $item['quantity'];
            $totalPrice += $item['price'] * $item['quantity'];
        }

        // Update cart totals
        $cartData['itemCount'] = $totalItems;
        $cartData['totalPrice'] = $totalPrice;

        // Save updated cart data to session
        session(['cartData' => $cartData]);

        return response()->json([
            'success' => true,
            'cartData' => $cartData
        ]);
    }

    // Page Checkout
    public function doCheckout()
    {
        // Get the cart data from the session
        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null
        ]);

        // Clear the cart data from the session
        session(['cartData' => null]);

        // Create a new order
        $order = Order::create([
            'id' => uniqid(),
            'dining_table_id' => $cartData['tableId'],
            'status' => 'waiting',
            'is_paid' => true,
        ]);

        DiningTable::find($cartData['tableId'])->update(['status' => 'unavailable']);

        // Data transaksi
        $transactionDetails = [
            'order_id' => $order->id,
            'gross_amount' => $cartData['totalPrice'], // Jumlah yang harus dibayar
        ];

        $itemDetails = [];
        $totalAmount = 0;
        foreach ($cartData['items'] as $item) {
            $itemDetails[] = [
                'id' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
            ];
            $amount = $item['price'] * $item['quantity'];

            OrderDetail::create([
                'order_id' => $order->id,
                'menu_id' => Menu::where('name', $item['name'])->first()->id,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total_amount' => $amount,
            ]);
            $totalAmount = $totalAmount + $amount;
        }

        Notification::make()
            ->success()
            ->title('Order Created')
            ->sendToDatabase(User::where('role', 'admin')->get());

        Payment::create([
            'order_id' => $order->id,
            'method' => 'cashless',
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        $transactionData = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
        ];

        // Mengambil URL pembayaran
        $snapToken = Snap::getSnapToken($transactionData);



        return response()->json(['snapToken' => $snapToken]);
    }


    // Fungsi untuk menangani pembayaran tunai
    public function doCashCheckout()
    {
        // Get the cart data from the session
        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null
        ]);

        // Clear the cart data from the session
        session(['cartData' => null]);

        // Create a new order
        $order = Order::create([
            'id' => uniqid(),
            'dining_table_id' => $cartData['tableId'],
            'status' => 'waiting',
            'is_paid' => false,
        ]);

        DiningTable::find($cartData['tableId'])->update(['status' => 'unavailable']);


        $itemDetails = [];
        $totalAmount = 0;
        foreach ($cartData['items'] as $item) {
            $itemDetails[] = [
                'id' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
            ];
            $amount = $item['price'] * $item['quantity'];

            OrderDetail::create([
                'order_id' => $order->id,
                'menu_id' => Menu::where('name', $item['name'])->first()->id,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total_amount' => $amount,
            ]);
            $totalAmount = $totalAmount + $amount;
        }

        Notification::make()
            ->success()
            ->title('Order Created')
            ->sendToDatabase(User::where('role', 'admin')->get());

        Payment::create([
            'order_id' => $order->id,
            'method' => 'cash',
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Redirect ke halaman invoice dengan order ID
        return response()->json([
            'success' => true,
            'orderId' => $order->id,
            'redirectUrl' => route('cashPayment', ['orderId' => $order->id])
        ]);
    }

    // Fungsi untuk menampilkan halaman invoice
    public function showInvoice($orderId)
    {
        // Retrieve the order details based on the order ID
        $orderDetails = OrderDetail::with('order.diningTable')->where('order_id', $orderId)->get();

        // Check if any order details were found
        if ($orderDetails->isEmpty()) {
            return redirect()->route('checkout')->with('error', 'Order not found.');
        }

        // Get the order from the first order detail
        $order = $orderDetails->first()->order;

        // Return the view with the order and its details
        return view('cashPayment', compact('order', 'orderDetails'));
    }





    // Fungsi untuk menyimpan nomor meja
    public function saveTable(Request $request)
    {
        $tableNumber = $request->input('tableNumber');
        $tableId = $request->input('tableId');

        // Ambil data keranjang dari session, atau inisialisasi jika tidak ada
        $cartData = session('cartData') ?? [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
            'tableId' => null
        ];

        // Simpan nomor meja ke dalam data keranjang
        $cartData['tableNumber'] = $tableNumber;
        $cartData['tableId'] = $tableId;


        // Simpan data keranjang yang diperbarui ke session
        session(['cartData' => $cartData]);

        return response()->json(['status' => 'success', 'message' => 'Table number saved successfully', 'tableNumber' => $tableNumber, 'tableId' => $tableId, 'order item' => $cartData]);
    }

    public function printReceipt(Order $order)
    {

        $items = $order->items()->with('menu')->get();
        $total = $items->sum('total_amount');

        $pdf = Pdf::loadView('receipt', [
            'order' => $order,
            'items' => $items,
            'total' => $total,
            'date' => now()->format('d/m/Y H:i:s'),
            'receipt_number' => sprintf('RCP-%s-%s', $order->id, now()->format('YmdHis'))
        ])
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        $fileName = sprintf('Order-%s.pdf', $order->id);

        return $pdf->stream($fileName, ['attachment' => false]);
    }
}
// public function debug()
// {
//     $cartData = session('cartData', [
//         'itemCount' => 0,
//         'totalPrice' => 0,
//         'items' => [],
//         'tableNumber' => null, // Tambahkan tableNumber ke dalam cartData
//         'tableId' => null
//     ]);

//     dd($cartData);
// }
