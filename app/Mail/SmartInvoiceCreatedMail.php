<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Act;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SmartInvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Invoice $invoice,
        public readonly ?Act $act = null,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Счёт № ' . $this->invoice->number . ' создан по шаблону')
            ->view('emails.smart-invoice-created');
    }
}
