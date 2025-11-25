<?php

use Illuminate\Support\Facades\Route;

include 'auth/auth.php';
include 'users/users.php';

Route::middleware('api.auth')->group(function () {
    include 'Orders/orders.php';
    include 'Payments/payment.php';
    include 'customer/customer.php';
    include 'Invoice/invoice.php';
    include 'settings/settings.php';
});