<?php

use App\Events\GameEvent;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
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
    Route::get('{customer}/categories', [CategoryController::class, 'index']);
    Route::post('{customer}/categories', [CategoryController::class, 'store']);
    Route::put('{customer}/categories/{category}/update', [CategoryController::class, 'update']);
    Route::delete('{customer}/categories/{category}/delete', [CategoryController::class, 'destroy']);
    Route::post('logout', [CustomerController::class, 'logout']);
})->middleware('auth:sanctum');
