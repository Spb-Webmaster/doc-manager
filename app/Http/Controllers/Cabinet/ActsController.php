<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Enums\ActStatus;
use App\Http\Controllers\Concerns\BulkDeletesDocuments;
use App\Http\Controllers\Controller;
use App\Models\Act;
use App\Models\Contract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActsController extends Controller
{
    use BulkDeletesDocuments;

    public function index(Request $request): View
    {
        $user = auth()->user();

        $contractors = $user->contractors()->orderBy('name')->get(['id', 'name']);

        $query = $user->acts()
            ->with(['contractor:id,name,inn'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->integer('contractor_id'));
        }

        $acts = $query->paginate(config('site.constants.paginate'))->withQueryString();

        $selectedContractorId = $request->filled('contractor_id') ? $request->integer('contractor_id') : null;

        return view('cabinet.acts', compact('acts', 'contractors', 'selectedContractorId'));
    }

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

        return view('cabinet.act-create', compact('reqData', 'bank', 'banks', 'contractors', 'preselectedContractorId'));
    }

    public function show(Act $act): JsonResponse
    {
        abort_unless($act->user_id === auth()->id(), 403);

        return response()->json([
            'id'         => $act->id,
            'number'     => $act->number,
            'date'       => $act->date?->format('Y-m-d'),
            'basis'      => $act->basis,
            'subtotal'   => (float) $act->subtotal,
            'nds_amount' => (float) $act->nds_amount,
            'total'      => (float) $act->total,
            'items'      => $act->items->map(fn($it) => [
                'name'     => $it->name,
                'unit'     => $it->unit,
                'quantity' => (float) $it->quantity,
                'price'    => (float) $it->price,
                'amount'   => (float) $it->amount,
            ]),
        ]);
    }

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
            'number'                 => 'required|string|max:50',
            'date'                   => 'required|date',
            'basis'                  => 'nullable|string|max:500',
            'nds_rate'               => 'required|integer|in:0,10,20',
            'items'                  => 'required|array|min:1',
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
            return response()->json(['errors' => ['contractor_id' => ['Укажите заказчика']]], 422);
        }

        if (Act::where('user_id', $user->id)->where('contractor_id', $contractor->id)->where('number', $data['number'])->exists()) {
            return response()->json([
                'errors' => ['number' => ['Акт с номером «' . $data['number'] . '» уже существует. Измените номер и попробуйте снова.']]
            ], 422);
        }

        if (!empty($data['bank_account_id'])) {
            $user->bankAccounts()->findOrFail($data['bank_account_id']);
        }

        $contractId = $data['contract_id'] ?? null;
        if (empty($contractId) && !empty($data['basis'])) {
            $contract = Contract::firstOrCreate(
                [
                    'user_id'       => $user->id,
                    'contractor_id' => $contractor->id,
                    'name'          => $data['basis'],
                ],
                [
                    'number' => null,
                    'date'   => null,
                ]
            );
            $contractId = $contract->id;
        }

        $ndsRate   = $data['nds_rate'];
        $subtotal  = collect($data['items'])->sum(fn($it) => $it['qty'] * $it['price']);
        $ndsAmount = round($subtotal * $ndsRate / 100, 2);
        $total     = $subtotal + $ndsAmount;

        $act = Act::create([
            'user_id'         => $user->id,
            'contractor_id'   => $contractor->id,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'number'          => $data['number'],
            'date'            => $data['date'],
            'basis'           => $this->sanitizeBasis($data['basis'] ?? null),
            'status'          => ActStatus::Draft,
            'subtotal'        => $subtotal,
            'nds_amount'      => $ndsAmount,
            'total'           => $total,
        ]);

        foreach ($data['items'] as $i => $item) {
            $amount  = $item['qty'] * $item['price'];
            $itemNds = round($amount * $ndsRate / 100, 2);
            $act->items()->create([
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
            'act'           => $act->id,
            'contractor_id' => $contractor->id,
            'redirect'      => route('cabinet.contractors') . '?open=' . $contractor->id,
        ], 201);
    }

    public function nextNumber(Request $request): JsonResponse
    {
        $user = auth()->user();
        $contractorId = $request->integer('contractor_id');
        $max = Act::where('user_id', $user->id)
            ->when($contractorId, fn($q) => $q->where('contractor_id', $contractorId))
            ->pluck('number')
            ->map(fn($n) => is_numeric($n) ? (int) $n : 0)
            ->max() ?? 0;
        return response()->json(['number' => $max + 1]);
    }

    public function destroy(Act $act): JsonResponse
    {
        abort_unless($act->user_id === auth()->id(), 403);
        $act->delete();
        return response()->json(['ok' => true]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        return $this->bulkDeleteOwned($request, auth()->user()->acts());
    }

    private function sanitizeBasis(?string $basis): ?string
    {
        if ($basis === null) return null;
        return mb_strtolower(trim($basis)) === 'без договора' ? null : (trim($basis) ?: null);
    }
}
