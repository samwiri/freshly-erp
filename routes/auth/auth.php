<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/check-email', [AuthController::class, 'checkIfEmailIsInUse']);
    Route::middleware('api.auth')->post('/logout', [AuthController::class, 'logout']);
});