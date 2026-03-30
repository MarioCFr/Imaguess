<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/profile', function () {
    return view('profile.edit');
})->middleware(['auth'])->name('profile');

// Rutas de autenticación de Breeze
require __DIR__.'/auth.php';
