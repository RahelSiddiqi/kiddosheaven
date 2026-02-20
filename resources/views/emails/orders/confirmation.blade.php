<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #4f46e5; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; color: #333; }
        .order-meta { background: #f9fafb; border-radius: 6px; padding: 16px; margin-bottom: 24px; }
        .order-meta p { margin: 4px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { text-align: left; font-size: 13px; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding: 8px 4px; }
        td { padding: 10px 4px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        .totals { text-align: right; font-size: 14px; }
        .totals .total { font-size: 16px; font-weight: bold; color: #111; }
        .footer { background: #f9fafb; padding: 20px 32px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Order Confirmed!</h1>
        <p style="margin:4px 0;opacity:.85">Order #{{ $order->order_number }}</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $order->customer_name }}</strong>,</p>
        <p>Thank you for your order! We're getting it ready and will notify you when it ships.</p>

        <div class="order-meta">
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
            <p><strong>Payment:</strong> {{ strtoupper($order->payment_method) }}</p>
            <p><strong>Ship to:</strong> {{ $order->address_line }}, {{ $order->city }} {{ $order->postal_code }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th style="text-align:right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="text-align:right">৳{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p>Subtotal: ৳{{ number_format($order->subtotal, 2) }}</p>
            @if($order->discount_amount > 0)
            <p style="color:#16a34a">Discount: −৳{{ number_format($order->discount_amount, 2) }}</p>
            @endif
            <p>Tax: ৳{{ number_format($order->tax_amount, 2) }}</p>
            <p class="total">Total: ৳{{ number_format($order->total_amount, 2) }}</p>
        </div>
    </div>
    <div class="footer">
        <p>If you have questions, reply to this email or contact our support team.</p>
        <p>© {{ date('Y') }} KiddosHeaven. All rights reserved.</p>
    </div>
</div>
</body>
</html>
