<?php

use App\Events\GameEvent;
use App\Http\Controllers\AirtelMoneyCallbackController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CashExpenseTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MtnMomoCallbackController;
use App\Http\Controllers\ScheduledTransactionController;
use App\Http\Controllers\WalletController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/customer', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Auth endpoints
Route::prefix('customers')->controller(CustomerController::class)->group(function () {
    Route::post('register', 'store');
    Route::post('login', 'login');
    Route::post('send-otp',  'sendOtp');
    Route::post('verify-otp',  'verifyOtp');
});

Route::prefix('customers')->group(function () {
    Route::put('{customer}/update', [CustomerController::class, 'update']);
    //Customer Categories
    Route::controller(CategoryController::class)->group(function () {
        Route::get('{customer}/categories/expenditures', 'getCategoriesWithSpentAmount');
        Route::get('{customer}/categories', 'index');
        Route::post('{customer}/categories', 'store');
        Route::put('{customer}/categories/{category}/update', 'update');
        Route::delete('{customer}/categories/{category}/delete', 'destroy');
    });

    //Customer Products
    Route::controller(ProductController::class)->group(function () {
        Route::get('products', 'getProducts');
        Route::get('products/price-list', 'getPriceList');
        Route::get('products/choice-list', 'getChoiceList');
    });

    //Customer Budgets
    Route::controller(BudgetController::class)->group(function () {
        Route::get('{customer}/budgets', 'index');
        Route::post('{customer}/budgets', 'store');
        Route::put('{customer}/budgets/{budget}/update', 'update');
        Route::delete('{customer}/budgets/{budget}/delete', 'destroy');
    });

    //Customer Feedback
    Route::controller(FeedbackController::class)->group(function () {
        Route::get('{customer}/feedbacks', 'index');
        Route::post('{customer}/feedbacks', 'store');
    });

    //Customer Cash Expense Transactions
    Route::controller(CashExpenseTransactionController::class)->group(function () {
        Route::get('{customer}/cash-expense-transactions', 'index');
        Route::post('{customer}/cash-expense-transactions', 'store');
    });

    //Customer Scheduled  Transactions
    Route::controller(ScheduledTransactionController::class)->group(function () {
        Route::get('{customer}/scheduled-transactions', 'index');
        Route::post('{customer}/scheduled-transactions', 'store');
        Route::get('{customer}/scheduled-transactions/upcoming/count', 'upcomingScheduledTransactionsCountByDate');
        Route::post('{customer}/scheduled-transactions/upcoming', 'upcomingScheduledTransactionsByDate');
    });

    //Customer Wallet
    Route::controller(WalletController::class)->group(function () {
        Route::get('{customer}/wallet', 'index');
        Route::post('{customer}/deposit', 'store');
    });

    Route::post('logout', [CustomerController::class, 'logout']);
})->middleware('auth:sanctum');

//Telecom callback
Route::controller(WalletController::class)->group(function () {
    Route::post('airtel/callback', 'airtelCallback');
    Route::post('mtn/callback', 'mtnCallback');
    Route::post('relworx/callback/collection', 'relworxCollectionCallback');
    Route::post('relworx/callback/disbursement', 'relworxDisbursementCallback');
    Route::post('relworx/callback/product', 'relworxProductCallback');
});
