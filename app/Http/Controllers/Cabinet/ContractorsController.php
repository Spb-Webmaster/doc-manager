<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Контрагенты (заказчики) в личном кабинете.
 *
 * Страницы: список контрагентов и форма добавления.
 * AJAX (JSON): создание, обновление, удаление, списки счетов/актов контрагента.
 */
class ContractorsController extends Controller
{
    /**
     * GET /cabinet/contractors — страница списка контрагентов пользователя.
     *
     * Отдаёт view 'cabinet.contractors' с массивом контрагентов (реквизиты,
     * контакты, банк) и счётчиками счетов/актов по каждому.
     */
    public function index(): View
    {
        $contractors = auth()->user()
            ->contractors()
            ->withCount(['invoices', 'acts'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($c) => [
                'id'        => $c->id,
                'inn'       => $c->inn,
                'kpp'       => $c->kpp,
                'ogrn'      => $c->ogrn,
                'name'      => $c->name,
                'full_name' => $c->full_name,
                'address'         => $c->legal_address,
                'email'           => $c->email,
                'phone'           => $c->phone,
                'person_contract' => $c->person_contract,
                'bik'             => $c->bik,
                'bank'            => $c->bank,
                'payment_account' => $c->payment_account,
                'inv_count'       => $c->invoices_count,
                'act_count'       => $c->acts_count,
            ]);

        return view('cabinet.contractors', compact('contractors'));
    }

    /**
     * GET /cabinet/contractors/create — страница формы добавления контрагента.
     *
     * Передаёт во view список уже добавленных ИНН для проверки дублей на фронте.
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        $existingInns = auth()->user()->contractors()->pluck('inn');
        return view('cabinet.contractor-create', compact('existingInns'));
    }

    /**
     * POST /cabinet/contractors — создание контрагента.
     *
     * Валидирует ИНН (10 или 12 цифр, уникален в рамках пользователя) и реквизиты.
     * Для AJAX-запроса возвращает JSON с данными созданного контрагента,
     * для обычного — redirect на список контрагентов.
     */
    public function store(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'inn'             => ['required', 'regex:/^\d{10}$|^\d{12}$/',
                                  Rule::unique('contractors', 'inn')->where('user_id', auth()->id())],
            'name'            => ['required', 'string', 'max:255'],
            'full_name'       => ['nullable', 'string', 'max:500'],
            'kpp'             => ['nullable', 'string', 'max:9'],
            'ogrn'            => ['nullable', 'string', 'max:15'],
            'address'         => ['nullable', 'string', 'max:500'],
            'email'           => ['nullable', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'person_contract' => ['nullable', 'string', 'max:255'],
            'payment_account' => ['nullable', 'string', 'max:20'],
            'bik'             => ['nullable', 'digits:9'],
            'bank'            => ['nullable', 'string', 'max:255'],
        ], [
            'inn.regex'  => 'ИНН должен содержать 10 цифр (юр. лицо) или 12 цифр (ИП).',
            'inn.unique' => 'Этот контрагент уже добавлен.',
        ]);

        $contractor = auth()->user()->contractors()->create([
            'inn'             => $validated['inn'],
            'name'            => $validated['name'],
            'full_name'       => $validated['full_name'] ?? null,
            'kpp'             => $validated['kpp'] ?? null,
            'ogrn'            => $validated['ogrn'] ?? null,
            'legal_address'   => $validated['address'] ?? null,
            'email'           => $validated['email'] ?? null,
            'phone'           => $validated['phone'] ?? null,
            'person_contract' => $validated['person_contract'] ?? null,
            'payment_account' => $validated['payment_account'] ?? null,
            'bik'             => $validated['bik'] ?? null,
            'bank'            => $validated['bank'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message'    => 'Контрагент добавлен',
                'contractor' => [
                    'id'        => $contractor->id,
                    'inn'       => $contractor->inn,
                    'kpp'       => $contractor->kpp,
                    'ogrn'      => $contractor->ogrn,
                    'name'      => $contractor->name,
                    'full_name' => $contractor->full_name,
                    'address'         => $contractor->legal_address,
                    'email'           => $contractor->email,
                    'phone'           => $contractor->phone,
                    'person_contract' => $contractor->person_contract,
                    'bik'             => $contractor->bik,
                    'bank'            => $contractor->bank,
                    'payment_account' => $contractor->payment_account,
                    'inv_count'       => 0,
                    'act_count'       => 0,
                ],
            ]);
        }

        return redirect()->route('cabinet.contractors');
    }

    /**
     * PATCH /cabinet/contractors/{contractor} — обновление контрагента (AJAX).
     *
     * Доступ только к своим контрагентам (403 для чужих). ИНН не редактируется.
     * Возвращает JSON с актуальными данными контрагента и счётчиками счетов/актов.
     */
    public function update(Request $request, Contractor $contractor): JsonResponse
    {
        abort_if($contractor->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'full_name'       => ['nullable', 'string', 'max:500'],
            'kpp'             => ['nullable', 'string', 'max:9'],
            'ogrn'            => ['nullable', 'string', 'max:15'],
            'address'         => ['nullable', 'string', 'max:500'],
            'email'           => ['nullable', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'person_contract' => ['nullable', 'string', 'max:255'],
            'payment_account' => ['nullable', 'string', 'max:20'],
            'bik'             => ['nullable', 'digits:9'],
            'bank'            => ['nullable', 'string', 'max:255'],
        ]);

        $contractor->update([
            'name'            => $validated['name'],
            'full_name'       => $validated['full_name'] ?? null,
            'kpp'             => $validated['kpp'] ?? null,
            'ogrn'            => $validated['ogrn'] ?? null,
            'legal_address'   => $validated['address'] ?? null,
            'email'           => $validated['email'] ?? null,
            'phone'           => $validated['phone'] ?? null,
            'person_contract' => $validated['person_contract'] ?? null,
            'payment_account' => $validated['payment_account'] ?? null,
            'bik'             => $validated['bik'] ?? null,
            'bank'            => $validated['bank'] ?? null,
        ]);

        $contractor->loadCount(['invoices', 'acts']);

        return response()->json([
            'message'    => 'Контрагент обновлён',
            'contractor' => [
                'id'              => $contractor->id,
                'inn'             => $contractor->inn,
                'kpp'             => $contractor->kpp,
                'ogrn'            => $contractor->ogrn,
                'name'            => $contractor->name,
                'full_name'       => $contractor->full_name,
                'address'         => $contractor->legal_address,
                'email'           => $contractor->email,
                'phone'           => $contractor->phone,
                'person_contract' => $contractor->person_contract,
                'bik'             => $contractor->bik,
                'bank'            => $contractor->bank,
                'payment_account' => $contractor->payment_account,
                'inv_count'       => $contractor->invoices_count,
                'act_count'       => $contractor->acts_count,
            ],
        ]);
    }

    /**
     * DELETE /cabinet/contractors/{contractor} — удаление контрагента (AJAX).
     *
     * Доступ только к своим контрагентам. Возвращает JSON с сообщением.
     */
    public function destroy(Contractor $contractor): JsonResponse
    {
        abort_if($contractor->user_id !== auth()->id(), 403);
        $contractor->delete();
        return response()->json(['message' => 'Контрагент удалён']);
    }

    /**
     * GET /cabinet/contractors/{contractor}/invoices — счета контрагента (AJAX).
     *
     * Используется в раскрывающейся карточке контрагента.
     * Возвращает JSON-массив счетов: номер, дата, сумма, статус, название договора.
     */
    public function invoices(Contractor $contractor): JsonResponse
    {
        abort_unless($contractor->user_id === auth()->id(), 403);

        $rows = $contractor->invoices()
            ->with('contract:id,name')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get(['id', 'number', 'date', 'total', 'status', 'basis', 'contract_id'])
            ->map(fn($inv) => [
                'id'            => $inv->id,
                'number'        => $inv->number,
                'date'          => $inv->date?->format('Y-m-d'),
                'total'         => (float) $inv->total,
                'status'        => $inv->status->value,
                'contract_name' => $inv->contract?->name ?? $inv->basis,
            ]);

        return response()->json($rows);
    }

    /**
     * GET /cabinet/contractors/{contractor}/acts — акты контрагента (AJAX).
     *
     * Используется в раскрывающейся карточке контрагента.
     * Возвращает JSON-массив актов: номер, дата, сумма, статус.
     */
    public function acts(Contractor $contractor): JsonResponse
    {
        abort_unless($contractor->user_id === auth()->id(), 403);

        $rows = $contractor->acts()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get(['id', 'number', 'date', 'total', 'status'])
            ->map(fn($act) => [
                'id'     => $act->id,
                'number' => $act->number,
                'date'   => $act->date?->format('Y-m-d'),
                'total'  => (float) $act->total,
                'status' => $act->status->value,
            ]);

        return response()->json($rows);
    }
}
