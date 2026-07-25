<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\GenerateDocumentPdf;
use App\Models\Act;
use App\Models\Invoice;
use App\Services\PdfService;

/**
 * Единый наблюдатель документов кабинета (актов и счетов):
 * держит PDF-файл в актуальном состоянии. Регистрируется для обеих
 * моделей в AppServiceProvider.
 */
class DocumentPdfObserver
{
    /**
     * Документ создан → ставим задачу генерации PDF в очередь.
     *
     * При driver=sync (дефолт) задача выполнится сразу же, синхронно.
     * При настроенной очереди (Redis/database) — асинхронно в фоне.
     */
    public function created(Act|Invoice $document): void
    {
        GenerateDocumentPdf::dispatch($document);
    }

    /**
     * Документ обновлён → перегенерируем PDF, данные могли измениться.
     *
     * Job использует saveQuietly() при записи pdf_path, поэтому
     * повторного вызова updated() не происходит — рекурсии нет.
     */
    public function updated(Act|Invoice $document): void
    {
        GenerateDocumentPdf::dispatch($document);
    }

    /**
     * Документ удалён → немедленно удаляем его файлы (PDF, печать, подпись).
     *
     * Выполняется синхронно (не через Job), потому что к моменту
     * выполнения очереди запись в БД уже не существует и модель
     * нельзя будет восстановить для получения путей.
     */
    public function deleted(Act|Invoice $document): void
    {
        app(PdfService::class)->deleteFiles($document);
    }
}
