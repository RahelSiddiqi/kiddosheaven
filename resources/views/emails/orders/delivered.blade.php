<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Delivered</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #16a34a; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; color: #333; }
        .return-box { background: #fef9c3; border: 1px solid #fde047; border-radius: 8px; padding: 16px; margin: 24px 0; font-size: 13px; }
        .btn { display: inline-block; background: #16a34a; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; margin-top: 8px; }
        .footer { background: #f9fafb; padding: 20px 32px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Order Delivered! ✅</h1>
        <p style="margin:4px 0;opacity:.85">Order #{{ $order->order_number }}</p>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $order->customer_name }}</strong>,</p>
        <p>Your order has been delivered! We hope you love your purchase.</p>

        <p style="text-align:center;margin:24px 0">
            <a href="{{ url('/account/orders/' . $order->id) }}" class="btn">View Order</a>
        </p>

        <div class="return-box">
            <strong>Not happy with your order?</strong><br>
            You can request a return or exchange within 7 days of delivery.
            <br><br>
            <a href="{{ url('/account/returns/new?order=' . $order->id) }}" style="color:#854d0e">Request a Return →</a>
        </div>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} KiddosHeaven. All rights reserved.</p>
    </div>
</div>
</body>
</html>
