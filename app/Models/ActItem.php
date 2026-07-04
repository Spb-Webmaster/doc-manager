<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'act_id',
    'sort_order',
    'name', 'unit',
    'quantity', 'price', 'amount',
    'nds_rate', 'nds_amount',
])]
class ActItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:3',
            'price'      => 'decimal:2',
            'amount'     => 'decimal:2',
            'nds_amount' => 'decimal:2',
        ];
    }

    public function act(): BelongsTo
    {
        return $this->belongsTo(Act::class);
    }
}
