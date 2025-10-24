<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
class PaymentController extends Controller
{
    public function createPayment(Request $request){
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,digital_wallet',
            'transaction_id' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'payment_date' => 'required|date',
            'metadata' => 'nullable|array',
        ]);
        $payment = Payment::create($validated);
        return response()->json([
            'message' => 'Payment created successfully',
            'payment' => $payment,
        ], 201);
    }
}
