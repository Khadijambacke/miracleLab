<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalculetteController;
use App\Http\Controllers\FormuleController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MessageController;

// Page d'accueil publique
Route::get('/', function () {
    return view('index');
})->name('home');

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes d'inscription et paiement
Route::get('/register', function () {
    return view('register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/payment', function () {
    return view('payment');
})->name('payment');
Route::post('/payment/process', [\App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payment.process');

// Routes protégées (nécessitent d'être connecté)
Route::middleware(['auth'])->group(function () {
    
    // Le tableau de bord unifié (redirige vers admin ou client selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Les vues directes pour admin/client si besoin, mais l'idéal est de passer par le dashboard unifié
    Route::get('/admin', function () {
        return view('dashboard-admin');
    })->name('dashboard.admin');

    // Routes pour les Formules et Ingrédients
    Route::post('/formules', [FormuleController::class, 'store'])->name('formules.store');
    Route::delete('/formules/{formule}', [FormuleController::class, 'destroy'])->name('formules.destroy');
    
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // Route::get('/calculette', [CalculetteController::class, 'index']);
});
