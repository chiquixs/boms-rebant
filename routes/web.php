<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/preview-login', function () {
    return view('auth.login');
})->withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
]);
