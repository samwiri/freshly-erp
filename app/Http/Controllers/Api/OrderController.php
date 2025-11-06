<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class OrderController extends Controller
{

    public function createOrder(Request $request){
        // Log the request input for audit/debug
        Log::info('Creating order. Incoming request', [
            'user_id' => Auth::user() ? Auth::user()->id : null,
            'payload' => $request->all()
        ]);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'nullable|exists:employees,id',
            // 'service_type' => 'required|in:wash,dry_clean,express,ironing,alterations',
            // 'status' => 'required|in:received,washing,drying,ironing,ready,delivered,cancelled',
            // 'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'pickupDate' => 'nullable|date',
            'deliveryDate' => 'nullable|date',
            'items' => 'required|array',
            // 'items.*.item_type' => '|string',
            'items.*.service_type' => 'required|in:wash,Dry Cleaning,express,ironing,alterations',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'

        ]);

        DB::beginTransaction();
        try {
            // Only check employee if employee_id is provided
            if ($validated['employee_id'] && !Employee::find($validated['employee_id'])) {
                Log::warning('Employee not found when creating order', [
                    'employee_id' => $validated['employee_id']
                ]);
                return response()->json(['error' => 'Employee not found'], 404);
            }
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'employee_id' => $validated['employee_id'],
                // 'service_type' => $validated['service_type'],
                'status' => "received",
                // 'priority' => $validated['priority'],
                // 'discount' => $validated['discount'],
                'special_instructions' => $validated['notes'],
                'payment_status' => 'pending',
                // 'delivery_address' => $validated['delivery_address'],
                'pickup_time' => $validated['pickupDate'],
                'delivery_time' => $validated['deliveryDate'],
            ]);

            Log::info('Order created in DB', [
                'order_id' => $order->id
            ]);

            foreach ($validated['items'] as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    // 'item_type' => $item['item_type'],
                    'service_type' => $item['service_type'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
                Log::info('Order item created', [
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                    'item_type' => $orderItem->item_type
                ]);
            }
            
            $order->load('items'); // Load the items relationship before calculating total
            $order->calculateTotal();
            Log::info('Order total calculated', [
                'order_id' => $order->id,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'total' => $order->total
            ]);
            
            // Now Auth::user() will work properly with middleware
            $order->updateStatus("received", Auth::user()->id, 'Order created');

            //create the invoice for the order
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'discount' => "0.12",
                'total' => $order->total,
                'status' => 'draft',
            ]);
            // Log::info('Order status history updated', [
            //     'order_id' => $order->id,
            //     'status' => $validated['status'],
            //     'changed_by' => Auth::user()->id,
            // ]);
            
            DB::commit();
            
            Log::info('Order creation transaction committed', [
                'order_id' => $order->id
            ]);
            
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['items', 'customer', 'employee', 'invoice'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getOrders(){
        $orders = Order::with(['items', 'customer','customer.user', 'employee'])->get();
        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders' => $orders,
        ], 200);
    }

    public function getOrder($id){
        $order = Order::with(['items', 'customer', 'employee'])->find($id);
        
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }
        
        return response()->json([
            'message' => 'Order fetched successfully',
            'order' => $order,
        ], 200);
    }

    public function updateOrder(Request $request, $id){
        $order = Order::find($id);
        
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }
        
        $order->update($request->all());
        return response()->json([
            'message' => 'Order updated successfully',
            'order' => $order,
        ], 200);
    }
}