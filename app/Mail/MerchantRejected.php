<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MerchantRejected extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this
            ->from(env('MAIL_FROM_ADDRESS', 'hakiahmad756@gmail.com'))
            ->subject('Pesanan Dibatalkan ' . $this->data->order_id . ' Oleh Penjual')
            ->view('order.merchantrejected')
            ->with('data', $this->data); 
    }
}
