<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .order-details {
            margin: 20px 0;
        }
        .order-items {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .order-items th, .order-items td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-items th {
            background-color: #f0f0f0;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Received</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $order->customer->name }},</p>
            
            <p>Thank you for your order! We have received your laundry and will process it shortly.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> #{{ $order->id }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                @if($order->pickup_time)
                    <p><strong>Pickup Date:</strong> {{ \Carbon\Carbon::parse($order->pickup_time)->format('F d, Y') }}</p>
                @endif
                @if($order->delivery_time)
                    <p><strong>Expected Delivery:</strong> {{ \Carbon\Carbon::parse($order->delivery_time)->format('F d, Y') }}</p>
                @endif
            </div>

            <h3>Items</h3>
            <table class="order-items">
                <thead>
                    <tr>
                        <th>Service Type</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ ucfirst($item->service_type) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>UGX {{ number_format($item->price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                <p>Subtotal: UGX {{ number_format($order->subtotal, 2) }}</p>
                <p>Tax: UGX {{ number_format($order->tax, 2) }}</p>
                <p>Total: UGX {{ number_format($order->total, 2) }}</p>
            </div>

            @if($order->special_instructions)
            <p><strong>Special Instructions:</strong><br>{{ $order->special_instructions }}</p>
            @endif
        </div>
        
        <div class="footer">
            <p>If you have any questions, please contact us.</p>
            <p>Thank you for choosing our laundry service!</p>
        </div>
    </div>
</body>
</html>