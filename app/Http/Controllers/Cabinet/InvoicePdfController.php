<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoicePdfController extends Controller
{
    public function __construct(private readonly PdfService $pdfService) {}

    /**
     * Скачать PDF счёта.
     *
     * Если файл отсутствует (например, не успел сгенерироваться) —
     * генерируем его прямо сейчас и сразу отдаём пользователю.
     */
    public function download(Invoice $invoice): StreamedResponse
    {
        // Только свои счета
        abort_unless($invoice->user_id === auth()->id(), 403);

        // Файл отсутствует — генерируем синхронно
        if (! $invoice->pdf_path || ! Storage::disk('pdf')->exists($invoice->pdf_path)) {
            $path = $this->pdfService->generateInvoice($invoice);
            $invoice->pdf_path = $path;
            $invoice->saveQuietly();
        }

        $filename = "Счёт-{$invoice->number}.pdf";

        return Storage::disk('pdf')->download($invoice->pdf_path, $filename);
    }
}
