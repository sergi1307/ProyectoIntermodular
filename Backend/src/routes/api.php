<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Delivery_pointController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;

//----------------
// RUTAS PÚBLICAS
//----------------


// 1. Autenticación
Route::prefix('auth')->name('auth.')->group(function (){
    Route::post('/register', [AuthController::class, 'createUser'])->name('register');
    Route::post('/login', [AuthController::class, 'loginUser'])->name('login');
});

// 2. Catálogo (Productos y Categorías)
Route::prefix('products')->name('products.')->group(function (){
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/show/{id}', [ProductController::class, 'show'])->name('show');
});

Route::prefix('categories')->name('categories.')->group(function (){
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/show/{id}', [CategoryController::class, 'show'])->name('show');
});

// 3. Información General (Puntos de entrega y Reviews)
Route::prefix('delivery_points')->name('delivery_points.')->group(function (){
    Route::get('/', [Delivery_pointController::class, 'index'])->name('index');
    Route::get('/myPoints', [Delivery_pointController::class, 'myPoints'])->name('myPoints');
});

Route::prefix('reviews')->name('reviews.')->group(function (){
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::get('/show/{id}', [ReviewController::class, 'show'])->name('show');
    Route::get('/producto/{productId}', [ReviewController::class, 'obtenerPorProducto'])->name('producto');
});

// --------------------
// RUTAS PROTEGIDAS
// --------------------

Route::middleware('auth:sanctum')->group(function() {

    // 1. Logout
    Route::post('/auth/logout', [AuthController::class, 'logoutUser'])->name('auth.logout');

    // 2. Usuario y Perfil
    Route::prefix('users')->name('users.')->group(function (){
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/me', [UserController::class, 'show'])->name('me');
        Route::put('/update', [UserController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('destroy');
        
        // Perfil específico
        Route::get('/my-profile', [UserController::class, 'myProfile'])->name('myProfile');
        Route::put('/update-my-profile', [UserController::class, 'updateMyProfile'])->name('updateMyProfile');
    });

    // Utilidad de Mapa
    Route::get('/map', [UserController::class, 'mostrarMapa'])->name('map');

    // 3. Compras y Carrito
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/add', [CartController::class, 'addItem']);
        Route::put('/update/{id}', [CartController::class, 'updateItem']);
        Route::delete('/remove/{id}', [CartController::class, 'removeItem']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::post('/checkout', [CartController::class, 'checkout']);
    });

    Route::prefix('sales')->name('sales.')->group(function (){
        Route::post('/store', [SaleController::class, 'store'])->name('store'); 
        Route::put('/update/{id}', [SaleController::class, 'update'])->name('update');
        Route::get('/my-orders', [SaleController::class, 'myOrders'])->name('my-orders');
        Route::get('/my-purchases', [SaleController::class, 'myPurchase'])->name('my-purchases');
        Route::get('/show/{id}', [SaleController::class, 'show'])->name('show'); 
    });

    // 4. Gestión de Contenido (Crear/Editar)
    
    // Productos
    Route::prefix('products')->name('products.')->group(function (){
        Route::get('/mine', [ProductController::class, 'myProducts'])->name('mine');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::put('/update/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Categorías
    Route::prefix('categories')->name('categories.')->group(function (){
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Puntos de Entrega
    Route::prefix('delivery_points')->name('delivery_points.')->group(function (){
        Route::post('/store', [Delivery_pointController::class, 'store'])->name('store');
        Route::put('/update/{id}', [Delivery_pointController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [Delivery_pointController::class, 'destroy'])->name('destroy');
    });

    // Reviews
    Route::post('/reviews/store', [ReviewController::class, 'store'])->name('reviews.store');
});