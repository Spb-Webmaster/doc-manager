<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'contractor_id', 'invoice_id', 'bank_account_id',
    'number', 'date', 'basis',
    'status',
    'subtotal', 'nds_amount', 'total',
    'comment',
])]
class Act extends Model
{
    protected function casts(): array
    {
        return [
            'date'       => 'date',
            'status'     => ActStatus::class,
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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ActItem::class)->orderBy('sort_order');
    }
}
