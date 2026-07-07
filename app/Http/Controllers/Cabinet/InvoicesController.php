<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Concerns\BulkDeletesDocuments;
use App\Http\Controllers\Concerns\SavesBase64Images;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    use BulkDeletesDocuments;
    use SavesBase64Images;

    public function index(Request $request): View
    {
        $user = auth()->user();

        $contractors = $user->contractors()->orderBy('name')->get(['id', 'name']);

        $query = $user->invoices()
            ->with(['contractor:id,name,inn', 'contract:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->integer('contractor_id'));
        }

        $invoices = $query->paginate(config('site.constants.paginate'))->withQueryString();

        $selectedContractorId = $request->filled('contractor_id') ? $request->integer('contractor_id') : null;

        return view('cabinet.invoices', compact('invoices', 'contractors', 'selectedContractorId'));
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

        return view('cabinet.invoice-create', compact('reqData', 'bank', 'banks', 'contractors', 'preselectedContractorId'));
    }

    public function show(Invoice $invoice): JsonResponse
    {
        abort_unless($invoice->user_id === auth()->id(), 403);

        $invoice->load('contract:id,name');

        return response()->json([
            'id'            => $invoice->id,
            'number'        => $invoice->number,
            'date'          => $invoice->date?->format('Y-m-d'),
            'basis'         => $invoice->basis,
            'contract_name' => $invoice->contract?->name,
            'subtotal'   => (float) $invoice->subtotal,
            'nds_amount' => (float) $invoice->nds_amount,
            'total'      => (float) $invoice->total,
            'items'      => $invoice->items->map(fn($it) => [
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
            'stamp_image'            => 'nullable|string',
            'stamp_scale'            => 'nullable|integer|min:50|max:200',
            'signature_image'        => 'nullable|string',
            'signature_scale'        => 'nullable|integer|min:50|max:200',
            'number'                 => 'required|string|max:50',
            'date'                   => 'required|date',
            'basis'                  => 'nullable|string|max:500',
            'nds_rate'               => 'required|integer|in:0,10,20',
            'items'                  => 'required|array|min:1',
            'items.*.name'           => 'required|string|max:512',
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
            return response()->json(['errors' => ['contractor_id' => ['Укажите получателя счёта']]], 422);
        }

        if (Invoice::where('user_id', $user->id)->where('contractor_id', $contractor->id)->where('number', $data['number'])->exists()) {
            return response()->json([
                'errors' => ['number' => ['Счёт с номером «' . $data['number'] . '» уже существует. Измените номер и попробуйте снова.']]
            ], 422);
        }

        if (!empty($data['bank_account_id'])) {
            $user->bankAccounts()->findOrFail($data['bank_account_id']);
        }

        // Если основание введено вручную (не выбран существующий договор) — создаём договор
        $contractId  = $data['contract_id'] ?? null;
        $cleanedBasis = $this->sanitizeBasis($data['basis'] ?? null);
        if (empty($contractId) && !empty($cleanedBasis)) {
            $contract = Contract::create([
                'user_id'       => $user->id,
                'contractor_id' => $contractor->id,
                'name'          => $cleanedBasis,
                'number'        => null,
                'date'          => null,
            ]);
            $contractId = $contract->id;
        }

        $ndsRate = $data['nds_rate'];
        $subtotal = collect($data['items'])->sum(fn($it) => $it['qty'] * $it['price']);
        $ndsAmount = round($subtotal * $ndsRate / 100, 2);
        $total = $subtotal + $ndsAmount;

        $invoice = Invoice::create([
            'user_id'         => $user->id,
            'contractor_id'   => $contractor->id,
            'contract_id'     => $contractId,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'number'          => $data['number'],
            'date'            => $data['date'],
            'basis'           => $this->sanitizeBasis($data['basis'] ?? null),
            'stamp_path'      => $this->saveBase64Image($data['stamp_image'] ?? null, $user->id, 'stamp'),
            'stamp_scale'     => $data['stamp_scale'] ?? 100,
            'signature_path'  => $this->saveBase64Image($data['signature_image'] ?? null, $user->id, 'sig'),
            'signature_scale' => $data['signature_scale'] ?? 100,
            'status'          => InvoiceStatus::Draft,
            'subtotal'        => $subtotal,
            'nds_amount'      => $ndsAmount,
            'total'           => $total,
        ]);

        foreach ($data['items'] as $i => $item) {
            $amount    = $item['qty'] * $item['price'];
            $itemNds   = round($amount * $ndsRate / 100, 2);
            $invoice->items()->create([
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
            'invoice'       => $invoice->id,
            'contractor_id' => $contractor->id,
            'redirect'      => route('cabinet.contractors') . '?open=' . $contractor->id,
        ], 201);
    }

    public function nextNumber(Request $request): JsonResponse
    {
        $user = auth()->user();
        $contractorId = $request->integer('contractor_id');
        $max = Invoice::where('user_id', $user->id)
            ->when($contractorId, fn($q) => $q->where('contractor_id', $contractorId))
            ->pluck('number')
            ->map(fn($n) => is_numeric($n) ? (int) $n : 0)
            ->max() ?? 0;
        return response()->json(['number' => $max + 1]);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        abort_unless($invoice->user_id === auth()->id(), 403);
        $invoice->delete();
        return response()->json(['ok' => true]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        return $this->bulkDeleteOwned($request, auth()->user()->invoices());
    }

    private function sanitizeBasis(?string $basis): ?string
    {
        if ($basis === null) return null;
        return mb_strtolower(trim($basis)) === 'без договора' ? null : (trim($basis) ?: null);
    }
}
