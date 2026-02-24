<?php

use Illuminate\Support\Facades\Route;
//faltaba esa linea para que el enrutador utilice y sepa de las funciones que hay en usercontroller
use App\Http\Controllers\UserController;
use App\Http\Controllers\FavoriteController;
Route::get('/', function () {
    return view('welcome');
});
//ruta para mostrar el mapa
Route::get('/mapa', [UserController::class, 'mostrarMapa']);
Route::resource('favorites', FavoriteController::class);
