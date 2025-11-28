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
use App\Models\Customer;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderReceived;
use App\Models\ServiceItem;


class OrderController extends Controller
{

    public function createOrder(Request $request)
    {
        $userId = $request->user()?->id;
        // Log the request input for audit/debug
        Log::info('Creating order. Incoming request', [
            'user_id' => $userId,
            'payload' => $request->all()
        ]);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'pickupDate' => 'nullable|date',
            'deliveryDate' => 'nullable|date',

            'items' => 'required|array',
            'items.*.service_item_id' => 'required|exists:service_items,id',
            'items.*.quantity' => 'required|integer|min:1'
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
                'employee_id' => $validated['employee_id'] ?? null,
                'user_id' => $request->user()?->id,
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

                $service = ServiceItem::find($item['service_item_id']);
                $unitPrice = $service->base_price;

                $totalPrice = $unitPrice * $item['quantity'];

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'service_item_id' => $service->id,
                    'item_type' => $service->name,       // optional
                    'description' => $service->description,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                Log::info('Order item created', [
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                    'service_item_id' => $service->id
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
            $customer = Customer::find($order->customer_id);
            $customer->updateTotalOrders($order->customer_id);
            $customer->updateTotalSpend($order->customer_id);
            try {
                Mail::to($order->customer->email)->send(new OrderReceived($order));
                Log::info('Order confirmation email sent', [
                    'order_id' => $order->id,
                    'customer_email' => $order->customer->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the order creation if email fails
            }
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

    public function getOrders(Request $request)
    {
        $userId = $request->user()?->id;
        Log::info('Fetching orders for user', ['user_id' => $userId]);

        $orders = Order::with(['items', 'customer', 'customer.user', 'employee'])
            ->select('orders.*')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->when($userId, function ($query) use ($userId) {
                $query->where('customers.user_id', $userId);
            })
            ->orderBy('orders.id', 'desc') // Add ordering to get newest first
            ->get();

        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders' => $orders,
        ], 200);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }
        $order->delete();
        $customer = Customer::find($order->customer_id);
        $customer->updateTotalOrders($order->customer_id);
        $customer->updateTotalSpend($order->customer_id);
        return response()->json([
            'message' => 'Order deleted successfully',
        ], 200);
    }

    public function getOrder(Request $request, $id)
    {

        $userId = $request->user()?->id;
        $order = Order::where('id', $id)->where('user_id', $userId) || Order::where('id', $id)->where('employee_id', $userId)->first();
        if (!$order) {
            return response()->json([
                'message' => 'Order not found or you are not authorized to access this order',
            ], 403);
        }
        Log::info('Fetching order ' . $order);

        $order = Order::with(['items', 'customer', 'employee'])->find($id);
        if (!$order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }
        return response()->json([
            'message' => 'Order fetched successfully',
            'order' => $order->load(['items', 'customer', 'employee']),
        ], 200);
    }

    public function updateOrder(Request $request, $id)
    {
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
