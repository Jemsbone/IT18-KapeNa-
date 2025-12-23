<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Kape Na!</title>
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
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .summary-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .summary-table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .summary-total {
            background-color: #f8f9fa;
            font-size: 18px;
            color: #2c2c2c;
        }
        .payment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .payment-info strong {
            color: #d3ad7f;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #d3ad7f;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
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
            <p>Order Confirmation</p>
        </div>

        <div class="order-number">
            <strong>Order Number: {{ $order->order_number }}</strong>
        </div>

        <div class="section">
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
        </div>

        <div class="section">
            <h2>Order Items</h2>
            @foreach($cartData as $item)
            <div class="cart-item">
                <div class="item-info">
                    <div class="item-name">{{ $item['product_name'] ?? $item['name'] ?? 'Unknown Product' }}</div>
                    <div class="item-details">
                        Quantity: {{ $item['quantity'] }} × ₱{{ number_format($item['product_price'] ?? $item['price'] ?? 0, 2) }}
                    </div>
                </div>
                <div class="item-total">
                    ₱{{ number_format($item['subtotal'] ?? (($item['product_price'] ?? $item['price'] ?? 0) * $item['quantity']), 2) }}
                </div>
            </div>
            @endforeach
        </div>

        <div class="section">
            <h2>Order Summary</h2>
            <table class="summary-table">
                <tr>
                    <td>Subtotal:</td>
                    <td>₱{{ number_format($orderSummary['subtotal'], 2) }}</td>
                </tr>
                <tr>
                    <td>Service Fee (5%):</td>
                    <td>₱{{ number_format($orderSummary['service_fee'] ?? $orderSummary['serviceFee'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax (10%):</td>
                    <td>₱{{ number_format($orderSummary['tax'], 2) }}</td>
                </tr>
                <tr class="summary-total">
                    <td><strong>Total:</strong></td>
                    <td><strong>₱{{ number_format($orderSummary['total'], 2) }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Payment Information</h2>
            <div class="payment-info">
                <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                @if($order->payment_method === 'gcash' && isset($order->payment_details['phone_number']))
                    <p><strong>GCash Phone Number:</strong> {{ $order->payment_details['phone_number'] }}</p>
                @endif
                @if($order->payment_method === 'bank' && isset($order->payment_details['bank_account']))
                    <p><strong>Bank Account:</strong> {{ $order->payment_details['bank_account'] }}</p>
                @endif
                <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your order!</p>
            <p>We'll prepare your order and notify you once it's ready.</p>
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

