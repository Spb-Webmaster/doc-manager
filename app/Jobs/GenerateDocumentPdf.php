<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Act;
use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Генерация PDF для документа кабинета — акта или счёта.
 */
class GenerateDocumentPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Act|Invoice $document) {}

    /**
     * Выполняется через очередь (при driver=sync — синхронно сразу после
     * создания/обновления). Использует saveQuietly() для сохранения pdf_path,
     * чтобы не вызывать DocumentPdfObserver повторно.
     */
    public function handle(PdfService $pdfService): void
    {
        try {
            // Перед перегенерацией удаляем только старый PDF —
            // файлы печати и подписи ещё нужны для нового
            $pdfService->deletePdf($this->document);

            // Генерируем новый PDF и получаем путь к нему
            $path = $pdfService->generate($this->document);

            // Сохраняем путь в БД без вызова событий модели (избегаем рекурсии)
            $this->document->pdf_path = $path;
            $this->document->saveQuietly();

        } catch (\Throwable $e) {
            // Логируем ошибку: документ уже создан, PDF можно перегенерировать позже
            Log::error('Ошибка генерации PDF документа', [
                'document' => $this->document::class,
                'id'       => $this->document->id,
                'error'    => $e->getMessage(),
            ]);

            throw $e; // Бросаем для повторной попытки (если очередь настроена)
        }
    }
}
