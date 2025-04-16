<?php

use App\Events\GameEvent;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CashExpenseTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\MerchantController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/customer', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/game', function () {
    $user = User::find(1)->first();
    broadcast(new GameEvent($user));
});

//Auth endpoints
Route::prefix('customers')->controller(CustomerController::class)->group(function () {
    Route::post('register', 'store');
    Route::post('login', 'login');
    Route::post('send-otp',  'sendOtp');
    Route::post('verify-otp',  'verifyOtp');
});

Route::prefix('customers')->group(function () {
    //Customer Categories
    Route::controller(CategoryController::class)->group(function () {
        Route::get('{customer}/categories', 'index');
        Route::post('{customer}/categories', 'store');
        Route::put('{customer}/categories/{category}/update', 'update');
        Route::delete('{customer}/categories/{category}/delete', 'destroy');
    });
    
    //Customer Merchants
    Route::controller(MerchantController::class)->group(function () {
        Route::get('{customer}/merchants', 'index');
        Route::post('{customer}/merchants', 'store');
        Route::put('{customer}/merchants/{merchant}/update', 'update');
        Route::delete('{customer}/merchants/{merchant}/delete', 'destroy');
    });

    //Customer Budgets
    Route::controller(BudgetController::class)->group(function () {
        Route::get('{customer}/budgets', 'index');
        Route::post('{customer}/budgets', 'store');
        Route::put('{customer}/budgets/{budget}/update', 'update');
        Route::delete('{customer}/budgets/{budget}/delete', 'destroy');
    });

     //Customer Budgets
    Route::controller(FeedbackController::class)->group(function () {
        Route::get('{customer}/feedbacks', 'index');
        Route::post('{customer}/feedbacks', 'store');
    });

    //Customer Cash Expense Transactions
    Route::controller(CashExpenseTransactionController::class)->group(function () {
        Route::get('{customer}/cash-expense-transactions', 'index');
        Route::post('{customer}/cash-expense-transactions', 'store');
    });

    Route::post('logout', [CustomerController::class, 'logout']);
})->middleware('auth:sanctum');
