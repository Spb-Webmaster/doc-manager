<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'invoice_template_id', 'period_months', 'day_of_month', 'with_act', 'is_active', 'next_run_at', 'last_run_at'])]
class SmartInvoice extends Model
{
    protected $casts = [
        'with_act'    => 'boolean',
        'is_active'   => 'boolean',
        'next_run_at' => 'date:Y-m-d',
        'last_run_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceTemplate(): BelongsTo
    {
        return $this->belongsTo(InvoiceTemplate::class);
    }
}
