<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\Order;

// Route menampilkan daftar produk
Route::get('/products', function () {
    $products = Product::all();
    return view('products', compact('products'));
});

// Route menampilkan halaman keranjang belanja
Route::get('/cart', function () {
    $cart = Session::get('cart', []);
    return view('cart', compact('cart'));
});

// Route untuk menambahkan produk ke keranjang
Route::post('/cart/add/{id}', function ($id) {
    $product = Product::find($id);
    if (!$product) {
        return redirect('/products')->with('error', 'Produk tidak ditemukan.');
    }

    $cart = Session::get('cart', []);
    $cart[$id] = [
        'name' => $product->name,
        'price' => $product->price,
        'quantity' => ($cart[$id]['quantity'] ?? 0) + 1
    ];

    Session::put('cart', $cart);
    return redirect('/cart')->with('success', 'Produk ditambahkan ke keranjang!');
});

// Route untuk checkout
Route::get('/checkout', function () {
    $cart = Session::get('cart', []);
    return view('checkout', compact('cart'));
});

// Route untuk menyimpan pesanan
Route::post('/checkout', function () {
    $cart = Session::get('cart', []);

    if (empty($cart)) {
        return redirect('/cart')->with('error', 'Keranjang belanja kosong!');
    }

    $order = Order::create([
        'user_id' => 1, // Sesuaikan dengan sistem autentikasi
        'total_price' => array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $cart))
    ]);

    Session::forget('cart'); // Hapus isi keranjang setelah checkout
    return redirect('/products')->with('success', 'Pesanan berhasil dibuat!');
});

Route::get('/', function () {
    return view('welcome');
});
