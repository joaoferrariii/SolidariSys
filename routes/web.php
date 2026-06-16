<?php

use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DoadorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\PasswordController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
     Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('/settings/profile', [ProfileController::class, 'destroy'])->name('settings.profile.destroy');

    Route::put('/settings/password', [PasswordController::class, 'update'])->name('settings.password.update');
});

// Doador
Route::get('/cadastro-doador',             [DoadorController::class, 'index']  )->middleware(['auth', 'verified'])->name('cadastro-doador');
Route::post('/cadastro-doador',            [DoadorController::class, 'store']  )->middleware(['auth', 'verified'])->name('cadastro-doador.store');
Route::put('/cadastro-doador/{doador}',    [DoadorController::class, 'update'] )->middleware(['auth', 'verified'])->name('cadastro-doador.update');
Route::delete('/cadastro-doador/{doador}', [DoadorController::class, 'destroy'])->middleware(['auth', 'verified'])->name('cadastro-doador.destroy');

// Doação
Route::get('/doacao',             [DoacaoController::class, 'index']  )->middleware(['auth', 'verified'])->name('doacao');
Route::post('/doacao',            [DoacaoController::class, 'store']  )->middleware(['auth', 'verified'])->name('doacao.store');
Route::put('/doacao/{doacao}',    [DoacaoController::class, 'update'] )->middleware(['auth', 'verified'])->name('doacao.update');
Route::delete('/doacao/{doacao}', [DoacaoController::class, 'destroy'])->middleware(['auth', 'verified'])->name('doacao.destroy');

// Usuario
Route::get('/cadastro-usuario',           [UsuarioController::class, 'index']  )->middleware(['auth', 'verified'])->name('cadastro-usuario');
Route::post('/cadastro-usuario',          [UsuarioController::class, 'store']  )->middleware(['auth', 'verified'])->name('cadastro-usuario.store');
Route::put('/cadastro-usuario/{user}',    [UsuarioController::class, 'update'] )->middleware(['auth', 'verified'])->name('cadastro-usuario.update');
Route::delete('/cadastro-usuario/{user}', [UsuarioController::class, 'destroy'])->middleware(['auth', 'verified'])->name('cadastro-usuario.destroy');

require __DIR__.'/auth.php';
