<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Act;
use App\Models\Invoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    // Человекочитаемые названия месяцев для шаблона
    private const MONTHS = [
        1 => 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря',
    ];

    /**
     * Конфигурация, различающаяся у типов документов:
     * blade-шаблон, имя переменной во view, префикс имени файла
     * и специфичная связь для eager load.
     */
    private function config(Act|Invoice $document): array
    {
        return $document instanceof Invoice
            ? ['view' => 'pdf.invoice', 'var' => 'invoice', 'prefix' => 'счет', 'load' => 'contract']
            : ['view' => 'pdf.act',     'var' => 'act',     'prefix' => 'акт',  'load' => 'invoice'];
    }

    /**
     * Генерирует PDF документа (счёта или акта) и сохраняет файл в Storage.
     * Возвращает путь относительно диска 'pdf'.
     */
    public function generate(Act|Invoice $document): string
    {
        $cfg = $this->config($document);

        // Подгружаем все связанные данные одним запросом
        $document->loadMissing([
            'contractor',
            'bankAccount',
            'items',
            'user.legalEntity',
            'user.individualEntrepreneur',
            'user.selfEmployed',
            $cfg['load'],
        ]);

        // base 27mm при scale=100 → при 160% даёт 43mm
        $stampSize = (int) min(55, round(27 * ($document->stamp_scale     ?? 100) / 100));
        $sigHeight = (int) min(35, round(15 * ($document->signature_scale ?? 100) / 100));

        $pdf = Pdf::loadView($cfg['view'], [
            $cfg['var'] => $document,
            'seller'    => $this->resolveSeller($document->user),
            'months'    => self::MONTHS,
            'stampSrc'  => $this->imageToBase64($document->stamp_path),
            'stampSize' => $stampSize,
            'sigSrc'    => $this->imageToBase64($document->signature_path),
            'sigHeight' => $sigHeight,
        ])->setPaper('a4', 'portrait');

        $path = $this->path($document);

        Storage::disk('pdf')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Удаляет только PDF-файл документа — используется перед перегенерацией,
     * когда файлы печати и подписи ещё нужны для нового PDF.
     */
    public function deletePdf(Act|Invoice $document): void
    {
        if ($document->pdf_path && Storage::disk('pdf')->exists($document->pdf_path)) {
            Storage::disk('pdf')->delete($document->pdf_path);
        }
    }

    /**
     * Удаляет все файлы документа (PDF, печать, подпись) из Storage —
     * используется при удалении самого документа.
     */
    public function deleteFiles(Act|Invoice $document): void
    {
        foreach ([$document->pdf_path, $document->stamp_path, $document->signature_path] as $path) {
            if ($path && Storage::disk('pdf')->exists($path)) {
                Storage::disk('pdf')->delete($path);
            }
        }
    }

    /**
     * Формирует путь файла документа:
     * {user_id}/{contractor_id}/{акт|счет}-{number}-{date}.pdf
     */
    private function path(Act|Invoice $document): string
    {
        $prefix = $this->config($document)['prefix'];
        $number = $this->sanitizeFilename($document->number);
        $date   = $document->date->format('d-m-Y');

        return "{$document->user_id}/{$document->contractor_id}/{$prefix}-{$number}-{$date}.pdf";
    }

    /**
     * Определяет продавца (поставщика) по данным пользователя.
     * Приоритет: ЮЛ → ИП → Самозанятый → фоллбек на имя аккаунта.
     */
    private function resolveSeller(User $user): array
    {
        if ($entity = $user->legalEntity) {
            return [
                'type'        => 'legal',
                'name'        => $entity->name ?? '',
                'full_name'   => $entity->full_name ?? $entity->name ?? '',
                'inn'         => $entity->inn ?? '',
                'kpp'         => $entity->kpp ?? '',
                'ogrn'        => $entity->ogrn ?? '',
                'address'     => $entity->legal_address ?? $entity->address ?? '',
                'director'    => $entity->director ?? '',
            ];
        }

        if ($entity = $user->individualEntrepreneur) {
            // Отбрасываем префикс "ИП", если его уже ввели в поле "Краткое наименование",
            // чтобы не задваивать его при формировании 'name'/'full_name'.
            $shortName = trim(preg_replace('/^ИП\.?\s+/iu', '', trim($entity->name ?? '')));

            return [
                'type'        => 'ip',
                'name'        => 'ИП ' . $shortName,
                'full_name'   => $entity->full_name ?? ('ИП ' . $shortName),
                'inn'         => $entity->inn ?? '',
                'kpp'         => '',
                'ogrn'        => $entity->ogrnip ?? '',
                'address'     => $entity->register_address ?? $entity->address ?? '',
                'director'    => $shortName,
            ];
        }

        if ($entity = $user->selfEmployed) {
            return [
                'type'        => 'self',
                'name'        => $entity->full_name ?? '',
                'full_name'   => 'Самозанятый ' . ($entity->full_name ?? ''),
                'inn'         => $entity->inn ?? '',
                'kpp'         => '',
                'ogrn'        => '',
                'address'     => $entity->register_address ?? $entity->address ?? '',
                'director'    => $entity->full_name ?? '',
            ];
        }

        // Данные не заполнены — минимальный фоллбек
        return [
            'type'      => 'unknown',
            'name'      => $user->name ?? '',
            'full_name' => $user->name ?? '',
            'inn'       => '', 'kpp' => '', 'ogrn' => '',
            'address'   => '', 'director' => '',
        ];
    }

    private function imageToBase64(?string $path): ?string
    {
        if (!$path || !Storage::disk('pdf')->exists($path)) return null;
        $content = Storage::disk('pdf')->get($path);
        $mime    = str_ends_with($path, '.png') ? 'image/png' : 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    /**
     * Очищает строку для безопасного использования в имени файла.
     * Заменяет символы, недопустимые в именах файлов Windows и Linux.
     */
    private function sanitizeFilename(string $name): string
    {
        return preg_replace('/[\/\\\\:*?"<>|]+/', '-', trim($name));
    }
}
