<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Status Update</title>
</head>
<body>
    <!--<p>Hi <?php echo e($order->customer->first_name); ?>,</p>-->
    <p>Hi <?php echo e($order->orderAddress->firstname ?? $order->customer->first_name); ?>,</p>


    <?php if($status == '1' || $status == 'processing'): ?>
        <p><strong>Your order <?php echo e($order->order_id); ?> is now being packed.</strong></p>
        <p>We’ll notify you once it is dispatched.</p>
    <?php elseif($status == '2' || $status == 'shipped'): ?>
        <p><strong>Your order <?php echo e($order->order_id); ?> has been dispatched.</strong></p>
        <p>It is on the way!</p>
    <?php elseif($status == '3' || $status == 'out_for_delivery'): ?>
        <p><strong>Your order <?php echo e($order->order_id); ?> is out for delivery.</strong></p>
        <?php if($order->delivery_person_name): ?>
            <p><strong>Delivery Person:</strong> <?php echo e($order->delivery_person_name); ?></p>
            <p><strong>Contact:</strong> <?php echo e($order->delivery_person_phone); ?></p>
        <?php endif; ?>
        <p>Please keep your phone available for the delivery agent.</p>
    <?php elseif($status == '4' || $status == 'delivered'): ?>
        <p><strong>Your order <?php echo e($order->order_id); ?> has been delivered.</strong></p>
        <p>Thank you for shopping with us!</p>
    <?php else: ?>
        <p>Your order status has been updated to: <?php echo e(ucfirst($status)); ?>.</p>
    <?php endif; ?>

    <p>Regards,<br>Team AuraMart</p>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\pauls\palVarnanDashboard\resources\views/emails/order_status_updated.blade.php ENDPATH**/ ?>