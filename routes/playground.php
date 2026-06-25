<?php

use App\Http\Controllers\PlaygroundDashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/playground', '/playgorund');

Route::prefix('playgorund')->name('playground.')->group(function () {
    Route::get('/', [PlaygroundDashboardController::class, 'upload'])->name('upload');
    Route::post('/upload', [PlaygroundDashboardController::class, 'store'])->name('store');
    Route::get('/dashboard', [PlaygroundDashboardController::class, 'dashboard'])->name('dashboard');
});
