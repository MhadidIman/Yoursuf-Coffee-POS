<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

// Autentikasi Single Owner (Pemilik Yoursuf Coffee)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Seluruh Halaman Kasir POS & Admin Backoffice Dikunci dengan Middleware 'auth'
Route::middleware('auth')->group(function () {
    // Halaman Utama Kasir (POS) Yoursuf Coffee
    Route::get('/', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/orders', [PosController::class, 'getOrders'])->name('pos.orders.index');
    Route::post('/pos/orders', [PosController::class, 'storeOrder'])->name('pos.orders.store');
    Route::patch('/pos/orders/{order}/status', [PosController::class, 'updateStatus'])->name('pos.orders.update-status');

    // Halaman Admin / Backoffice Yoursuf Coffee
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Filter Tanggal Custom & Export Laporan Keuangan (Excel CSV)
        Route::get('/finance/custom-stats', [AdminController::class, 'getCustomFinanceStats'])->name('finance.custom-stats');
        Route::get('/finance/export-csv', [AdminController::class, 'exportFinanceCsv'])->name('finance.export-csv');

        // Pencatatan Pengeluaran (Expense Tracker)
        Route::post('/expenses', [AdminController::class, 'storeExpense'])->name('expenses.store');
        Route::put('/expenses/{expense}', [AdminController::class, 'updateExpense'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [AdminController::class, 'destroyExpense'])->name('expenses.destroy');

        // Manajemen Menu & HPP (Products)
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
        Route::patch('/products/{product}/toggle', [AdminController::class, 'toggleProductAvailability'])->name('products.toggle');
        Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');

        // Pengingat Stok Bahan Utama (Ingredients)
        Route::post('/ingredients', [AdminController::class, 'storeIngredient'])->name('ingredients.store');
        Route::put('/ingredients/{ingredient}', [AdminController::class, 'updateIngredient'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [AdminController::class, 'destroyIngredient'])->name('ingredients.destroy');

        // Riwayat & Pembatalan Transaksi (Void Order)
        Route::delete('/orders/{order}', [AdminController::class, 'destroyOrder'])->name('orders.destroy');
    });
});
