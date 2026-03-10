<?php

use App\Events\GameEvent;
use App\Http\Controllers\CashExpenseTransactionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ScheduledTransactionController;
use App\Http\Controllers\WalletController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::get('/privacy', function () {
    return Inertia::render('PrivacyPolicy');
})->name('privacy');

Route::get('/delete-customer-account', function () {
    return Inertia::render('DeleteCustomerAccount');
})->name('delete-customer-account');

Route::post('/delete-customer-account', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => ['required', 'email']]);
    return back()->with('status', 'Account deletion request received. We will process it shortly.');
})->name('delete-customer-account.submit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/wallets', [WalletController::class, 'customerWallets'])->name('wallets.index');
    Route::get('/feedbacks', [FeedbackController::class, 'feedbacks'])->name('feedbacks.index');
    Route::get('/wallet-transactions', [WalletController::class, 'walletWalletTransactions'])->name('wallets.transactions');
    Route::get('/scheduled-transactions', [ScheduledTransactionController::class, 'scheduledTransactions'])->name('scheduled.transactions');
    Route::get('/cash-transactions', [CashExpenseTransactionController::class, 'cashExpenseTransactions'])->name('cash.transactions');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
