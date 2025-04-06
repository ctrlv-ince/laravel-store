<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt</title>
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
            <h1>Order Receipt</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $order->account->user->first_name ?? 'Customer' }},</p>
            
            <p>Thank you for your order! We're pleased to confirm that we've received your order #{{ $order->order_id }}.</p>
            
            <div class="order-details">
                <div class="order-header">
                    <div>Order #{{ $order->order_id }}</div>
                    <div>Date: {{ $order->date_ordered->format('M d, Y H:i') }}</div>
                    <div>Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}</div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                    <div style="flex: 1; padding-right: 10px;">
                        <h3 style="margin: 0 0 10px 0; font-size: 16px;">Customer Information</h3>
                        <div>{{ $order->account->user->first_name }} {{ $order->account->user->last_name }}</div>
                        <div>{{ $order->account->user->email }}</div>
                        <div>{{ $order->account->user->phone_number ?? 'N/A' }}</div>
                    </div>
                    
                    <div style="flex: 1; padding-left: 10px;">
                        <h3 style="margin: 0 0 10px 0; font-size: 16px;">Payment Information</h3>
                        <div>Payment Method: {{ ucfirst($order->payment_method ?? 'Standard') }}</div>
                        <div>Payment Status: {{ ucfirst($order->payment_status ?? 'Completed') }}</div>
                        <div>Transaction ID: {{ $order->transaction_id ?? 'N/A' }}</div>
                    </div>
                </div>
                
                <div class="order-items">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background-color: #f2f2f2;">
                            <tr>
                                <th style="text-align: left; padding: 8px;">Item</th>
                                <th style="text-align: right; padding: 8px;">Price</th>
                                <th style="text-align: center; padding: 8px;">Quantity</th>
                                <th style="text-align: right; padding: 8px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderInfos as $item)
                            <tr>
                                <td style="padding: 8px; border-bottom: 1px solid #eee;">
                                    <strong>{{ $item->item->item_name }}</strong>
                                    <div style="font-size: 12px; color: #777;">{{ $item->item->item_description ?? '' }}</div>
                                </td>
                                <td style="text-align: right; padding: 8px; border-bottom: 1px solid #eee;">₱{{ number_format($item->item->price, 2) }}</td>
                                <td style="text-align: center; padding: 8px; border-bottom: 1px solid #eee;">{{ $item->quantity }}</td>
                                <td style="text-align: right; padding: 8px; border-bottom: 1px solid #eee;">₱{{ number_format($item->item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="order-total">
                    <div style="margin-top: 10px;">Subtotal: ₱{{ number_format($order->total_amount, 2) }}</div>
                    <div>Shipping: ₱{{ number_format($order->shipping_fee ?? 0, 2) }}</div>
                    <div>Tax: ₱{{ number_format($order->tax ?? 0, 2) }}</div>
                    <div style="font-weight: bold; margin-top: 5px; font-size: 16px;">Total: ₱{{ number_format($order->total_amount + ($order->shipping_fee ?? 0) + ($order->tax ?? 0), 2) }}</div>
                </div>
            </div>
            
            <div style="margin: 20px 0; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #4a86e8;">
                <h3 style="margin: 0 0 10px 0; font-size: 16px;">Delivery Information</h3>
                <p style="margin: 0;">Your order will be processed and shipped as soon as possible. You will receive a notification when your order has been shipped.</p>
                <p style="margin: 5px 0 0 0;">Estimated delivery: 3-5 business days</p>
            </div>
            
            <p>A PDF copy of your receipt is attached to this email for your records.</p>
            
            <p>If you have any questions about your order, please contact our customer service at <a href="mailto:customer-support@techstore.com" style="color: #4a86e8;">customer-support@techstore.com</a> or call us at (123) 456-7890.</p>
            
            <p>Thank you for shopping with us!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Tech Store. All rights reserved.</p>
            <p>This is an automated email, please do not reply.</p>
        </div>
    </div>
</body>
</html>