<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Pal Varnan</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 640px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1c799b 0%, #0d9488 100%); padding: 36px 32px; text-align: center;">
                            <h1 style="margin: 0; font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                Pal Varnan
                            </h1>
                            <p style="margin: 6px 0 0; font-size: 14px; color: #ccfbf1; font-weight: 500;">
                                Order Confirmation &amp; Invoice Summary
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 32px 24px;">
                            <h2 style="margin: 0 0 12px; font-size: 20px; font-weight: 700; color: #0f172a;">
                                Thank you, {{ $order->billing_name ?? 'Valued Customer' }}!
                            </h2>
                            <p style="margin: 0 0 24px; font-size: 15px; color: #475569; line-height: 1.6;">
                                Your order has been placed successfully and is being prepared for dispatch. Here are your order details:
                            </p>

                            <!-- Order Meta Box -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                                <tr>
                                    <td width="50%" style="padding: 6px 12px; font-size: 13px; color: #64748b;">
                                        <strong>Order Number:</strong><br>
                                        <span style="font-size: 15px; font-weight: 700; color: #1c799b;">#{{ $order->order_number }}</span>
                                    </td>
                                    <td width="50%" style="padding: 6px 12px; font-size: 13px; color: #64748b;">
                                        <strong>Order Date:</strong><br>
                                        <span style="color: #0f172a;">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : date('d M Y, h:i A') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" style="padding: 6px 12px; font-size: 13px; color: #64748b;">
                                        <strong>Payment Method:</strong><br>
                                        <span style="color: #0f172a; font-weight: 600;">{{ $order->payment_method }}</span>
                                    </td>
                                    <td width="50%" style="padding: 6px 12px; font-size: 13px; color: #64748b;">
                                        <strong>Payment Status:</strong><br>
                                        <span style="color: {{ strtolower($order->payment_status) === 'paid' ? '#059669' : '#d97706' }}; font-weight: 700;">{{ $order->payment_status }}</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Items Table -->
                            <div style="font-size: 14px; font-weight: 700; text-transform: uppercase; color: #1c799b; letter-spacing: 0.5px; margin-bottom: 12px;">
                                📦 Items Ordered
                            </div>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 24px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                                        <th style="padding: 10px 8px; font-size: 12px; color: #64748b; text-transform: uppercase;">Product</th>
                                        <th style="padding: 10px 8px; font-size: 12px; color: #64748b; text-transform: uppercase; text-align: center;">Qty</th>
                                        <th style="padding: 10px 8px; font-size: 12px; color: #64748b; text-transform: uppercase; text-align: right;">Price</th>
                                        <th style="padding: 10px 8px; font-size: 12px; color: #64748b; text-transform: uppercase; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px 8px;">
                                            <div style="font-weight: 600; font-size: 14px; color: #0f172a;">{{ $item->product_name }}</div>
                                            @if(!empty($item->variant_name))
                                            <div style="font-size: 12px; color: #64748b;">{{ $item->variant_name }}</div>
                                            @endif
                                        </td>
                                        <td style="padding: 12px 8px; text-align: center; font-size: 14px; color: #475569;">
                                            {{ $item->quantity }}
                                        </td>
                                        <td style="padding: 12px 8px; text-align: right; font-size: 14px; color: #475569;">
                                            ₹{{ number_format($item->price, 2) }}
                                        </td>
                                        <td style="padding: 12px 8px; text-align: right; font-weight: 600; font-size: 14px; color: #0f172a;">
                                            ₹{{ number_format($item->total, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Totals Calculation -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
                                <tr>
                                    <td width="60%"></td>
                                    <td width="40%">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #64748b;">Subtotal:</td>
                                                <td style="padding: 4px 0; font-size: 14px; font-weight: 600; color: #0f172a; text-align: right;">₹{{ number_format($order->subtotal, 2) }}</td>
                                            </tr>
                                            @if((float)$order->gst_amount > 0)
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #64748b;">GST / Tax:</td>
                                                <td style="padding: 4px 0; font-size: 14px; font-weight: 600; color: #0f172a; text-align: right;">+₹{{ number_format($order->gst_amount, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #64748b;">Shipping Charge:</td>
                                                <td style="padding: 4px 0; font-size: 14px; font-weight: 600; color: #0f172a; text-align: right;">
                                                    {{ (float)$order->shipping_charge > 0 ? '+₹' . number_format($order->shipping_charge, 2) : 'FREE' }}
                                                </td>
                                            </tr>
                                            @if((float)$order->coupon_discount > 0)
                                            <tr>
                                                <td style="padding: 4px 0; font-size: 13px; color: #059669; font-weight: 600;">Coupon Discount:</td>
                                                <td style="padding: 4px 0; font-size: 14px; font-weight: 700; color: #059669; text-align: right;">-₹{{ number_format($order->coupon_discount, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr style="border-top: 2px solid #e2e8f0;">
                                                <td style="padding: 10px 0; font-size: 16px; font-weight: 700; color: #0f172a;">Grand Total:</td>
                                                <td style="padding: 10px 0; font-size: 18px; font-weight: 800; color: #1c799b; text-align: right;">₹{{ number_format($order->total_amount, 2) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Delivery Address Box -->
                            <div style="font-size: 14px; font-weight: 700; text-transform: uppercase; color: #1c799b; letter-spacing: 0.5px; margin-bottom: 8px;">
                                📍 Delivery Details
                            </div>
                            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 24px; font-size: 14px; color: #334155; line-height: 1.5;">
                                <strong>{{ $order->shipping_name }}</strong><br>
                                {{ $order->shipping_door_no }}, {{ $order->shipping_street }}<br>
                                @if(!empty($order->shipping_area) && $order->shipping_area !== '-')
                                {{ $order->shipping_area }}<br>
                                @endif
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
                                Phone: <strong>{{ $order->shipping_phone }}</strong> | Email: <strong>{{ $order->shipping_email }}</strong>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 32px; text-align: center;">
                            <p style="margin: 0 0 8px; font-size: 13px; color: #64748b;">
                                Need help with your order? Reach out to our customer care team at <a href="mailto:Kalaimern2005@gmail.com" style="color: #1c799b; font-weight: 600; text-decoration: none;">Kalaimern2005@gmail.com</a>
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                &copy; {{ date('Y') }} Pal Varnan. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
