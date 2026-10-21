<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products/import', [ProductController::class, 'importProducts']);
    Route::post('/products/import', [ProductController::class, 'importBatch']);

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
});

Route::get('/run-migration', function () {
    Artisan::call('migrate', ["--force" => true]);
    return "Migration output: " . Artisan::output();
});

require __DIR__.'/auth.php';
