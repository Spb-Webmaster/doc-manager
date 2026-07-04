<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
class Contractor extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function acts(): HasMany
    {
        return $this->hasMany(Act::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
