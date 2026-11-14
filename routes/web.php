<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BomController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ProcurementController;
use App\Http\Controllers\Admin\MaterialShortageController;
use App\Http\Controllers\Produksi\ProductionQueueController;
use App\Http\Controllers\Produksi\StockPlanningController;
use App\Http\Controllers\Produksi\ProductionDashboardController;
use App\Http\Controllers\Produksi\ProductionHistoryController;
use App\Http\Controllers\Admin\DashboardController;


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
            redirect()->route('produksi.queue.index'),

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
            | Dashboard
            |--------------------------------------------------------------------------
            */

            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Order Management
            |--------------------------------------------------------------------------
            */

            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');


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


            Route::get('/inventory', [InventoryController::class, 'index'])
                ->name('inventory.index');

            // Riwayat produk jadi: sementara tetap menggunakan closure lama

            Route::get('/inventory/{product:sku}/history', [
                InventoryController::class,
                'productHistory',
            ])->name('inventory.history');


            // Riwayat bahan baku: menggunakan InventoryController
            Route::get('/inventory/material/{material}/history', [
                InventoryController::class,
                'materialHistory',
            ])->name('inventory.material-history');

            /*
|--------------------------------------------------------------------------
| Procurement
|--------------------------------------------------------------------------
*/

            Route::get('/procurement/suppliers', [SupplierController::class, 'index'])->name('procurement.suppliers');
            Route::post('/procurement/suppliers', [SupplierController::class, 'store'])->name('procurement.suppliers.store');
            Route::put('/procurement/suppliers/{supplier}', [SupplierController::class, 'update'])->name('procurement.suppliers.update');
            Route::delete('/procurement/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('procurement.suppliers.destroy');

            // Material Shortage (harus di atas /procurement/{purchaseOrder:po_no})
            Route::get('/procurement/shortage', [MaterialShortageController::class, 'page'])->name('procurement.shortage');
            Route::get('/procurement/shortage/data', [MaterialShortageController::class, 'data'])->name('procurement.shortage.data');
            Route::get('/procurement/shortage/{materialShortage}/data', [MaterialShortageController::class, 'show'])->name('procurement.shortage.show');
            Route::get('/procurement/shortage/filters/materials', [MaterialShortageController::class, 'materials'])->name('procurement.shortage.materials');
            Route::get('/procurement/shortage/filters/suppliers', [MaterialShortageController::class, 'suppliers'])->name('procurement.shortage.suppliers');
            Route::get('/procurement/shortage/filters/supplier-materials', [MaterialShortageController::class, 'supplierMaterials'])
                ->name('procurement.shortage.supplier-materials');
            Route::post('/procurement/from-shortages', [ProcurementController::class, 'storeFromShortages'])->name('procurement.store-from-shortage');

            Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
            Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');


            // Wildcard PALING BAWAH, karena /procurement/{apapun} nangkep semua
            Route::get('/procurement/{purchaseOrder:po_no}', [ProcurementController::class, 'show'])->name('procurement.show');
            Route::post('/procurement/{purchaseOrder:po_no}/receive', [ProcurementController::class, 'receive'])->name('procurement.receive');

            /*
            |--------------------------------------------------------------------------
            | Activity Logs
            |--------------------------------------------------------------------------
            */

            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
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
                return view('produksi.dashboard.index');
            })->name('dashboard');

            Route::get('/queue', function () {
                return view('produksi.queue.index');
            })->name('queue.index');

            Route::get('/queue/data', [ProductionQueueController::class, 'index'])
                ->name('queue.data');

            Route::get('/planning', function () {
                return view('produksi.planning.index');
            })->name('planning.index');

            Route::get('/planning/products', [StockPlanningController::class, 'products'])
                ->name('planning.products');

            Route::get('/planning/requests', [StockPlanningController::class, 'requests'])
                ->name('planning.requests');

            Route::post('/planning', [StockPlanningController::class, 'store'])
                ->name('planning.store');

            Route::get('/history', function () {
                return view('produksi.history.index');
            })->name('history.index');

            Route::get('/queue/{productionRequest:request_no}/materials', [ProductionQueueController::class, 'materials'])
                ->name('queue.materials');

            Route::post('/queue/{productionRequest:request_no}/execute', [ProductionQueueController::class, 'execute'])
                ->name('queue.execute');

            Route::post('/queue/{productionRequest:request_no}/shortage', [ProductionQueueController::class, 'shortage'])
                ->name('queue.shortage');

            Route::post('/queue/{productionRequest:request_no}/start', [ProductionQueueController::class, 'start'])
                ->name('queue.start');
            
            Route::get('/history', [ProductionHistoryController::class, 'page'])->name('history.index');
            Route::get('/history/data', [ProductionHistoryController::class, 'data'])->name('history.data');
            Route::get('/history/{log}/materials', [ProductionHistoryController::class, 'materials'])->name('history.materials');
            Route::get('/dashboard/data', [ProductionDashboardController::class, 'data'])
                ->name('dashboard.data');
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
                return view('owner.dashboard.index');
            })->name('dashboard');

            Route::get('/sales-report', function () {
                return view('owner.sales-report.index');
            })->name('sales-report');

            Route::get('/activity-log', function () {
                return view('owner.activity-log.index');
            })->name('activity-log');

            Route::get('/user-management', function () {
                return view('owner.user-management.index');
            })->name('user-management');

            Route::get('/master-data', function () {
                return view('owner.master-data.index');
            })->name('master-data');
        });
});
