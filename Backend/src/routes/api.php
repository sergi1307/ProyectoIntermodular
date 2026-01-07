<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Endpoint /register
// Registra un usuari 
Route::post('/register', [AuthController::class, 'createUser'])
    ->name('register');

// Endpoint /login
// Autentica un usuari
Route::post('/login', [AuthController::class, 'loginUser'])
    ->name('login');


