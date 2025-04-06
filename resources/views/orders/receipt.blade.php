<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Receipt #{{ $order->order_id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4a5568;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2d3748;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            color: #4a5568;
            margin: 5px 0;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            flex: 1;
            padding: 15px;
            background: #f7fafc;
            border-radius: 5px;
            margin: 0 10px;
        }
        .info-box h2 {
            color: #2d3748;
            font-size: 16px;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            color: #4a5568;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #2d3748;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #4a5568;
            color: #4a5568;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background: #f6e05e; color: #744210; }
        .status-shipped { background: #4299e1; color: #1a365d; }
        .status-for_confirm { background: #9f7aea; color: #44337a; }
        .status-completed { background: #48bb78; color: #22543d; }
        .status-cancelled { background: #f56565; color: #742a2a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tech Components Store</h1>
            <p>Order Receipt</p>
            <p>#{{ $order->order_id }}</p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h2>Order Information</h2>
                <div class="info-item">
                    <span class="info-label">Order ID:</span> #{{ $order->order_id }}
                </div>
                <div class="info-item">
                    <span class="info-label">Date:</span> 
                    @if(is_string($order->date_ordered))
                        {{ \Carbon\Carbon::parse($order->date_ordered)->format('M d, Y H:i') }}
                    @else
                        {{ $order->date_ordered->format('M d, Y H:i') }}
                    @endif
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-{{ $order->status }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
            </div>

            <div class="info-box">
                <h2>Customer Information</h2>
                <div class="info-item">
                    <span class="info-label">Name:</span> {{ $order->account->user->first_name }} {{ $order->account->user->last_name }}
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span> {{ $order->account->user->email }}
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span> {{ $order->account->user->phone_number }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderInfos as $item)
                <tr>
                    <td>
                        <div>{{ $item->item->item_name }}</div>
                        <div style="font-size: 12px; color: #718096;">{{ $item->item->item_description }}</div>
                    </td>
                    <td>P{{ number_format($item->item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>P{{ number_format($item->item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total Amount: P{{ number_format($order->total_amount, 2) }}
        </div>

        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Tech Components Store - Your One-Stop Shop for Microcontrollers and Electronics</p>
            <p>For any inquiries, please contact our support team</p>
        </div>
    </div>
</body>
</html> 