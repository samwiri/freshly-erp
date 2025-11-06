<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::prefix('users')->group(function () {
    Route::get('/', [UsersController::class, 'index']);
    

});