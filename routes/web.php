<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/admin/orders', function () {
    return view('admin.orders.index');
});

Route::get('/admin/orders/{id}', function ($id) {
    return view('admin.orders.show', [
        'orderId' => $id
    ]);
});
