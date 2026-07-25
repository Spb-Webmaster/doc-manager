<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Contractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Договоры контрагентов в личном кабинете (полностью AJAX, без своих страниц).
 *
 * Используются в карточке контрагента и как «основание» при создании счетов и актов.
 */
class ContractsController extends Controller
{
    /**
     * GET /cabinet/contractors/{contractor}/contracts — список договоров контрагента (AJAX).
     *
     * Доступ только к своим контрагентам. Возвращает JSON-массив договоров
     * в порядке ручной сортировки (sort_order).
     */
    public function index(Contractor $contractor): JsonResponse
    {
        abort_unless($contractor->user_id === auth()->id(), 403);

        return response()->json(
            $contractor->contracts()->orderBy('sort_order')->orderByDesc('id')->get()
        );
    }

    /**
     * POST /cabinet/contracts/reorder — сохранение порядка договоров (AJAX, drag&drop).
     *
     * Принимает массив ids в новом порядке; обновляет sort_order только
     * у договоров текущего пользователя. Возвращает JSON {ok: true}.
     */
    public function reorder(Request $request): JsonResponse
    {
        $ids = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer'],
        ])['ids'];

        $userIds = Contract::where('user_id', auth()->id())->pluck('id')->flip();

        foreach ($ids as $order => $id) {
            if ($userIds->has($id)) {
                Contract::where('id', $id)->update(['sort_order' => $order]);
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * POST /cabinet/contractors/{contractor}/contracts — создание договора (AJAX).
     *
     * Доступ только к своим контрагентам. Принимает название, номер и дату.
     * Возвращает JSON 201 с созданным договором.
     */
    public function store(Request $request, Contractor $contractor): JsonResponse
    {
        abort_unless($contractor->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'number' => 'required|string|max:100',
            'date'   => 'required|date',
        ]);

        $contract = $contractor->contracts()->create([
            ...$data,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['contract' => $contract], 201);
    }

    /**
     * PATCH /cabinet/contracts/{contract} — обновление договора (AJAX).
     *
     * Доступ только к своим договорам. Возвращает JSON с обновлённым договором.
     */
    public function update(Request $request, Contract $contract): JsonResponse
    {
        abort_unless($contract->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'number' => 'required|string|max:100',
            'date'   => 'required|date',
        ]);

        $contract->update($data);

        return response()->json(['contract' => $contract->fresh()]);
    }

    /**
     * DELETE /cabinet/contracts/{contract} — удаление договора (AJAX).
     *
     * Доступ только к своим договорам. Возвращает JSON с сообщением.
     */
    public function destroy(Contract $contract): JsonResponse
    {
        abort_unless($contract->user_id === auth()->id(), 403);

        $contract->delete();

        return response()->json(['message' => 'Договор удалён']);
    }
}
