<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\SmartInvoiceCreatedMail;
use App\Models\Act;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSmartInvoiceCreatedMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Invoice $invoice,
        public readonly ?Act $act,
        public readonly string $email,
    ) {}

    /**
     * Отправка письма о счёте (и акте), созданных по расписанию умного счёта.
     *
     * Выполняется через очередь, чтобы недоступность почтового сервера
     * не мешала обработке команды smart-invoices:process.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new SmartInvoiceCreatedMail($this->invoice, $this->act));
        } catch (\Throwable $e) {
            Log::error('Ошибка отправки письма о счёте по шаблону', [
                'invoice_id' => $this->invoice->id,
                'act_id'     => $this->act?->id,
                'email'      => $this->email,
                'error'      => $e->getMessage(),
            ]);

            throw $e; // Бросаем для повторной попытки (если очередь настроена)
        }
    }
}
