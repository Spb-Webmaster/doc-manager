<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name', 'full_name',
    'inn', 'kpp', 'ogrn', 'okved',
    'legal_address', 'address',
    'director', 'accountant', 'person_contract',
    'phone', 'email',
    'payment_nds', 'taxation_id',
    'bank', 'payment_account', 'bik', 'correspondent_account',
])]
class LegalEntity extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
