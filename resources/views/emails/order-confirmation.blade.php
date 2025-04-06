<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4a86e8;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .order-details {
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .order-header {
            background-color: #f2f2f2;
            padding: 10px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        .order-items {
            padding: 10px;
        }
        .order-item {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .order-total {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: right;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $order->account->user->first_name ?? 'Customer' }},</p>
            
            <p>Thank you for your order! We're pleased to confirm that we've received your order.</p>
            
            <div class="order-details">
                <div class="order-header">
                    <div>Order #{{ $order->order_id }}</div>
                    <div>Date: {{ $order->order_date->format('M d, Y') }}</div>
                </div>
                
                <div class="order-items">
                    @foreach($order->orderItems as $item)
                    <div class="order-item">
                        <div><strong>{{ $item->item->item_name }}</strong></div>
                        <div>Quantity: {{ $item->quantity }}</div>
                        <div>Price: ₱{{ number_format($item->price, 2) }}</div>
                        <div>Total: ₱{{ number_format($item->price * $item->quantity, 2) }}</div>
                    </div>
                    @endforeach
                </div>
                
                <div class="order-total">
                    Total: ₱{{ number_format($order->total_amount, 2) }}
                </div>
            </div>
            
            <p>Shipping Address: {{ $order->shipping_address }}</p>
            <p>Payment Method: {{ ucfirst($order->payment_method) }}</p>
            
            <p>If you have any questions about your order, please contact our customer service at customer-support@techstore.com.</p>
            
            <p>Thank you for shopping with us!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Tech Store. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html> 