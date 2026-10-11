<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Halaman login: selalu bisa dibuka (session lama dihapus oleh controller)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Proses login: hanya untuk tamu
Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function (Request $request) {
        return match ($request->user()->role->name) {
            'admin'        => redirect()->route('admin.orders.index'),
            'produksi'     => redirect()->route('produksi.dashboard'),
            'bisnis owner' => redirect()->route('owner.dashboard'),
            default        => abort(403),
        };
    })->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/orders', fn () => view('admin.orders.index'))->name('orders.index');
    });

    Route::middleware('role:produksi')->prefix('produksi')->name('produksi.')->group(function () {
        Route::get('/dashboard', fn () => 'Dashboard Produksi')->name('dashboard');
    });

    Route::middleware('role:bisnis owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', fn () => 'Dashboard Bisnis Owner')->name('dashboard');
    });
});