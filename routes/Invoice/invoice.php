<?php
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;


Route::prefix('invoices')->group(function () {
    Route::post('/', [InvoiceController::class, 'createInvoice']);
    Route::get('/{id}', [InvoiceController::class, 'showInvoice']);
    Route::get('/', [InvoiceController::class, 'index']);
});