<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Delivery_pointController;
use App\Http\Controllers\ReviewController;

// Endpoints d'Autenticació
Route::prefix('auth')->name('auth.')->group(function (){
    Route::post('/register', [AuthController::class, 'createUser'])->name('register');
    Route::post('/login', [AuthController::class, 'loginUser'])->name('login');
});

// Endpoints d'Usuaris
Route::prefix('users')->name('users.')->group(function (){
    Route::get('/',[UserController::class, 'index'])->name('index');
    Route::get('/map', [UserController::class, 'mostrarMapa'])->name('map');
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

// Endpoints de Categories
Route::prefix('categories')->name('categories.')->group(function (){
    Route::get('/',[CategoryController::class, 'index'])->name('index');
    Route::post('/store', [CategoryController::class, 'store'])->name('store');
    Route::get('/show/{id}', [CategoryController::class, 'show'])->name('show');
    Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});
// Endpoints de Ventas 
Route::prefix('sales')->name('sales.')->group(function (){
    Route::get('/', [SaleController::class, 'index'])->name('index');
    Route::post('/store', [SaleController::class, 'store'])->name('store'); 
    Route::get('/show/{id}', [SaleController::class, 'show'])->name('show');
    Route::put('/update/{id}', [SaleController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [SaleController::class, 'destroy'])->name('destroy');
});

// Endpoints de Puntos de Entrega
Route::prefix('delivery_points')->name('delivery_points.')->group(function (){
    Route::get('/', [Delivery_pointController::class, 'index'])->name('index');
    Route::post('/store', [Delivery_pointController::class, 'store'])->name('store');
    Route::get('/show/{id}', [Delivery_pointController::class, 'show'])->name('show');
    Route::put('/update/{id}', [Delivery_pointController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [Delivery_pointController::class, 'destroy'])->name('destroy');
});

// Endpoints de Reseñas
Route::prefix('reviews')->name('reviews.')->group(function (){
    Route::get('/', [ReviewController::class, 'index'])->name('index');    
    Route::post('/store', [ReviewController::class, 'store'])->name('store');
    Route::get('/show/{id}', [ReviewController::class, 'show'])->name('show');
    Route::put('/update/{id}', [ReviewController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [ReviewController::class, 'destroy'])->name('destroy');
});