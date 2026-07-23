<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ActStatus;
use App\Models\Act;
use App\Models\Invoice;
use App\Models\SmartInvoice;
use Illuminate\Support\Carbon;

class SmartInvoiceActService
{
    public function periodEnd(Carbon $periodStart, int $months): Carbon
    {
        return $periodStart->copy()->addMonths($months);
    }

    public function dateRange(Carbon $periodStart, Carbon $periodEnd): string
    {
        return ' С ' . $periodStart->format('d.m.Y') . 'г. по ' . $periodEnd->format('d.m.Y') . 'г.';
    }

    public function appendPeriodToInvoiceItems(Invoice $invoice, string $dateRange): void
    {
        foreach ($invoice->items as $item) {
            $item->update(['name' => $item->name . $dateRange]);
        }
    }

    public function createAct(SmartInvoice $smart, Invoice $invoice, Carbon $periodStart, Carbon $periodEnd): Act
    {
        $template = $smart->invoiceTemplate;
        $userId   = $smart->user_id;

        $dateRange   = $this->dateRange($periodStart, $periodEnd);
        $basisSuffix = !empty($template->basis) ? ' Основание: ' . $template->basis . '.' : '';

        $ndsRate   = $template->nds_rate;
        $items     = $template->items;
        $subtotal  = collect($items)->sum(fn($it) => $it['qty'] * $it['price']);
        $ndsAmount = round($subtotal * $ndsRate / 100, 2);
        $total     = $subtotal + $ndsAmount;

        $act = Act::create([
            'user_id'         => $userId,
            'contractor_id'   => $template->contractor_id,
            'invoice_id'      => $invoice->id,
            'bank_account_id' => $template->bank_account_id,
            'number'          => (string) $this->nextActNumber($userId),
            'date'            => $periodEnd->copy()->addDay(),
            'basis'           => $template->basis,
            'status'          => ActStatus::Draft,
            'subtotal'        => $subtotal,
            'nds_amount'      => $ndsAmount,
            'total'           => $total,
            'stamp_path'      => $invoice->stamp_path,
            'stamp_scale'     => $invoice->stamp_scale,
            'signature_path'  => $invoice->signature_path,
            'signature_scale' => $invoice->signature_scale,
        ]);

        foreach ($items as $i => $item) {
            $amount  = $item['qty'] * $item['price'];
            $itemNds = round($amount * $ndsRate / 100, 2);
            $act->items()->create([
                'sort_order' => $i,
                'name'       => $item['name'] . $dateRange . $basisSuffix,
                'unit'       => $item['unit'],
                'quantity'   => $item['qty'],
                'price'      => $item['price'],
                'amount'     => $amount,
                'nds_rate'   => $ndsRate,
                'nds_amount' => $itemNds,
            ]);
        }

        return $act;
    }

    public function nextActNumber(int $userId): int
    {
        return (Act::where('user_id', $userId)
            ->pluck('number')
            ->map(fn($n) => is_numeric($n) ? (int) $n : 0)
            ->max() ?? 0) + 1;
    }
}
