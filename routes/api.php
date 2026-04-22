<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
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

    // Lot 3 - Cœur de reservation
    Route::middleware('role:enseignant')->group(function () {
        Route::get('/bookings/my', [BookingController::class, 'myBookings'])->name('bookings.my');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    });

    Route::middleware('role:responsable')->group(function () {
        Route::get('/bookings/pending', [BookingController::class, 'pending'])->name('bookings.pending');
        Route::post('/bookings/{booking}/decision', [BookingController::class, 'decide'])->name('bookings.decide');
    });
});

// ─── Route de santé ─────────────────────────────────────────────────────────
Route::get('/health', fn () => response()->json([
    'status'  => 'ok',
    'project' => 'RoomBook',
    'lot'     => 'Lot 1 — Gestion des Utilisateurs & Authentification',
    'version' => '1.0.0',
]));
