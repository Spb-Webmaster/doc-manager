<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Services\PdfService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActPdfController extends Controller
{
    public function __construct(private readonly PdfService $pdfService) {}

    /**
     * Скачать PDF акта.
     *
     * Если файл отсутствует (например, не успел сгенерироваться) —
     * генерируем его прямо сейчас и сразу отдаём пользователю.
     */
    public function download(Act $act): StreamedResponse
    {
        // Только свои акты
        abort_unless($act->user_id === auth()->id(), 403);

        // Файл отсутствует — генерируем синхронно
        if (! $act->pdf_path || ! Storage::disk('pdf')->exists($act->pdf_path)) {
            $path = $this->pdfService->generateAct($act);
            $act->pdf_path = $path;
            $act->saveQuietly();
        }

        $filename = "Акт-{$act->number}.pdf";

        return Storage::disk('pdf')->download($act->pdf_path, $filename);
    }
}
