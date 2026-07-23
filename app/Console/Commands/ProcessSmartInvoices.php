<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Jobs\SendSmartInvoiceCreatedMail;
use App\Models\Act;
use App\Models\Invoice;
use App\Models\SmartInvoice;
use App\Services\SmartInvoiceActService;
use Illuminate\Console\Command;

class ProcessSmartInvoices extends Command
{
    protected $signature   = 'smart-invoices:process';
    protected $description = 'Создаёт счета (и акты) по расписанию умных счетов';

    public function __construct(private readonly SmartInvoiceActService $actService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $today = now()->startOfDay();

        $smartInvoices = SmartInvoice::query()
            ->with(['invoiceTemplate.contractor', 'invoiceTemplate.bankAccount', 'user'])
            ->where('is_active', true)
            ->whereNotNull('next_run_at')
            ->whereDate('next_run_at', '<=', $today)
            ->get();

        if ($smartInvoices->isEmpty()) {
            $this->info('Нет умных счетов для обработки.');
            return self::SUCCESS;
        }

        $processed = 0;

        foreach ($smartInvoices as $smart) {
            try {
                $this->processOne($smart);
                $processed++;
            } catch (\Throwable $e) {
                $this->error("SmartInvoice #{$smart->id}: {$e->getMessage()}");
            }
        }

        $this->info("Обработано: {$processed}");
        return self::SUCCESS;
    }

    private function processOne(SmartInvoice $smart): void
    {
        $template    = $smart->invoiceTemplate;
        $userId      = $smart->user_id;
        $periodStart = $smart->next_run_at->copy();
        $periodEnd   = $this->actService->periodEnd($periodStart, $smart->period_months);
        $dateRange   = $this->actService->dateRange($periodStart, $periodEnd);

        $ndsRate   = $template->nds_rate;
        $items     = $template->items;
        $subtotal  = collect($items)->sum(fn($it) => $it['qty'] * $it['price']);
        $ndsAmount = round($subtotal * $ndsRate / 100, 2);
        $total     = $subtotal + $ndsAmount;

        $invoice = Invoice::create([
            'user_id'         => $userId,
            'contractor_id'   => $template->contractor_id,
            'bank_account_id' => $template->bank_account_id,
            'number'          => (string) $this->nextInvoiceNumber($userId),
            'date'            => $periodStart,
            'basis'           => $template->basis,
            'status'          => InvoiceStatus::Draft,
            'subtotal'        => $subtotal,
            'nds_amount'      => $ndsAmount,
            'total'           => $total,
        ]);

        foreach ($items as $i => $item) {
            $amount  = $item['qty'] * $item['price'];
            $itemNds = round($amount * $ndsRate / 100, 2);
            $invoice->items()->create([
                'sort_order' => $i,
                'name'       => $item['name'] . $dateRange,
                'unit'       => $item['unit'],
                'quantity'   => $item['qty'],
                'price'      => $item['price'],
                'amount'     => $amount,
                'nds_rate'   => $ndsRate,
                'nds_amount' => $itemNds,
            ]);
        }

        $act = null;
        if ($smart->with_act) {
            $act = $this->actService->createAct($smart, $invoice, $periodStart, $periodEnd);
        }

        $nextMonth = $periodEnd->copy()->startOfMonth();
        $d         = min($smart->day_of_month, $nextMonth->daysInMonth);

        $smart->update([
            'last_run_at' => now(),
            'next_run_at' => $nextMonth->addDays($d - 1),
        ]);

        $this->notifyInvoiceCreated($smart, $invoice, $act);
    }

    private function notifyInvoiceCreated(SmartInvoice $smart, Invoice $invoice, ?Act $act): void
    {
        $user = $smart->user;

        if (!$user || !$user->email || !$user->notify_invoice_from_template) {
            return;
        }

        SendSmartInvoiceCreatedMail::dispatch($invoice, $act, $user->email);
    }

    private function nextInvoiceNumber(int $userId): int
    {
        return (Invoice::where('user_id', $userId)
            ->pluck('number')
            ->map(fn($n) => is_numeric($n) ? (int) $n : 0)
            ->max() ?? 0) + 1;
    }
}
