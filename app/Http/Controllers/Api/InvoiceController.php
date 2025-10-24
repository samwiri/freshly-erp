<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
class InvoiceController extends Controller
{
    public function createInvoice(Request $request){
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
        ]);
        $invoice = Invoice::create($validated);
        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice,
        ], 201);
    }
}
