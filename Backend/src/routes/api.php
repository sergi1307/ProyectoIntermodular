<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


// Endpoint /register
// Registra un usuari 
Route::post('/register', [AuthController::class, 'createUser'])
    ->name('register');

// Endpoint /login
// Autentica un usuari
Route::post('/login', [AuthController::class, 'loginUser'])
    ->name('login');

// Endpoint /users/index
// Obté tots els usuaris 
Route::get('/users/index', [UserController::class, 'index'])->name('users.index');

// Endpoint /users/show/{id}
// Obté un usuari 
Route::get('/users/show/{id}', [UserController::class, 'show'])->name('users.show');

// Endpoint /users/update/{id}
// Actualitza les dades d'un usuari
Route::put('/users/update/{id}', [UserController::class, 'update'])->name('users.update');

// Endpoint /users/destroy/{id}
// Elimina un usuari
Route::delete('/users/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Endpoint /mapa
// Mostra el mapa dels punts de venta
Route::get('/mapa', [UserController::class, 'mostrarMapa'])
    ->name('mapa');