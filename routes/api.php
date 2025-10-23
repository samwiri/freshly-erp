<?php

use Illuminate\Support\Facades\Route;

include 'auth/auth.php';
include 'users/users.php';

Route::middleware('api.auth')->group(function () {
    include 'Orders/orders.php';
});