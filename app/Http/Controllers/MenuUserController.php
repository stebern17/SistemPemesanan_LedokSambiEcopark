<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuUserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kategori dari query string, default ke 'food'
        $category = $request->query('category', 'food');

        // Ambil data menu berdasarkan kategori
        $menus = Menu::where('category', $category)->get();

        // Kembalikan view dengan data menu dan kategori
        return view('welcome', compact('menus', 'category'));
    }

    public function checkout()
    {
        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => []
        ]);

        // Pass cart data to the checkout view
        return view('checkout', compact('cartData'));
    }

    public function addToCart(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        // Ambil data keranjang dari session, atau inisialisasi jika tidak ada
        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => []
        ]);

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
    public function getCartData()
    {
        $cartData = session('cartData', [
            'itemCount' => 0,
            'totalPrice' => 0,
            'items' => []
        ]);

        return response()->json($cartData);
    }

    // In MenuUserController.php
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
            'items' => []
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
}
