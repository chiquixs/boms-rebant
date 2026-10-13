```php
<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

            Route::get('/orders', function () {

                return view('admin.orders.index');
            })->name('orders.index');


            Route::get('/orders/{id}', function ($id) {

                return view('admin.orders.show', [
                    'orderId' => $id,
                ]);
            })->name('orders.show');


            /*
            |--------------------------------------------------------------------------
            | Master Produk & SKU
            |--------------------------------------------------------------------------
            */

            Route::get('/products', function () {

                return view('admin.products.index');
            })->name('products.index');


            /*
            |--------------------------------------------------------------------------
            | Bill of Materials
            |--------------------------------------------------------------------------
            */

            Route::get('/products/{sku}/bom', function ($sku) {

                return view('admin.products.bom', [
                    'sku' => $sku,
                ]);
            })->name('products.bom');


            /*
            |--------------------------------------------------------------------------
            | Kategori Produk
            |--------------------------------------------------------------------------
            */

            Route::get('/categories', function () {
                return view('admin.categories.index');
            })->name('categories.index');

            Route::get('/materials', function () {
                return view('admin.materials.index');
            })->name('materials.index');
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
