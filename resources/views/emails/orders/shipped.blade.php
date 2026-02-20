<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Order Has Shipped</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #0891b2; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; color: #333; }
        .tracking-box { background: #ecfdf5; border: 1px solid #6ee7b7; border-radius: 8px; padding: 20px; margin: 24px 0; text-align: center; }
        .tracking-box .number { font-size: 20px; font-weight: bold; letter-spacing: 2px; color: #065f46; }
        .btn { display: inline-block; background: #0891b2; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; margin-top: 12px; }
        .footer { background: #f9fafb; padding: 20px 32px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Your Order Is On Its Way! 📦</h1>
        <p style="margin:4px 0;opacity:.85">Order #{{ $shipment->order->order_number }}</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $shipment->order->customer_name }}</strong>,</p>
        <p>Great news! Your order has been shipped and is on its way to you.</p>

        @if($shipment->tracking_number)
        <div class="tracking-box">
            <p style="margin:0 0 8px">Tracking Number</p>
            <div class="number">{{ $shipment->tracking_number }}</div>
            <p style="margin:8px 0 0;font-size:13px;color:#065f46">Carrier: {{ strtoupper($shipment->carrier ?? 'N/A') }}</p>
            @if($shipment->tracking_link)
            <a href="{{ $shipment->tracking_link }}" class="btn">Track Your Order</a>
            @endif
        </div>
        @endif

        @if($shipment->estimated_delivery)
        <p><strong>Estimated Delivery:</strong> {{ \Carbon\Carbon::parse($shipment->estimated_delivery)->format('M d, Y') }}</p>
        @endif

        <p>Ship to: {{ $shipment->order->address_line }}, {{ $shipment->order->city }} {{ $shipment->order->postal_code }}</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} KiddosHeaven. All rights reserved.</p>
    </div>
</div>
</body>
</html>
