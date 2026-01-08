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

Route::get('/index', [UserController::class, 'index'])->name('users.index');

Route::get('/show/{id}', [UserController::class, 'show'])->name('users.show');

Route::put('/update/{id}', [UserController::class, 'update'])->name('users.update');

Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('/mapa', [UserController::class, 'mostrarMapa'])
    ->name('mapa');