<?php

use App\Events\GameEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/game', function(){
     $user = User::find(1)->first();
    broadcast(new GameEvent($user));
});
