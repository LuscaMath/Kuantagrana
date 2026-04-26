<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\FinancialTransactionController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('mapa', [EnvironmentController::class, 'index'])->name('environments.index');
    Route::get('ambientes/{slug}', [EnvironmentController::class, 'show'])->name('environments.show');
    Route::get('tutorial', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('tutorial/dismiss', [OnboardingController::class, 'dismiss'])->name('onboarding.dismiss');

    Route::resource('financial-transactions', FinancialTransactionController::class)
        ->except('show');
    Route::post('goals/{goal}/contributions', [GoalController::class, 'storeContribution'])
        ->name('goals.contributions.store');
    Route::resource('goals', GoalController::class)
        ->except('show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
