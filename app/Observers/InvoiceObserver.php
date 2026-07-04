<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\GenerateInvoicePdf;
use App\Models\Invoice;
use App\Services\PdfService;

class InvoiceObserver
{
    /**
     * Счёт создан → ставим задачу генерации PDF в очередь.
     *
     * При driver=sync (дефолт) задача выполнится сразу же, синхронно.
     * При настроенной очереди (Redis/database) — асинхронно в фоне.
     */
    public function created(Invoice $invoice): void
    {
        GenerateInvoicePdf::dispatch($invoice);
    }

    /**
     * Счёт обновлён → перегенерируем PDF, данные могли измениться.
     *
     * Job использует saveQuietly() при записи pdf_path, поэтому
     * повторного вызова updated() не происходит — рекурсии нет.
     */
    public function updated(Invoice $invoice): void
    {
        GenerateInvoicePdf::dispatch($invoice);
    }

    /**
     * Счёт удалён → немедленно удаляем PDF-файл из Storage.
     *
     * Выполняется синхронно (не через Job), потому что к моменту
     * выполнения очереди запись в БД уже не существует и модель
     * нельзя будет восстановить для получения пути.
     */
    public function deleted(Invoice $invoice): void
    {
        app(PdfService::class)->deleteInvoice($invoice);
    }
}
