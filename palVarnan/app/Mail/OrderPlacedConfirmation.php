<?php

namespace App\Mail;

use App\Models\ProductOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(ProductOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject("Order Confirmed! #" . $this->order->order_number . " - Pal Varnan")
                    ->view('emails.order_confirmation');
    }
}
