<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Status Update</title>
</head>
<body>
    <!--<p>Hi {{ $order->customer->first_name }},</p>-->
    <p>Hi {{ $order->orderAddress->firstname ?? $order->customer->first_name }},</p>


    @if($status == '1' || $status == 'processing')
        <p><strong>Your order {{ $order->order_id }} is now being packed.</strong></p>
        <p>We’ll notify you once it is dispatched.</p>
    @elseif($status == '2' || $status == 'shipped')
        <p><strong>Your order {{ $order->order_id }} has been dispatched.</strong></p>
        <p>It is on the way!</p>
    @elseif($status == '3' || $status == 'out_for_delivery')
        <p><strong>Your order {{ $order->order_id }} is out for delivery.</strong></p>
        @if($order->delivery_person_name)
            <p><strong>Delivery Person:</strong> {{ $order->delivery_person_name }}</p>
            <p><strong>Contact:</strong> {{ $order->delivery_person_phone }}</p>
        @endif
        <p>Please keep your phone available for the delivery agent.</p>
    @elseif($status == '4' || $status == 'delivered')
        <p><strong>Your order {{ $order->order_id }} has been delivered.</strong></p>
        <p>Thank you for shopping with us!</p>
    @else
        <p>Your order status has been updated to: {{ ucfirst($status) }}.</p>
    @endif

    <p>Regards,<br>Team AuraMart</p>
</body>
</html>
