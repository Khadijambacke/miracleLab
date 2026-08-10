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
Route::get('/payment/status/{reference}', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('payment.status');
Route::post('/intech/callback', [\App\Http\Controllers\PaymentController::class, 'handleCallback'])->name('intech.callback')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Routes PayTech (callbacks publics - pas de CSRF ni auth requis)
Route::get('/payment/success', [\App\Http\Controllers\PaymentController::class, 'paytechSuccess'])->name('paytech.success');
Route::get('/payment/cancel', [\App\Http\Controllers\PaymentController::class, 'paytechCancel'])->name('paytech.cancel');
Route::post('/paytech/ipn', [\App\Http\Controllers\PaymentController::class, 'handlePaytechIPN'])->name('paytech.ipn')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Routes protégées (nécessitent d'être connecté)
Route::middleware(['auth'])->group(function () {
    
    // PayTech paiement (requiert un utilisateur connecté)
    Route::post('/paytech/process', [\App\Http\Controllers\PaymentController::class, 'processPaytechPayment'])->name('paytech.process');

    // Le tableau de bord unifié (redirige vers admin ou client selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Les vues directes pour admin/client si besoin, mais l'idéal est de passer par le dashboard unifié
    Route::get('/admin', function () {
        return view('dashboard-admin');
    })->name('dashboard.admin');

    // Routes pour les Formules et Ingrédients
    Route::post('/formules', [FormuleController::class, 'store'])->name('formules.store');
    Route::put('/formules/{formule}', [FormuleController::class, 'update'])->name('formules.update');
    Route::delete('/formules/{formule}', [FormuleController::class, 'destroy'])->name('formules.destroy');
    
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // Route::get('/calculette', [CalculetteController::class, 'index']);
});
