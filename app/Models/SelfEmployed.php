<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'full_name', 'inn',
    'register_address', 'address',
    'phone', 'email',
    'passport_serial', 'passport_number', 'who_issued', 'date_issued',
    'bank', 'payment_account', 'bik', 'correspondent_account',
])]
class SelfEmployed extends Model
{
    protected $table = 'self_employed';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
