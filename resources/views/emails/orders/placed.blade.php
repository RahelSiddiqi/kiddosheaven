<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 30px 0;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <!-- Header -->
                <tr>
                    <td style="background-color: #7c3aed; padding: 30px 40px; text-align: center;">
                        <h1 style="color: #ffffff; margin: 0; font-size: 24px;">{{ config('app.name', 'KiddosHeaven') }}</h1>
                        <p style="color: #e9d5ff; margin: 8px 0 0;">Order Confirmation</p>
                    </td>
                </tr>
                <!-- Body -->
                <tr>
                    <td style="padding: 40px;">
                        <h2 style="color: #1f2937; margin:0 0 8px;">Thank you for your order!</h2>
                        <p style="color: #6b7280; margin: 0 0 24px;">Hi {{ $order->customer_name }}, your order has been received and is being processed.</p>

                        <!-- Order Info -->
                        <table width="100%" style="margin-bottom: 24px;">
                            <tr>
                                <td style="color: #6b7280; font-size: 14px;">Order Number</td>
                                <td style="color: #1f2937; font-weight: bold; text-align: right;">{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td style="color: #6b7280; font-size: 14px; padding-top: 8px;">Date</td>
                                <td style="color: #1f2937; text-align: right; padding-top: 8px;">{{ $order->created_at->format('F j, Y') }}</td>
                            </tr>
                            <tr>
                                <td style="color: #6b7280; font-size: 14px; padding-top: 8px;">Status</td>
                                <td style="text-align: right; padding-top: 8px;"><span style="background-color: #ddd6fe; color: #7c3aed; padding: 2px 10px; border-radius: 99px; font-size: 13px; text-transform: capitalize;">{{ $order->status }}</span></td>
                            </tr>
                        </table>

                        <!-- Items -->
                        <h3 style="color: #1f2937; margin: 0 0 12px; font-size: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 8px;">Items Ordered</h3>
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 24px;">
                            <thead>
                                <tr>
                                    <th style="text-align: left; color: #6b7280; font-size: 13px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">Product</th>
                                    <th style="text-align: center; color: #6b7280; font-size: 13px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">Qty</th>
                                    <th style="text-align: right; color: #6b7280; font-size: 13px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">Price</th>
                                    <th style="text-align: right; color: #6b7280; font-size: 13px; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td style="padding: 10px 0; color: #1f2937; font-size: 14px; border-bottom: 1px solid #f3f4f6;">{{ $item->product_name ?? $item->name }}</td>
                                    <td style="padding: 10px 0; color: #6b7280; font-size: 14px; text-align: center; border-bottom: 1px solid #f3f4f6;">{{ $item->quantity }}</td>
                                    <td style="padding: 10px 0; color: #6b7280; font-size: 14px; text-align: right; border-bottom: 1px solid #f3f4f6;">৳{{ number_format($item->price, 2) }}</td>
                                    <td style="padding: 10px 0; color: #1f2937; font-size: 14px; text-align: right; border-bottom: 1px solid #f3f4f6; font-weight: 600;">৳{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Totals -->
                        <table width="100%" style="margin-bottom: 24px;">
                            <tr>
                                <td style="color: #6b7280; font-size: 14px; padding: 4px 0;">Subtotal</td>
                                <td style="color: #1f2937; text-align: right; font-size: 14px; padding: 4px 0;">৳{{ number_format($order->subtotal ?? $order->total, 2) }}</td>
                            </tr>
                            @if(($order->tax ?? 0) > 0)
                            <tr>
                                <td style="color: #6b7280; font-size: 14px; padding: 4px 0;">Tax</td>
                                <td style="color: #1f2937; text-align: right; font-size: 14px; padding: 4px 0;">৳{{ number_format($order->tax, 2) }}</td>
                            </tr>
                            @endif
                            @if(($order->shipping_cost ?? 0) > 0)
                            <tr>
                                <td style="color: #6b7280; font-size: 14px; padding: 4px 0;">Shipping</td>
                                <td style="color: #1f2937; text-align: right; font-size: 14px; padding: 4px 0;">৳{{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="color: #1f2937; font-size: 16px; font-weight: bold; padding-top: 8px; border-top: 2px solid #e5e7eb;">Grand Total</td>
                                <td style="color: #7c3aed; font-size: 16px; font-weight: bold; text-align: right; padding-top: 8px; border-top: 2px solid #e5e7eb;">৳{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </table>

                        <!-- Shipping Address -->
                        @if($order->shipping_address ?? $order->address)
                        <h3 style="color: #1f2937; margin: 0 0 8px; font-size: 16px;">Shipping Address</h3>
                        <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0 0 24px;">
                            {{ $order->shipping_address ?? $order->address }}
                        </p>
                        @endif
                    </td>
                </tr>
                <!-- Footer -->
                <tr>
                    <td style="background-color: #f9fafb; padding: 24px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                        <p style="color: #9ca3af; font-size: 13px; margin: 0;">Questions? Reply to this email or contact us at <a href="mailto:{{ config('mail.from.address', 'support@example.com') }}" style="color: #7c3aed;">{{ config('mail.from.address', 'support@example.com') }}</a></p>
                        <p style="color: #d1d5db; font-size: 12px; margin: 8px 0 0;">&copy; {{ date('Y') }} {{ config('app.name', 'KiddosHeaven') }}. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
