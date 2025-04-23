<?php

use App\Events\GameEvent;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WalletController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/wallets', [WalletController::class, 'customerWallets'])->name('wallets.index');
});


Route::get('broadcast', function () {
    $user = User::find(1)->first();
    broadcast(new GameEvent($user));
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
