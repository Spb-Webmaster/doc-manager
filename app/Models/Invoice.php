<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'contractor_id', 'contract_id', 'bank_account_id',
    'number', 'date', 'due_date',
    'basis', 'status',
    'subtotal', 'nds_amount', 'total',
    'comment', 'stamp_path', 'stamp_scale', 'signature_path', 'signature_scale',
])]
class Invoice extends Model
{
    protected $fillable = [
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date'       => 'date',
            'due_date'   => 'date',
            'status'     => InvoiceStatus::class,
            'subtotal'   => 'decimal:2',
            'nds_amount' => 'decimal:2',
            'total'      => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function acts(): HasMany
    {
        return $this->hasMany(Act::class);
    }
}
