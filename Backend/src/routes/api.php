<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

// Endpoints d'Autenticació
Route::prefix('auth')->name('auth.')->group(function (){
    Route::post('/register', [AuthController::class, 'createUser'])->name('register');
    Route::post('/login', [AuthController::class, 'loginUser'])->name('login');
});

// Endpoints d'Usuaris
Route::prefix('users')->name('users.')->group(function (){
    Route::get('/',[UserController::class, 'index'])->name('index');
    Route::get('/map', [UserController::class, 'mostrarMapa'])->name('map');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/show/{id}', [UserController::class, 'show'])->name('show');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
});

// Endpoints de Productes
Route::prefix('products')->name('products.')->group(function (){
    Route::get('/',[ProductController::class, 'index'])->name('index');
    Route::post('/store', [ProductController::class, 'store'])->name('store');
    Route::get('/show/{id}', [ProductController::class, 'show'])->name('show');
    Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [ProductController::class, 'destroy'])->name('destroy');
});