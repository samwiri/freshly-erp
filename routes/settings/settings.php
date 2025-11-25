<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\serviceItemController;
Route::prefix("settings")->group(function () {
    Route::get('/service-item', [serviceItemController::class, 'index']);
    Route::post('/service-item', [serviceItemController::class, 'create']);
    Route::get('/service-item/{id}', [serviceItemController::class, 'show']);
    Route::put('/service-item/{id}', [serviceItemController::class, 'update']);
    Route::delete('/service-item/{id}', [serviceItemController::class, 'destroy']);
});