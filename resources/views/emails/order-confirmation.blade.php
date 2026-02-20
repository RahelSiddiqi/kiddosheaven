<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — Kiddo's Heaven</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; color: #1e1e1e; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .header { background: #018790; padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,.8); font-size: 14px; }
        .body { padding: 32px 40px; }
        .greeting { font-size: 17px; font-weight: 600; margin-bottom: 8px; }
        .sub { font-size: 14px; color: #555; margin-bottom: 24px; }
        .order-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px 24px; margin-bottom: 24px; }
        .order-box h2 { margin: 0 0 16px; font-size: 15px; color: #018790; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; border-bottom: 1px solid #f0f0f0; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #6b7280; }
        .info-row .value { font-weight: 600; color: #111; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 24px; }
        .items-table th { text-align: left; padding: 10px 12px; background: #f3f4f6; color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .4px; }
        .items-table td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        .items-table tr:last-child td { border-bottom: none; }
        .total-row td { font-weight: 700; font-size: 15px; padding-top: 14px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #fef3c7; color: #92400e; }
        .badge.confirmed { background: #d1fae5; color: #065f46; }
        .address-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px 20px; font-size: 14px; color: #374151; margin-bottom: 24px; }
        .address-box h2 { margin: 0 0 8px; font-size: 13px; color: #018790; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
        .cta { text-align: center; margin: 24px 0; }
        .cta a { display: inline-block; background: #018790; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 700; font-size: 15px; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
        .footer a { color: #018790; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    {{-- Header --}}
    <div class="header">
        <h1>🎉 Order Confirmed!</h1>
        <p>Thank you for shopping with Kiddo's Heaven</p>
    </div>

    {{-- Body --}}
    <div class="body">
        <p class="greeting">Hi {{ $order->customer_name }},</p>
        <p class="sub">
            We've received your order and we're getting it ready.
            You'll receive another update once it ships.
        </p>

        {{-- Order Info --}}
        <div class="order-box">
            <h2>Order Details</h2>
            <div class="info-row">
                <span class="label">Order Number</span>
                <span class="value">#{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="label">Order Date</span>
                <span class="value">{{ $order->created_at->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Payment Method</span>
                <span class="value">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($order->payment_method) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Status</span>
                <span class="value"><span class="badge confirmed">{{ ucfirst($order->status) }}</span></span>
            </div>
        </div>

        {{-- Items --}}
        @if ($order->items && $order->items->count())
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product_name ?? ($item->product->name ?? 'Product') }}</strong>
                        @if (!empty($item->variant_label))
                            <div style="font-size:12px;color:#9ca3af">{{ $item->variant_label }}</div>
                        @endif
                    </td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">৳{{ number_format($item->price * $item->quantity, 0) }}</td>
                </tr>
                @endforeach
                @if ($order->discount_amount > 0)
                <tr>
                    <td colspan="2" style="text-align:right;color:#059669">Discount</td>
                    <td style="text-align:right;color:#059669">−৳{{ number_format($order->discount_amount, 0) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="2" style="text-align:right">Total</td>
                    <td style="text-align:right;color:#018790">৳{{ number_format($order->total_amount, 0) }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- Shipping Address --}}
        <div class="address-box">
            <h2>Shipping Address</h2>
            {{ $order->address_line }}, {{ $order->city }}
            @if ($order->postal_code) — {{ $order->postal_code }} @endif
        </div>

        {{-- CTA --}}
        <div class="cta">
            <a href="{{ url('/orders') }}">View Your Order</a>
        </div>

        <p style="font-size:13px;color:#6b7280;text-align:center">
            Questions? Contact us at
            <a href="mailto:hello@kiddosheaven.com" style="color:#018790">hello@kiddosheaven.com</a>
        </p>
    </div>

    <div class="footer">
        © {{ date('Y') }} Kiddo's Heaven. All rights reserved.<br>
        <a href="{{ url('/') }}">Visit our store</a> &bull;
        <a href="{{ url('/track-order') }}">Track your order</a>
    </div>
</div>
</body>
</html>
