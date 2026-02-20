<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>You left something in your cart!</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #f59e0b; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        td { padding: 10px 4px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        .btn { display: inline-block; background: #f59e0b; color: #fff; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-size: 15px; font-weight: bold; margin-top: 16px; }
        .coupon-box { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 16px; margin: 20px 0; text-align: center; }
        .footer { background: #f9fafb; padding: 20px 32px; font-size: 12px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Don't forget your cart!</h1>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $user->name }}</strong>,</p>
        <p>You left some items in your shopping cart. Come back and complete your purchase!</p>

        <table>
            @foreach($cartItems as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td align="right">{{ $item['quantity'] }} × ৳{{ number_format($item['price'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td><strong>Total</strong></td>
                <td align="right"><strong>৳{{ number_format($cartTotal, 2) }}</strong></td>
            </tr>
        </table>

        @if($couponCode)
        <div class="coupon-box">
            <p style="margin:0 0 8px">Use code <strong>{{ $couponCode }}</strong> for a special discount!</p>
        </div>
        @endif

        <p style="text-align:center">
            <a href="{{ url('/cart') }}" class="btn">Complete Your Purchase →</a>
        </p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} KiddosHeaven. You received this because you have an account with us.</p>
    </div>
</div>
</body>
</html>
