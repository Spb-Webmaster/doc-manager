<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Concerns\BulkDeletesDocuments;
use App\Http\Controllers\Concerns\SavesBase64Images;
use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\Contract;
use App\Models\Invoice;
use BackedEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Общая логика документов личного кабинета — актов и счетов.
 *
 * Оба типа документов устроены одинаково (номер, дата, контрагент, договор,
 * банковский счёт, позиции с НДС, печать/подпись, автогенерация PDF через
 * DocumentPdfObserver → GenerateDocumentPdf), поэтому вся логика живёт здесь,
 * а наследники (ActsController, InvoicesController) задают только модель,
 * view и тексты сообщений.
 */
abstract class DocumentController extends Controller
{
    use BulkDeletesDocuments;
    use SavesBase64Images;

    /** @var class-string<Act|Invoice> Класс модели документа. */
    protected string $modelClass;

    /** View списка документов и имя переменной с ними внутри view. */
    protected string $listView;
    protected string $listVariable;

    /** View формы создания документа. */
    protected string $createView;

    /** Ключ с id созданного документа в JSON-ответе store() — его ждёт фронтенд. */
    protected string $responseKey;

    /** Текст ошибки валидации, когда контрагент не указан. */
    protected string $missingContractorMessage;

    /** Шаблон ошибки о дубле номера (%s — введённый номер). */
    protected string $duplicateNumberMessage;

    /** Relation документов текущего пользователя (acts() / invoices()). */
    abstract protected function ownedDocuments(): HasMany;

    /** Статус «черновик» для нового документа. */
    abstract protected function draftStatus(): BackedEnum;

    /**
     * Страница списка документов текущего пользователя.
     *
     * Поддерживает фильтр по контрагенту (?contractor_id=) и пагинацию.
     * Возвращает view списка с документами, контрагентами и выбранным фильтром.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $contractors = $user->contractors()->orderBy('name')->get(['id', 'name']);

        $query = $this->ownedDocuments()
            ->with(['contractor:id,name,inn', 'contract:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->integer('contractor_id'));
        }

        $documents = $query->paginate(config('site.constants.paginate'))->withQueryString();

        return view($this->listView, [
            $this->listVariable    => $documents,
            'contractors'          => $contractors,
            'selectedContractorId' => $request->filled('contractor_id') ? $request->integer('contractor_id') : null,
        ]);
    }

    /**
     * Страница формы создания документа.
     *
     * Готовит данные для формы: реквизиты исполнителя (ЮЛ или ИП), банковские счета,
     * список контрагентов и предвыбранного контрагента (?contractor_id=).
     */
    public function create(Request $request): View
    {
        $user  = auth()->user();
        $legal = $user->legalEntity;
        $ip    = $user->individualEntrepreneur;
        $req   = $legal ?? $ip;
        $banks = $user->bankAccounts()->orderByDesc('is_primary')->orderBy('created_at')->get();
        $bank  = $banks->first();

        $reqData = null;
        if ($req && $req->inn) {
            $reqData = [
                'inn'        => $req->inn,
                'name'       => $req->full_name ?? $req->name ?? null,
                'ogrn_label' => $legal ? 'ОГРН' : 'ОГРНИП',
                'ogrn'       => $legal ? ($legal->ogrn ?? null) : ($ip->ogrnip ?? null),
                'address'    => $legal ? ($legal->legal_address ?? null) : ($ip->register_address ?? null),
            ];
        }

        $contractors = $user->contractors()->orderBy('name')->get()
            ->map(fn($c) => [
                'id'      => $c->id,
                'name'    => $c->name,
                'inn'     => $c->inn,
                'kpp'     => $c->kpp,
                'ogrn'    => $c->ogrn,
                'address' => $c->legal_address,
            ])->values();

        $preselectedContractorId = null;
        if ($request->filled('contractor_id')) {
            $id = $request->integer('contractor_id');
            if ($user->contractors()->where('id', $id)->exists()) {
                $preselectedContractorId = $id;
            }
        }

        return view($this->createView, compact('reqData', 'bank', 'banks', 'contractors', 'preselectedContractorId'));
    }

    /**
     * Данные документа для модального просмотра (AJAX).
     *
     * Доступ только к своим документам (403 для чужих).
     * Возвращает JSON с реквизитами, названием договора, суммами и позициями.
     */
    protected function showDocument(Act|Invoice $document): JsonResponse
    {
        abort_unless($document->user_id === auth()->id(), 403);

        $document->load('contract:id,name');

        return response()->json([
            'id'            => $document->id,
            'number'        => $document->number,
            'date'          => $document->date?->format('Y-m-d'),
            'basis'         => $document->basis,
            'contract_name' => $document->contract?->name,
            'subtotal'      => (float) $document->subtotal,
            'nds_amount'    => (float) $document->nds_amount,
            'total'         => (float) $document->total,
            'items'         => $document->items->map(fn($it) => [
                'name'     => $it->name,
                'unit'     => $it->unit,
                'quantity' => (float) $it->quantity,
                'price'    => (float) $it->price,
                'amount'   => (float) $it->amount,
            ]),
        ]);
    }

    /**
     * Создание документа из формы (AJAX).
     *
     * Принимает контрагента (существующего либо нового — тогда он создаётся по ИНН),
     * договор или основание, банковский счёт, печать/подпись (base64), номер, дату,
     * ставку НДС и позиции. Проверяет уникальность номера в рамках пользователя
     * и контрагента; выбранный договор проверяется на принадлежность пользователю,
     * при ручном вводе основания договор находится или создаётся. Считает суммы
     * (subtotal/НДС/итого) и сохраняет документ с позициями; PDF генерируется
     * автоматически через DocumentPdfObserver.
     * Возвращает JSON 201 {<act|invoice>, contractor_id, redirect} либо 422 с ошибками.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'contractor_id'          => 'nullable|integer',
            'new_contractor'         => 'nullable|array',
            'new_contractor.inn'     => 'required_with:new_contractor|string|max:12',
            'new_contractor.name'    => 'required_with:new_contractor|string|max:255',
            'new_contractor.kpp'     => 'nullable|string|max:9',
            'new_contractor.ogrn'    => 'nullable|string|max:15',
            'new_contractor.address' => 'nullable|string|max:500',
            'contract_id'            => 'nullable|integer',
            'bank_account_id'        => 'nullable|integer',
            'stamp_image'            => 'nullable|string',
            'stamp_scale'            => 'nullable|integer|min:50|max:200',
            'signature_image'        => 'nullable|string',
            'signature_scale'        => 'nullable|integer|min:50|max:200',
            'number'                 => 'required|string|max:50',
            'date'                   => 'required|date',
            'basis'                  => 'nullable|string|max:500',
            'nds_rate'               => 'required|integer|in:0,10,20',
            'items'                  => 'required|array|min:1',
            // 255 — предел колонки name в БД (act_items / invoice_items)
            'items.*.name'           => 'required|string|max:255',
            'items.*.unit'           => 'required|string|max:20',
            'items.*.qty'            => 'required|numeric|min:0.001',
            'items.*.price'          => 'required|numeric|min:0',
        ]);

        if (!empty($data['contractor_id'])) {
            $contractor = $user->contractors()->findOrFail($data['contractor_id']);
        } elseif (!empty($data['new_contractor'])) {
            $nc = $data['new_contractor'];
            $contractor = $user->contractors()->firstOrCreate(
                ['inn' => $nc['inn']],
                [
                    'name'          => $nc['name'],
                    'full_name'     => $nc['name'],
                    'kpp'           => $nc['kpp'] ?? null,
                    'ogrn'          => $nc['ogrn'] ?? null,
                    'legal_address' => $nc['address'] ?? null,
                ]
            );
        } else {
            return response()->json(['errors' => ['contractor_id' => [$this->missingContractorMessage]]], 422);
        }

        if ($this->ownedDocuments()->where('contractor_id', $contractor->id)->where('number', $data['number'])->exists()) {
            return response()->json([
                'errors' => ['number' => [sprintf($this->duplicateNumberMessage, $data['number'])]],
            ], 422);
        }

        if (!empty($data['bank_account_id'])) {
            $user->bankAccounts()->findOrFail($data['bank_account_id']);
        }

        // Договор: выбранный проверяем на принадлежность пользователю,
        // при ручном вводе основания — находим существующий или создаём новый
        $basis      = $this->sanitizeBasis($data['basis'] ?? null);
        $contractId = $data['contract_id'] ?? null;

        if (!empty($contractId)) {
            $user->contracts()->findOrFail($contractId);
        } elseif (!empty($basis)) {
            $contractId = Contract::firstOrCreate(
                [
                    'user_id'       => $user->id,
                    'contractor_id' => $contractor->id,
                    'name'          => $basis,
                ],
                [
                    'number' => null,
                    'date'   => null,
                ]
            )->id;
        }

        $ndsRate   = $data['nds_rate'];
        $subtotal  = collect($data['items'])->sum(fn($it) => $it['qty'] * $it['price']);
        $ndsAmount = round($subtotal * $ndsRate / 100, 2);

        $modelClass = $this->modelClass;
        $document   = $modelClass::create([
            'user_id'         => $user->id,
            'contractor_id'   => $contractor->id,
            'contract_id'     => $contractId,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'number'          => $data['number'],
            'date'            => $data['date'],
            'basis'           => $basis,
            'stamp_path'      => $this->saveBase64Image($data['stamp_image'] ?? null, $user->id, 'stamp'),
            'stamp_scale'     => $data['stamp_scale'] ?? 100,
            'signature_path'  => $this->saveBase64Image($data['signature_image'] ?? null, $user->id, 'sig'),
            'signature_scale' => $data['signature_scale'] ?? 100,
            'status'          => $this->draftStatus(),
            'subtotal'        => $subtotal,
            'nds_amount'      => $ndsAmount,
            'total'           => $subtotal + $ndsAmount,
        ]);

        foreach ($data['items'] as $i => $item) {
            $amount  = $item['qty'] * $item['price'];
            $itemNds = round($amount * $ndsRate / 100, 2);
            $document->items()->create([
                'sort_order' => $i,
                'name'       => $item['name'],
                'unit'       => $item['unit'],
                'quantity'   => $item['qty'],
                'price'      => $item['price'],
                'amount'     => $amount,
                'nds_rate'   => $ndsRate,
                'nds_amount' => $itemNds,
            ]);
        }

        return response()->json([
            $this->responseKey => $document->id,
            'contractor_id'    => $contractor->id,
            'redirect'         => route('cabinet.contractors') . '?open=' . $contractor->id,
        ], 201);
    }

    /**
     * Следующий свободный номер документа (AJAX).
     *
     * Берёт максимальный числовой номер среди документов пользователя (опционально
     * в рамках контрагента ?contractor_id=). Возвращает JSON {number: max + 1}.
     */
    public function nextNumber(Request $request): JsonResponse
    {
        $contractorId = $request->integer('contractor_id');

        $max = $this->ownedDocuments()
            ->when($contractorId, fn($q) => $q->where('contractor_id', $contractorId))
            ->pluck('number')
            ->map(fn($n) => is_numeric($n) ? (int) $n : 0)
            ->max() ?? 0;

        return response()->json(['number' => $max + 1]);
    }

    /**
     * Удаление документа (AJAX).
     *
     * Доступ только к своим документам. Файлы (PDF, печать, подпись)
     * удаляются в DocumentPdfObserver::deleted(). Возвращает JSON {ok: true}.
     */
    protected function destroyDocument(Act|Invoice $document): JsonResponse
    {
        abort_unless($document->user_id === auth()->id(), 403);
        $document->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Массовое удаление документов по списку id (AJAX).
     *
     * Удаляет только документы текущего пользователя, см. BulkDeletesDocuments.
     * Возвращает JSON {ok: true, deleted: N}.
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        return $this->bulkDeleteOwned($request, $this->ownedDocuments());
    }

    /**
     * Нормализует «основание»: пустую строку и «без договора» превращает в null.
     */
    protected function sanitizeBasis(?string $basis): ?string
    {
        if ($basis === null) return null;

        return mb_strtolower(trim($basis)) === 'без договора' ? null : (trim($basis) ?: null);
    }
}
