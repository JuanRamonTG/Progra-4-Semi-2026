<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\RegistroMaterialController;
use App\Http\Controllers\RegistroFallecidoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sistema', function () {
    return view('academica');
});

Route::get('/bienvenida/{nombre}', function ($nombre) {
    return '<h1>Bienvenido a mi pagina, hola '.$nombre.', como estas...</h1>';
});

// Rutas de sincronización que imitan la estructura anterior
Route::prefix('private/modulos')->group(function () {
    Route::any('alumnos/alumno.php', [AlumnoController::class, 'index']);
    Route::any('materias/materia.php', [MateriaController::class, 'index']);
    Route::any('docentes/docente.php', [DocenteController::class, 'index']);
    Route::any('matriculas/matricula.php', [MatriculaController::class, 'index']);
    Route::any('inscripciones/inscripcion.php', [InscripcionController::class, 'index']);
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
