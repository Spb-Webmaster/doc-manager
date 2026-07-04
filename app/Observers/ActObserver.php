<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\GenerateActPdf;
use App\Models\Act;
use App\Services\PdfService;

class ActObserver
{
    /**
     * Акт создан → ставим задачу генерации PDF в очередь.
     *
     * При driver=sync (дефолт) задача выполнится сразу же, синхронно.
     * При настроенной очереди (Redis/database) — асинхронно в фоне.
     */
    public function created(Act $act): void
    {
        GenerateActPdf::dispatch($act);
    }

    /**
     * Акт обновлён → перегенерируем PDF, данные могли измениться.
     *
     * Job использует saveQuietly() при записи pdf_path, поэтому
     * повторного вызова updated() не происходит — рекурсии нет.
     */
    public function updated(Act $act): void
    {
        GenerateActPdf::dispatch($act);
    }

    /**
     * Акт удалён → немедленно удаляем PDF-файл из Storage.
     *
     * Выполняется синхронно (не через Job), потому что к моменту
     * выполнения очереди запись в БД уже не существует и модель
     * нельзя будет восстановить для получения пути.
     */
    public function deleted(Act $act): void
    {
        app(PdfService::class)->deleteAct($act);
    }
}
