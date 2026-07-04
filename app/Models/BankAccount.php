<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'bank', 'city',
    'payment_account', 'correspondent_account', 'bik',
    'is_primary', 'sort_order',
])]
class BankAccount extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
