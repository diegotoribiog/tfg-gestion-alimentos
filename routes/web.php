<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlimentoController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\RecetaController;

// Redirigimos la raíz al inicio
Route::get('/', function () {
    return redirect()->route('inicio');
});

// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Inicio (Dashboard)
    Route::get('/inicio', [DashboardController::class, 'index'])->name('inicio');

    // Ayuda y Guía
    Route::get('/ayuda', function () {
        return Inertia::render('Ayuda/ComoFunciona');
    })->name('ayuda.como-funciona');

    // Recetas con IA y Gestión de Historial
    // Gestión de Recetas IA
    Route::controller(RecetaController::class)->group(function () {
        Route::get('/recetas', 'index')->name('recetas.index');
        Route::post('/recetas/generar', 'generar')->name('recetas.ia');
        Route::post('/recetas/cocinar', 'cocinarReceta')->name('recetas.cocinar');
        Route::post('/recetas/{receta}/favorito', 'toggleFavorito')->name('recetas.favorito');
        Route::delete('/recetas/historial', 'destroyHistory')->name('recetas.destroyHistory');
        Route::delete('/recetas/{receta}', 'destroy')->name('recetas.destroy');
    });
    // Gestión de Perfil
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/perfil', 'edit')->name('perfil.edit');
        Route::patch('/perfil', 'update')->name('perfil.update');
        Route::delete('/perfil', 'destroy')->name('perfil.destroy');
    });

    // Gestión de Alimentos (Inventario)
    Route::controller(AlimentoController::class)->group(function () {
        Route::get('/inventario', 'index')->name('alimentos.index');
        Route::post('/alimentos', 'store')->name('alimentos.store');
        Route::put('/alimentos/{alimento}', 'update')->name('alimentos.update');
        Route::delete('/alimentos/caducados', 'destroyExpired')->name('alimentos.destroyExpired');
        Route::delete('/alimentos/{alimento}', 'destroy')->name('alimentos.destroy');
        Route::post('/alimentos/{alimento}/ajustar-stock', 'ajustarStock')->name('alimentos.ajustarStock');
    });
});

require __DIR__ . '/auth.php';
