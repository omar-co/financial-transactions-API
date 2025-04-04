<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

Route::middleware('auth:api')->group(function () {
    Route::apiResource('accounts', AccountController::class)->except('update', 'destroy');
});
