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
     * Генерирует PDF для счёта и сохраняет файл в Storage.
     * Возвращает путь относительно диска 'pdf'.
     */
    public function generateInvoice(Invoice $invoice): string
    {
        // Подгружаем все связанные данные одним запросом
        $invoice->loadMissing([
            'contractor',
            'bankAccount',
            'contract',
            'items',
            'user.legalEntity',
            'user.individualEntrepreneur',
            'user.selfEmployed',
        ]);

        $seller = $this->resolveSeller($invoice->user);

        // base 27mm при scale=100 → при 160% даёт 43mm
        $stampSize = (int) min(55, round(27 * ($invoice->stamp_scale     ?? 100) / 100));
        $sigHeight = (int) min(35, round(15 * ($invoice->signature_scale ?? 100) / 100));

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice'   => $invoice,
            'seller'    => $seller,
            'months'    => self::MONTHS,
            'stampSrc'  => $this->imageToBase64($invoice->stamp_path),
            'stampSize' => $stampSize,
            'sigSrc'    => $this->imageToBase64($invoice->signature_path),
            'sigHeight' => $sigHeight,
        ])->setPaper('a4', 'portrait');

        $path = $this->invoicePath($invoice);

        Storage::disk('pdf')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Генерирует PDF для акта и сохраняет файл в Storage.
     * Возвращает путь относительно диска 'pdf'.
     */
    public function generateAct(Act $act): string
    {
        $act->loadMissing([
            'contractor',
            'bankAccount',
            'invoice',
            'items',
            'user.legalEntity',
            'user.individualEntrepreneur',
            'user.selfEmployed',
        ]);

        $seller = $this->resolveSeller($act->user);

        $stampSize = (int) min(55, round(27 * ($act->stamp_scale     ?? 100) / 100));
        $sigHeight = (int) min(35, round(15 * ($act->signature_scale ?? 100) / 100));

        $pdf = Pdf::loadView('pdf.act', [
            'act'       => $act,
            'seller'    => $seller,
            'months'    => self::MONTHS,
            'stampSrc'  => $this->imageToBase64($act->stamp_path),
            'stampSize' => $stampSize,
            'sigSrc'    => $this->imageToBase64($act->signature_path),
            'sigHeight' => $sigHeight,
        ])->setPaper('a4', 'portrait');

        $path = $this->actPath($act);

        Storage::disk('pdf')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Удаляет PDF-файл счёта из Storage, если он существует.
     */
    public function deleteInvoice(Invoice $invoice): void
    {
        foreach ([$invoice->pdf_path, $invoice->stamp_path, $invoice->signature_path] as $path) {
            if ($path && Storage::disk('pdf')->exists($path)) {
                Storage::disk('pdf')->delete($path);
            }
        }
    }

    /**
     * Удаляет PDF-файл акта из Storage, если он существует.
     */
    public function deleteAct(Act $act): void
    {
        foreach ([$act->pdf_path, $act->stamp_path, $act->signature_path] as $path) {
            if ($path && Storage::disk('pdf')->exists($path)) {
                Storage::disk('pdf')->delete($path);
            }
        }
    }

    /**
     * Формирует путь файла счёта:
     * {user_id}/{contractor_id}/счет-{number}-{date}.pdf
     */
    private function invoicePath(Invoice $invoice): string
    {
        $number = $this->sanitizeFilename($invoice->number);
        $date   = $invoice->date->format('d-m-Y');

        return "{$invoice->user_id}/{$invoice->contractor_id}/счет-{$number}-{$date}.pdf";
    }

    /**
     * Формирует путь файла акта:
     * {user_id}/{contractor_id}/акт-{number}-{date}.pdf
     */
    private function actPath(Act $act): string
    {
        $number = $this->sanitizeFilename($act->number);
        $date   = $act->date->format('d-m-Y');

        return "{$act->user_id}/{$act->contractor_id}/акт-{$number}-{$date}.pdf";
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
