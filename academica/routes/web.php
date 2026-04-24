<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroMaterialController;
use App\Http\Controllers\RegistroFallecidoController;

Route::get('/opciones', function () {
    return view('welcome');
});

Route::get('/bienvenida/{nombre}', function ($nombre) {
    return '<h1>Bienvenido a mi pagina, hola '.$nombre.', como estas...</h1>';
});

// Rutas de sincronización que imitan la estructura anterior
Route::prefix('private/modulos')->group(function () {
    Route::any('registromaterial/registromaterial.php', [RegistroMaterialController::class, 'index']);
    Route::any('registrofallecido/registrofallecido.php', [RegistroFallecidoController::class, 'index']);
});

// Rutas REST para los nuevos módulos
Route::get('/registromaterial', function () {
    return view('registromaterial');
});

Route::get('/registrofallecido', function () {
    return view('registrofallecido');
});

Route::get('/modulos', function () {
    return view('modulos');
});
