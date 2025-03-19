<!DOCTYPE html>
<html>
<head>
    <title>Order Status Update</title>
</head>
<body>
    <h1>Order #{{ $order->id }} Status Update</h1>
    <p>Hello,</p>
    <p>Your order has been updated to: <strong>{{ $newStatus }}</strong>.</p>
    <p><strong>Details:</strong></p>
    <ul>
        <li>Order ID: {{ $order->id }}</li>
        <li>Total Price: ${{ $order->total_price }}</li>
        <li>Payment Status: {{ $order->payment_status }}</li>
    </ul>
    <p>Track your order: <a href="{{ url('/api/orders/' . $order->id . '/track') }}">Click here</a></p>
    <p>Thank you for using Pharmacy API!</p>
</body>
</html>