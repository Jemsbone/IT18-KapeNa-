<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Ready to Pickup - Kape Na!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #d3ad7f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-container {
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 300px;
            height: auto;
        }
        .header h1 {
            color: #d3ad7f;
            margin: 0;
            font-size: 28px;
        }
        .ready-badge {
            background-color: #28a745;
            color: #ffffff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        .order-number {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .order-number strong {
            color: #d3ad7f;
            font-size: 18px;
        }
        .section {
            margin: 25px 0;
        }
        .section h2 {
            color: #2c2c2c;
            border-bottom: 2px solid #d3ad7f;
            padding-bottom: 10px;
            font-size: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 10px;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-info {
            flex: 1;
        }
        .item-name {
            font-weight: bold;
            color: #2c2c2c;
            margin-bottom: 5px;
        }
        .item-details {
            color: #666;
            font-size: 14px;
        }
        .item-total {
            font-weight: bold;
            color: #d3ad7f;
            font-size: 16px;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(isset($logoDataUri) && $logoDataUri)
            <div class="logo-container">
                <img src="{{ $logoDataUri }}" alt="Kape Na! Logo" />
            </div>
            @else
            <h1>☕ Kape Na!</h1>
            @endif
            <p>Order Ready to Pickup</p>
        </div>

        <div class="ready-badge">
            ✅ Your Order is Ready!
        </div>

        <div class="order-number">
            <strong>Order Number: {{ $order->order_number }}</strong>
        </div>

        <div class="info-box">
            <p><strong>Great news!</strong> Your order is now ready for pickup.</p>
            <p>Please come to our store to collect your order at your earliest convenience.</p>
        </div>

        <div class="section">
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
        </div>

        <div class="section">
            <h2>Order Items</h2>
            @if(is_array($order->cart_data))
                @foreach($order->cart_data as $item)
                <div class="cart-item">
                    <div class="item-info">
                        <div class="item-name">{{ $item['name'] ?? 'Unknown Product' }}</div>
                        <div class="item-details">
                            Quantity: {{ $item['quantity'] ?? 1 }} × ₱{{ number_format($item['price'] ?? 0, 2) }}
                        </div>
                    </div>
                    <div class="item-total">
                        ₱{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        @if($order->order_summary)
        <div class="section">
            <h2>Order Summary</h2>
            <p><strong>Total Amount:</strong> ₱{{ number_format($order->total_amount, 2) }}</p>
            <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
            <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'pending') }}</p>
        </div>
        @endif

        <div class="info-box">
            <p><strong>📍 Pickup Location:</strong></p>
            <p>Kape Na! - Caraga State University - Main Campus</p>
            <p><strong>📞 Contact:</strong> +639914677225 | +639512592678</p>
        </div>

        <div class="footer">
            <p>Thank you for choosing Kape Na!</p>
            <p>We look forward to serving you again soon.</p>
            <p>If you have any questions, please contact us at magbanuajembo17@gmail.com</p>
            <p style="margin-top: 20px;">
                <strong>Kape Na!</strong><br>
                Caraga State University - Main Campus<br>
                +639914677225 | +639512592678
            </p>
        </div>
    </div>
</body>
</html>

