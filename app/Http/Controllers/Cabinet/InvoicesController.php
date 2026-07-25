<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;

/**
 * Счета на оплату в личном кабинете.
 *
 * Маршруты (все под /cabinet/invoices): index — список, create — форма,
 * show — просмотр (AJAX), store — создание (AJAX), next-number — подбор номера,
 * destroy / bulk-delete — удаление. Вся логика — в базовом DocumentController;
 * здесь только конфигурация для счетов.
 */
class InvoicesController extends DocumentController
{
    protected string $modelClass   = Invoice::class;
    protected string $listView     = 'cabinet.invoices';
    protected string $listVariable = 'invoices';
    protected string $createView   = 'cabinet.invoice-create';
    protected string $responseKey  = 'invoice';

    protected string $missingContractorMessage = 'Укажите получателя счёта';
    protected string $duplicateNumberMessage   = 'Счёт с номером «%s» уже существует. Измените номер и попробуйте снова.';

    protected function ownedDocuments(): HasMany
    {
        return auth()->user()->invoices();
    }

    protected function draftStatus(): BackedEnum
    {
        return InvoiceStatus::Draft;
    }

    /**
     * GET /cabinet/invoices/{invoice} — данные счёта для модального просмотра (AJAX).
     */
    public function show(Invoice $invoice): JsonResponse
    {
        return $this->showDocument($invoice);
    }

    /**
     * DELETE /cabinet/invoices/{invoice} — удаление счёта (AJAX).
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        return $this->destroyDocument($invoice);
    }
}
