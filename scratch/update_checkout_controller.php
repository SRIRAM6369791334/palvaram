<?php
$file = __DIR__ . '/../palVarnan/app/Http/Controllers/CheckoutController.php';
$content = file_get_contents($file);

// 1. Add Mail import
if (strpos($content, 'use Illuminate\Support\Facades\Mail;') === false) {
    $content = str_replace(
        "use Illuminate\Support\Facades\Log;",
        "use Illuminate\Support\Facades\Log;\nuse Illuminate\Support\Facades\Mail;",
        $content
    );
}

// 2. Add email dispatch before return in placeOrder()
$targetReturn = "return response()->json([\n            'status' => 'success',\n            'message' => 'Order placed successfully!',";
if (strpos($content, "'message' => 'Order placed successfully!',") !== false) {
    $emailBlock = <<<'PHP'
        // 8. Dual Email Notifications (Customer + Admin) with non-blocking probe
        try {
            if ($this->isSmtpReachable()) {
                $emailItems = [];
                foreach ($cartItems as $ci) {
                    $vLabel = '';
                    if ($ci->variant) {
                        if ($ci->variant->size || $ci->variant->color) {
                            $parts = [];
                            if ($ci->variant->size) $parts[] = 'Size: ' . $ci->variant->size->size;
                            if ($ci->variant->color) $parts[] = 'Color: ' . $ci->variant->color->color;
                            $vLabel = implode(' | ', $parts);
                        } else {
                            $vLabel = ($ci->variant->value ?? '') . ($ci->variant->unit->short_name ?? '');
                        }
                    }
                    $emailItems[] = [
                        'name' => $ci->product->product_name ?? 'Product',
                        'variant' => $vLabel,
                        'quantity' => $ci->product_quantity,
                        'price' => $ci->variant->offer_price ?? $ci->product->product_price ?? 0
                    ];
                }

                $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', 'sriramsri1234321@gmail.com'));
                $fromEmail  = config('mail.from.address', env('MAIL_FROM_ADDRESS', 'orders@palvarnan.com'));
                $fromName   = config('mail.from.name', env('MAIL_FROM_NAME', 'Pal Varnan'));

                // Customer confirmation email
                if (!empty($request->shipping_email)) {
                    Mail::send('emails.order_customer', ['order' => $order, 'items' => $emailItems], function ($msg) use ($request, $order, $fromEmail, $fromName) {
                        $msg->to($request->shipping_email)
                            ->from($fromEmail, $fromName)
                            ->subject("Order Confirmation #{$order->order_number} - Pal Varnan");
                    });
                }

                // Admin order alert
                if (!empty($adminEmail)) {
                    Mail::send('emails.order_admin', ['order' => $order, 'items' => $emailItems], function ($msg) use ($adminEmail, $order, $fromEmail, $fromName) {
                        $msg->to($adminEmail)
                            ->from($fromEmail, $fromName)
                            ->subject("New Order Received #{$order->order_number} - ₹" . number_format($order->total_amount, 2));
                    });
                }
            }
        } catch (\Throwable $mailEx) {
            Log::warning('Order notification email dispatch skipped: ' . $mailEx->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order placed successfully!',
PHP;

    $pattern = '/return response\(\)->json\(\[\s*\'status\'\s*=>\s*\'success\',\s*\'message\'\s*=>\s*\'Order placed successfully!\',/s';
    $content = preg_replace($pattern, $emailBlock, $content, 1);
}

// 3. Add isSmtpReachable() method at the end of class
$helperMethod = <<<'PHP'
    /**
     * Non-blocking SMTP availability check (1.2s timeout).
     */
    private function isSmtpReachable(): bool
    {
        $host = config('mail.mailers.smtp.host', env('MAIL_HOST', 'smtp.gmail.com'));
        $port = (int) config('mail.mailers.smtp.port', env('MAIL_PORT', 587));

        $socket = @fsockopen($host, $port, $errno, $errstr, 1.2);
        if ($socket) {
            fclose($socket);
            return true;
        }
        return false;
    }
}
PHP;

// Replace closing brace of class
$lastBracePos = strrpos($content, '}');
if ($lastBracePos !== false) {
    $content = substr($content, 0, $lastBracePos) . $helperMethod . "\n";
}

file_put_contents($file, $content);
echo "CheckoutController.php updated successfully with order emails!\n";
