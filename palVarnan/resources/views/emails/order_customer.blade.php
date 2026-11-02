<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmation - Pal Varnan</title>
<style>
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 24px 12px; color: #1f2937; line-height: 1.5; }
.card { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; }
.header { background: linear-gradient(135deg, #0c716a 0%, #15803d 100%); color: #ffffff; padding: 32px 28px; text-align: center; }
.header h1 { margin: 0 0 6px; font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
.header p { margin: 0; opacity: 0.9; font-size: 14px; }
.content { padding: 32px 28px; }
.badge { display: inline-block; background-color: #ecfdf5; color: #047857; font-weight: 700; font-size: 12px; padding: 4px 12px; border-radius: 9999px; border: 1px solid #a7f3d0; margin-bottom: 16px; text-transform: uppercase; }
.order-box { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px 20px; margin: 20px 0; }
.order-box table { width: 100%; border-collapse: collapse; font-size: 14px; }
.order-box td { padding: 6px 0; }
.order-box td:first-child { color: #6b7280; width: 40%; }
.order-box td:last-child { font-weight: 600; color: #111827; }
.items-table { width: 100%; border-collapse: collapse; margin: 24px 0 16px; font-size: 14px; }
.items-table th { text-align: left; padding: 10px 8px; border-bottom: 2px solid #e5e7eb; font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
.items-table td { padding: 12px 8px; border-bottom: 1px solid #f3f4f6; }
.items-table td.qty { text-align: center; color: #4b5563; }
.items-table td.amt { text-align: right; font-weight: 600; }
.total-table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 14px; }
.total-table td { padding: 6px 8px; }
.total-table td:first-child { color: #6b7280; text-align: right; }
.total-table td:last-child { text-align: right; font-weight: 600; width: 120px; }
.total-table tr.grand-total td { font-size: 16px; font-weight: 700; color: #0c716a; border-top: 2px solid #e5e7eb; padding-top: 10px; }
.footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px; text-align: center; font-size: 12px; color: #6b7280; }
.btn { display: inline-block; background-color: #0c716a; color: #ffffff !important; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 16px; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Pal Varnan</h1>
        <p>Pure Quality • Authentic Flavors • Handpicked Gifts</p>
    </div>
    <div class="content">
        <span class="badge">Order Confirmed</span>
        <h2 style="margin: 0 0 10px; font-size: 20px; color: #111827;">Thank you for your order, {{ $order->billing_name }}!</h2>
        <p style="margin: 0 0 16px; font-size: 14px; color: #4b5563;">
            We have received your order <strong>#{{ $order->order_number }}</strong>. Our team is carefully packaging your items for prompt dispatch.
        </p>

        <div class="order-box">
            <table>
                <tr>
                    <td>Order ID</td>
                    <td>#{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td>Order Date</td>
                    <td>{{ date('d M Y, h:i A') }}</td>
                </tr>
                <tr>
                    <td>Payment Method</td>
                    <td>{{ $order->payment_method }} ({{ $order->payment_status }})</td>
                </tr>
                <tr>
                    <td>Delivery Address</td>
                    <td>{{ $order->shipping_door_no }}, {{ $order->shipping_street }}, {{ $order->shipping_city }} - {{ $order->shipping_pincode }}, {{ $order->shipping_state }}</td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="qty">Qty</th>
                    <th class="amt">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #111827;">{{ $item['name'] }}</div>
                        @if(!empty($item['variant']))
                            <div style="font-size: 12px; color: #6b7280;">{{ $item['variant'] }}</div>
                        @endif
                    </td>
                    <td class="qty">× {{ $item['quantity'] }}</td>
                    <td class="amt">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="total-table">
            <tr>
                <td>Subtotal</td>
                <td>₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->gst_amount > 0)
            <tr>
                <td>Taxes (GST)</td>
                <td>₹{{ number_format($order->gst_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td>Shipping Charge</td>
                <td>₹{{ number_format($order->shipping_charge, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Total Paid</td>
                <td>₹{{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>

        <div style="text-align: center; margin-top: 28px;">
            <a href="{{ url('/') }}" class="btn">Continue Shopping with Pal Varnan</a>
        </div>
    </div>
    <div class="footer">
        <p style="margin: 0 0 6px;">Need assistance with your delivery? Contact us anytime at support@palvarnan.com or call +91 8778509639</p>
        <p style="margin: 0;">© {{ date('Y') }} Pal Varnan. All rights reserved.</p>
    </div>
</div>
</body>
</html>
