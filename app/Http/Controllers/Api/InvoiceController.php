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
    public function showInvoice($id){
        $invoice = Invoice::with(['order', 'customer', 'customer.user'])->find($id);
        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found',
            ], 404);
        }
        return response()->json([
            'message' => 'Invoice fetched successfully',
            'invoice' => $invoice,
        ], 200);
    }
    public function index (Request $request){
        $userId = $request->user()?->id;
        $invoices = Invoice::with(['order', 'customer', 'customer.user'])
        ->select('invoices.*')
        ->join('orders', 'invoices.order_id', '=', 'orders.id')
        ->join('customers', 'orders.customer_id', '=', 'customers.id')
        ->when($userId, function ($query) use ($userId) {
            $query->where('customers.user_id', $userId);
        })
        ->orderBy('invoices.id', 'desc')
        ->get();
        return response()->json([
            'message' => 'Invoices fetched successfully',
            'invoices' => $invoices,
        ], 200);
    }
}
