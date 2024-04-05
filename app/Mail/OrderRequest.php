<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRequest extends Mailable
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
            ->subject('Pesanan baru ' . $this->data->order_id . ' Menunggu Konfirmasi')
            ->view('order.request')
            ->with('data', $this->data);
    }
}
