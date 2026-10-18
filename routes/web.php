<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BomController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MaterialController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

// Halaman login
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login');


// Proses login hanya untuk guest
Route::middleware('guest')->group(function () {

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.process');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');


    /*
    |--------------------------------------------------------------------------
    | Dashboard Redirect
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function (Request $request) {

        return match ($request->user()->role->name) {

            'admin' =>
            redirect()->route('admin.orders.index'),

            'produksi' =>
            redirect()->route('produksi.dashboard'),

            'bisnis owner' =>
            redirect()->route('owner.dashboard'),

            default =>
            abort(403),
        };
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {


            /*
            |--------------------------------------------------------------------------
            | Order Management
            |--------------------------------------------------------------------------
            */

                Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
                Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
                Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');


            /*
            |--------------------------------------------------------------------------
            | Master Produk & SKU
            |--------------------------------------------------------------------------
            */

            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::put('/products/{group}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{group}', [ProductController::class, 'destroy'])->name('products.destroy');


            /*
            |--------------------------------------------------------------------------
            | Bill of Materials
            |--------------------------------------------------------------------------
            */

            Route::get('/products/{sku}/bom', [BomController::class, 'index'])->name('products.bom');
            Route::post('/products/{sku}/bom', [BomController::class, 'store'])->name('products.bom.store');
            Route::put('/products/{sku}/bom/{material}', [BomController::class, 'update'])->name('products.bom.update');
            Route::delete('/products/{sku}/bom/{material}', [BomController::class, 'destroy'])->name('products.bom.destroy');


            /*
            |--------------------------------------------------------------------------
            | Kategori Produk
            |--------------------------------------------------------------------------
            */

            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');        

            Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
            Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
            Route::put('/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
            Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');  

            Route::get('/inventory', function () {
                return view('admin.inventory.index');
            })->name('inventory.index');

            Route::get('/inventory/{sku}/history', function ($sku) {
                return view('admin.inventory.history', [
                    'sku' => $sku,
                ]);
            })->name('inventory.history');

            Route::get('/inventory/material/{material}/history', function ($material) {
                return view('admin.inventory.material-history', [
                    'material' => $material,
                ]);
            })->name('inventory.material-history');
        });


    /*
    |--------------------------------------------------------------------------
    | PRODUKSI
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:produksi')
        ->prefix('produksi')
        ->name('produksi.')
        ->group(function () {

            Route::get('/dashboard', function () {

                return 'Dashboard Produksi';
            })->name('dashboard');
        });


    /*
    |--------------------------------------------------------------------------
    | BISNIS OWNER
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:bisnis owner')
        ->prefix('owner')
        ->name('owner.')
        ->group(function () {

            Route::get('/dashboard', function () {

                return 'Dashboard Bisnis Owner';
            })->name('dashboard');
        });
});
