<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateInvoicePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Invoice $invoice) {}

    /**
     * Генерация PDF для счёта.
     *
     * Выполняется через очередь (при driver=sync — синхронно сразу после создания/обновления).
     * Использует saveQuietly() для сохранения pdf_path, чтобы не вызывать Observer повторно.
     */
    public function handle(PdfService $pdfService): void
    {
        try {
            // Перед перегенерацией удаляем старый файл, если он есть
            $pdfService->deleteInvoice($this->invoice);

            // Генерируем новый PDF и получаем путь к нему
            $path = $pdfService->generateInvoice($this->invoice);

            // Сохраняем путь в БД без вызова событий модели (избегаем рекурсии)
            $this->invoice->pdf_path = $path;
            $this->invoice->saveQuietly();

        } catch (\Throwable $e) {
            // Логируем ошибку, но не падаем — счёт уже создан, PDF можно перегенерировать позже
            Log::error('Ошибка генерации PDF счёта', [
                'invoice_id' => $this->invoice->id,
                'error'      => $e->getMessage(),
            ]);

            throw $e; // Бросаем для повторной попытки (если очередь настроена)
        }
    }
}
