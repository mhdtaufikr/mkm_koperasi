<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoopDashboardUpdateController;
use App\Http\Controllers\CoopDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php (atau api.php jika mau pure JSON)
Route::get('/api/coop-dashboard', [CoopDashboardController::class, 'index']);
// routes/web.php
Route::get('/koperasi/dashboard', [CoopDashboardController::class, 'index'])->name('coop.dashboard');
Route::get('/koperasi/dashboard/view', [CoopDashboardController::class, 'indexView'])->name('coop.dashboard.view');
Route::put('/koperasi/dashboard/update-members',     [CoopDashboardUpdateController::class, 'updateMembers'])->name('coop.dashboard.update.members');
Route::put('/koperasi/dashboard/update-projections', [CoopDashboardUpdateController::class, 'updateProjections'])->name('coop.dashboard.update.projections');
Route::put('/koperasi/dashboard/update-balance',     [CoopDashboardUpdateController::class, 'updateBalance'])->name('coop.dashboard.update.balance');
Route::put('/koperasi/dashboard/update-monthlies',   [CoopDashboardUpdateController::class, 'updateMonthlies'])->name('coop.dashboard.update.monthlies');
Route::put('/koperasi/dashboard/update-participation',
    [CoopDashboardUpdateController::class, 'updateParticipation']
)->name('coop.dashboard.update.participation');

