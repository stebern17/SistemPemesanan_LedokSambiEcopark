<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\DiningTable;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;

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
            'tableNumber' => null // Tambahkan tableNumber ke dalam cartData
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
            'tableNumber' => null // Tambahkan tableNumber ke dalam cartData
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
            'tableNumber' => null // Tambahkan tableNumber ke dalam cartData
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
            'tableNumber' => null // Tambahkan tableNumber ke dalam cartData
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
            'tableNumber' => null // Tambahkan tableNumber ke dalam cartData
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
            'tableNumber' => null // Tambahkan tableNumber ke dalam cartData
        ]);

        // Clear the cart data from the session
        session(['cartData' => null]);

        // Data transaksi
        $transactionDetails = [
            'order_id' => 'order-' . time(),
            'gross_amount' => $cartData['totalPrice'], // Jumlah yang harus dibayar
        ];

        $itemDetails = [];
        foreach ($cartData['items'] as $item) {
            $itemDetails[] = [
                'id' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'name' => $item['name'],
            ];
        }

        $customerDetails = [
            'first_name' => "John",
            'last_name' => "Doe",
            'email' => "john.doe@example.com",
            'phone' => "081234567890",
            'billing_address' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'address' => 'Baker Street',
                'city' => 'London',
                'postal_code' => '12345',
                'phone' => '081234567890',
                'country' => 'Indonesia',
            ],
        ];

        $transactionData = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
        ];

        // Mengambil URL pembayaran
        $snapToken = Snap::getSnapToken($transactionData);

        return response()->json(['snapToken' => $snapToken]);
    }

    // Fungsi untuk menyimpan nomor meja
    public function saveTable(Request $request)
    {
        $tableNumber = $request->input('tableNumber');

        // Ambil data keranjang dari session, atau inisialisasi jika tidak ada
        $cartData = session('cartData') ?? [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => [],
            'tableNumber' => null // Tambahkan tableNumber ke dalam cartData
        ];

        // Simpan nomor meja ke dalam data keranjang
        $cartData['tableNumber'] = $tableNumber;

        // Simpan data keranjang yang diperbarui ke session
        session(['cartData' => $cartData]);

        return response()->json(['status' => 'success', 'message' => 'Table number saved successfully', 'tableNumber' => $tableNumber]);
    }
}
