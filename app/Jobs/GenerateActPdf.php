<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Act;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateActPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Act $act) {}

    /**
     * Генерация PDF для акта.
     *
     * Выполняется через очередь (при driver=sync — синхронно сразу после создания/обновления).
     * Использует saveQuietly() для сохранения pdf_path, чтобы не вызывать Observer повторно.
     */
    public function handle(PdfService $pdfService): void
    {
        try {
            // Перед перегенерацией удаляем старый файл, если он есть
            $pdfService->deleteAct($this->act);

            // Генерируем новый PDF и получаем путь к нему
            $path = $pdfService->generateAct($this->act);

            // Сохраняем путь в БД без вызова событий модели (избегаем рекурсии)
            $this->act->pdf_path = $path;
            $this->act->saveQuietly();

        } catch (\Throwable $e) {
            // Логируем ошибку, но не падаем — акт уже создан, PDF можно перегенерировать позже
            Log::error('Ошибка генерации PDF акта', [
                'act_id' => $this->act->id,
                'error'  => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
