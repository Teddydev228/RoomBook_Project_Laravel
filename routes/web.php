<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| RoomBook - Routes Frontend (Lot 4)
|--------------------------------------------------------------------------
|
| Ces routes servent l'interface utilisateur de l'application RoomBook.
| Toutes les routes authentifiées nécessitent que l'utilisateur ait un token API valide
| provenant du backend (Lots 1/2/3).
|
*/

// Routes publiques
Route::middleware('web')->group(function () {
    // Connexion
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    // Page d'accueil
    Route::get('/', function () {
        return view('welcome');
    });
});

// Routes protégées - nécessitent un token API stocké en session
Route::middleware(['web', 'auth.api'])->group(function () {
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard principal - redirige selon le rôle
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // === ROUTES ENSEIGNANT (accessible par tous les utilisateurs connectés) ===
    Route::prefix('enseignant')->name('enseignant.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'teacherDashboard'])->name('dashboard');
    })->middleware('role:enseignant');
    
    // === ROUTES RESPONSABLE (responsable et admin) ===
    Route::prefix('responsable')->name('responsable.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'managerDashboard'])->name('dashboard');
    })->middleware('role:responsable');
    
    // === ROUTES ADMIN (admin uniquement) ===
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    })->middleware('role:admin');
    
    // === GESTION DES RÉSERVATIONS ===
    //Créer une réservation (accessible par enseignant, responsable, admin)
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{id}', [BookingController::class, 'show'])->whereNumber('id')->name('show');
    })->middleware('role:enseignant');
    
    // Annuler une réservation (enseignant uniquement - ses propres réservations acceptées)
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel')
        ->middleware('role:enseignant');
    
    // Décision sur réservations en attente (Responsable uniquement)
    Route::post('/bookings/{id}/decision', [BookingController::class, 'decision'])
        ->name('bookings.decision')
        ->middleware('role:responsable');
    
    // Réservations en attente (Responsable uniquement)
    Route::get('/bookings/pending', [BookingController::class, 'pending'])
        ->name('bookings.pending')
        ->middleware('role:responsable');
    
    // === GESTION DES SALLES (Admin uniquement) ===
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomController::class, 'destroy'])->name('destroy');
    })->middleware('role:admin');
    
    // === GESTION DU MATÉRIEL (Admin uniquement) ===
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/create', [EquipmentController::class, 'create'])->name('create');
        Route::post('/', [EquipmentController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [EquipmentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [EquipmentController::class, 'destroy'])->name('destroy');
    })->middleware('role:admin');
    
    // === GESTION DES UTILISATEURS (Admin uniquement) ===
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    })->middleware('role:admin');
    
    // Accès non autorisé
    Route::get('/unauthorized', [AuthController::class, 'unauthorized'])->name('unauthorized');
});