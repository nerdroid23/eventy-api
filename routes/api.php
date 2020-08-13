<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;

Route::post('register', RegisterController::class);
Route::post('login', [LoginController::class, 'login']);

Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
Route::post('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
});
