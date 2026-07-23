<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\InvoiceTemplate;
use App\Models\SmartInvoice;
use App\Services\SmartInvoiceActService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SmartInvoicesController extends Controller
{
    public function __construct(private readonly SmartInvoiceActService $actService)
    {
    }

    public function index(): View
    {
        $templates = auth()->user()
            ->smartInvoices()
            ->with(['invoiceTemplate.contractor', 'invoiceTemplate.bankAccount'])
            ->orderByDesc('id')
            ->get();

        return view('cabinet.templates', compact('templates'));
    }

    public function showTemplate(SmartInvoice $smartInvoice): JsonResponse
    {
        abort_unless($smartInvoice->user_id === auth()->id(), 403);

        $smartInvoice->load(['invoiceTemplate.contractor', 'invoiceTemplate.bankAccount']);
        $tpl = $smartInvoice->invoiceTemplate;

        return response()->json([
            'id'            => $smartInvoice->id,
            'period_months' => $smartInvoice->period_months,
            'day_of_month'  => $smartInvoice->day_of_month,
            'with_act'      => $smartInvoice->with_act,
            'is_active'     => $smartInvoice->is_active,
            'next_run_at'   => $smartInvoice->next_run_at?->format('d.m.Y'),
            'last_run_at'   => $smartInvoice->last_run_at?->format('d.m.Y H:i'),
            'contractor'    => $tpl->contractor ? [
                'name' => $tpl->contractor->name,
                'inn'  => $tpl->contractor->inn,
            ] : null,
            'basis'         => $tpl->basis,
            'nds_rate'      => $tpl->nds_rate,
            'items'         => collect($tpl->items)->map(fn($it) => [
                'name'  => $it['name'],
                'unit'  => $it['unit'],
                'qty'   => $it['qty'],
                'price' => $it['price'],
            ])->values(),
        ]);
    }

    public function toggleActive(SmartInvoice $smartInvoice): JsonResponse
    {
        abort_unless($smartInvoice->user_id === auth()->id(), 403);

        $smartInvoice->update(['is_active' => !$smartInvoice->is_active]);

        return response()->json(['is_active' => $smartInvoice->is_active]);
    }

    public function destroyTemplate(SmartInvoice $smartInvoice): JsonResponse
    {
        abort_unless($smartInvoice->user_id === auth()->id(), 403);

        $smartInvoice->invoiceTemplate()->delete();
        $smartInvoice->delete();

        return response()->json(['ok' => true]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'contractor_id'   => 'required|integer',
            'bank_account_id' => 'nullable|integer',
            'basis'           => 'nullable|string|max:500',
            'nds_rate'        => 'required|integer|in:0,10,20',
            'items'           => 'required|array|min:1',
            'items.*.name'    => 'required|string|max:500',
            'items.*.unit'    => 'required|string|max:50',
            'items.*.qty'     => 'required|numeric|min:0',
            'items.*.price'   => 'required|numeric|min:0',
            'period_months'   => 'required|integer|in:1,2,3,6',
            'day_of_month'    => 'required|integer|min:1|max:31',
            'with_act'        => 'boolean',
            'invoice_id'      => 'nullable|integer',
        ]);

        abort_unless(
            $user->contractors()->where('id', $data['contractor_id'])->exists(),
            403
        );

        $template = InvoiceTemplate::create([
            'user_id'        => $user->id,
            'contractor_id'  => $data['contractor_id'],
            'bank_account_id'=> $data['bank_account_id'] ?? null,
            'basis'          => $data['basis'] ?? null,
            'nds_rate'       => $data['nds_rate'],
            'items'          => $data['items'],
        ]);

        $nextRunAt = $this->calcNextRun((int) $data['day_of_month']);

        $smartInvoice = SmartInvoice::create([
            'user_id'             => $user->id,
            'invoice_template_id' => $template->id,
            'period_months'       => $data['period_months'],
            'day_of_month'        => $data['day_of_month'],
            'with_act'            => $data['with_act'] ?? false,
            'next_run_at'         => $nextRunAt,
        ]);

        if (!empty($data['invoice_id'])) {
            $invoice = $user->invoices()->find($data['invoice_id']);

            if ($invoice) {
                $periodStart = $invoice->date->copy();
                $periodEnd   = $this->actService->periodEnd($periodStart, (int) $data['period_months']);
                $dateRange   = $this->actService->dateRange($periodStart, $periodEnd);

                $this->actService->appendPeriodToInvoiceItems($invoice, $dateRange);

                if ($smartInvoice->with_act) {
                    $this->actService->createAct($smartInvoice, $invoice, $periodStart, $periodEnd);
                }
            }
        }

        return response()->json(['ok' => true], 201);
    }

    private function calcNextRun(int $day): Carbon
    {
        $now  = now();
        $last = $now->copy()->daysInMonth;
        $d    = min($day, $last);
        $date = $now->copy()->startOfMonth()->addDays($d - 1);

        if ($date->lte($now)) {
            $next      = $now->copy()->addMonth()->startOfMonth();
            $d         = min($day, $next->daysInMonth);
            $date      = $next->addDays($d - 1);
        }

        return $date;
    }
}
