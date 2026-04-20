<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RoomBook — Routes API Lot 1 : Gestion des Utilisateurs & Authentification
|--------------------------------------------------------------------------
*/

// ─── Routes publiques (pas d'authentification requise) ───────────────────────
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

// ─── Routes protégées (token Sanctum requis) ─────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Authentification
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/me',      [AuthController::class, 'me'])->name('auth.me');

    // CRUD Utilisateurs — Réservé aux Administrateurs
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});

// ─── Route de santé ─────────────────────────────────────────────────────────
Route::get('/health', fn () => response()->json([
    'status'  => 'ok',
    'project' => 'RoomBook',
    'lot'     => 'Lot 1 — Gestion des Utilisateurs & Authentification',
    'version' => '1.0.0',
]));
