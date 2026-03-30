<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

// Página de inicio → redirige a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard (requiere login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Juego (requiere login o invitado)
Route::get('/game', function () {
    return view('game');
})->middleware(['auth'])->name('game');

// Invitado
Route::get('/game/guest', function () {
    return view('game');
})->name('game.guest');

// Ranking (público)
Route::get('/ranking', function () {
    $topScores = \App\Models\Score::with('user')
        ->orderByDesc('points')
        ->get()
        ->unique('user_id')
        ->values();

    return view('ranking', compact('topScores'));
})->name('ranking');

// Perfil
use App\Http\Controllers\ProfileController;

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas del juego (API)
Route::middleware(['auth'])->group(function () {
    Route::get('/game/next-image', [GameController::class, 'nextImage']);
    Route::post('/game/save-score', [GameController::class, 'saveScore']);
});

// Rutas de autenticación de Breeze
require __DIR__.'/auth.php';
