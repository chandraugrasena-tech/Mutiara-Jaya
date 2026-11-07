<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik: lihat menu dan keterangan toko (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [HomeController::class, 'show'])->name('product.show');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');

/*
|--------------------------------------------------------------------------
| Login / register / logout
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Wajib login: promo, keranjang, lokasi (alamat), checkout, dan pesanan
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/promo', [HomeController::class, 'promo'])->name('promo');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/promo', [CartController::class, 'applyPromo'])->name('cart.promo');
    Route::delete('/cart/promo', [CartController::class, 'removePromo'])->name('cart.promo.remove');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::post('/addresses/{address}/select', [AddressController::class, 'select'])->name('addresses.select');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
});

/*
|--------------------------------------------------------------------------
| Halaman admin - wajib login + role admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::redirect('/', '/admin/dashboard');
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::resource('products', Admin\ProductController::class)->except('show');
    Route::resource('promos', Admin\PromoController::class)->except('show');

    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');

    Route::get('/pemasukan', [Admin\RevenueController::class, 'index'])->name('revenue');

    Route::get('/stok', [Admin\StockController::class, 'index'])->name('stock.index');
    Route::patch('/stok/{product}', [Admin\StockController::class, 'adjust'])->name('stock.adjust');

    Route::get('/keterangan-toko', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/keterangan-toko', [Admin\SettingController::class, 'update'])->name('settings.update');
});
