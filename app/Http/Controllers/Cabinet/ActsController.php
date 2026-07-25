<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Enums\ActStatus;
use App\Models\Act;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;

/**
 * Акты выполненных работ в личном кабинете.
 *
 * Маршруты (все под /cabinet/acts): index — список, create — форма,
 * show — просмотр (AJAX), store — создание (AJAX), next-number — подбор номера,
 * destroy / bulk-delete — удаление. Вся логика — в базовом DocumentController;
 * здесь только конфигурация для актов.
 */
class ActsController extends DocumentController
{
    protected string $modelClass   = Act::class;
    protected string $listView     = 'cabinet.acts';
    protected string $listVariable = 'acts';
    protected string $createView   = 'cabinet.act-create';
    protected string $responseKey  = 'act';

    protected string $missingContractorMessage = 'Укажите заказчика';
    protected string $duplicateNumberMessage   = 'Акт с номером «%s» уже существует. Измените номер и попробуйте снова.';

    protected function ownedDocuments(): HasMany
    {
        return auth()->user()->acts();
    }

    protected function draftStatus(): BackedEnum
    {
        return ActStatus::Draft;
    }

    /**
     * GET /cabinet/acts/{act} — данные акта для модального просмотра (AJAX).
     */
    public function show(Act $act): JsonResponse
    {
        return $this->showDocument($act);
    }

    /**
     * DELETE /cabinet/acts/{act} — удаление акта (AJAX).
     */
    public function destroy(Act $act): JsonResponse
    {
        return $this->destroyDocument($act);
    }
}
