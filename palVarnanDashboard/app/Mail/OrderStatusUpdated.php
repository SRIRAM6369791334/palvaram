<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $status;

    public function __construct($order, $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Order Status Update')
                    ->view('emails.order_status_updated');
    }
}
