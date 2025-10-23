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
            'service_type' => 'required|in:wash,dry_clean,express,ironing,alterations',
            'status' => 'required|in:received,washing,drying,ironing,ready,delivered,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'special_instructions' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'pickup_time' => 'nullable|date',
            'delivery_time' => 'nullable|date',
            'items' => 'required|array',
            'items.*.item_type' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
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
                'service_type' => $validated['service_type'],
                'status' => $validated['status'],
                'priority' => $validated['priority'],
                'special_instructions' => $validated['special_instructions'],
                'payment_status' => 'pending',
                'delivery_address' => $validated['delivery_address'],
                'pickup_time' => $validated['pickup_time'],
                'delivery_time' => $validated['delivery_time'],
            ]);

            Log::info('Order created in DB', [
                'order_id' => $order->id
            ]);

            foreach ($validated['items'] as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['item_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
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
            $order->updateStatus($validated['status'], Auth::user()->id, 'Order created');
            Log::info('Order status history updated', [
                'order_id' => $order->id,
                'status' => $validated['status'],
                'changed_by' => Auth::user()->id,
            ]);
            
            DB::commit();
            
            Log::info('Order creation transaction committed', [
                'order_id' => $order->id
            ]);
            
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order->load(['items', 'customer', 'employee'])
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
        $orders = Order::with(['items', 'customer', 'employee'])->get();
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