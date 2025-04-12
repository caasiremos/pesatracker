<?php

use App\Events\GameEvent;
use App\Http\Controllers\CustomerController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/customer', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/game', function(){
     $user = User::find(1)->first();
    broadcast(new GameEvent($user));
});

//Auth endpoints
Route::prefix('customers')->controller(CustomerController::class)->group(function(){
    Route::post('register', 'store');
    Route::post('login', 'login');
});

