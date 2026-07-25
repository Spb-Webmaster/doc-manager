<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\Invoice;
use App\Services\PdfService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Скачивание PDF документов кабинета — актов и счетов.
 */
class DocumentPdfController extends Controller
{
    public function __construct(private readonly PdfService $pdfService) {}

    /**
     * GET /cabinet/acts/{act}/pdf — скачать PDF акта.
     */
    public function act(Act $act): StreamedResponse
    {
        return $this->download($act, "Акт-{$act->number}.pdf");
    }

    /**
     * GET /cabinet/invoices/{invoice}/pdf — скачать PDF счёта.
     */
    public function invoice(Invoice $invoice): StreamedResponse
    {
        return $this->download($invoice, "Счёт-{$invoice->number}.pdf");
    }

    /**
     * Отдаёт PDF документа. Доступ только к своим документам.
     *
     * Если файл отсутствует (например, не успел сгенерироваться) —
     * генерируем его прямо сейчас и сразу отдаём пользователю.
     */
    private function download(Act|Invoice $document, string $filename): StreamedResponse
    {
        abort_unless($document->user_id === auth()->id(), 403);

        if (! $document->pdf_path || ! Storage::disk('pdf')->exists($document->pdf_path)) {
            $document->pdf_path = $this->pdfService->generate($document);
            $document->saveQuietly();
        }

        return Storage::disk('pdf')->download($document->pdf_path, $filename);
    }
}
