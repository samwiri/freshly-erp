<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'createOrder']);
    Route::get('/', [OrderController::class, 'getOrders']);
});