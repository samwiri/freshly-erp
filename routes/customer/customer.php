<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;

Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::post('/', [CustomerController::class, 'create']);
    Route::get('/{id}', [CustomerController::class, 'show']);
    Route::delete('/{id}', [CustomerController::class, 'delete']);
});