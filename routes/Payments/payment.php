<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

Route::prefix('payments')->group(function () {
    Route::post('/', [PaymentController::class, 'createPayment']);
});