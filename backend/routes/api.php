
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NilaiController;
use App\Models\Setting;

/*
|--------------------------------------------------------------------------
| API Nilai Mahasiswa
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/nilai', [NilaiController::class, 'index']);
Route::post('/nilai', [NilaiController::class, 'store']);

/*
|--------------------------------------------------------------------------
| REST API Mutiara Jaya
|--------------------------------------------------------------------------
*/

// Register dan Login (publik)
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Data produk dan kategori (publik)
Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'show']);

Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);

// Data promo (publik)
Route::get('/promos', [\App\Http\Controllers\Api\PromoController::class, 'index']);

// Informasi toko (publik, tanpa login)
Route::get('/store-info', function () {
    $keys = [
        'store_name',
        'store_tagline',
        'store_description',
        'store_address',
        'store_hours',
        'store_phone',
        'store_whatsapp',
        'store_email',
        'delivery_area',
        'delivery_fee',
    ];

    $info = [];

    foreach ($keys as $key) {
        $info[$key] = Setting::val($key);
    }

    return response()->json($info);
});

/*
|--------------------------------------------------------------------------
| API yang Membutuhkan Login (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Profil dan Logout
    Route::get('/profile', [\App\Http\Controllers\Api\AuthController::class, 'profile']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    // Pesanan
    Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'store']);
    Route::get('/orders/{order}', [\App\Http\Controllers\Api\OrderController::class, 'show']);

    // Khusus Admin
    Route::middleware('admin')->group(function () {
        Route::apiResource('products', \App\Http\Controllers\Api\ProductController::class)
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class)
            ->only(['store', 'update', 'destroy']);
    });

});
