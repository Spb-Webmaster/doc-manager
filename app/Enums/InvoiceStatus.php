<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft     = 'draft';
    case Sent      = 'sent';
    case Paid      = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Черновик',
            self::Sent      => 'Отправлен',
            self::Paid      => 'Оплачен',
            self::Cancelled => 'Отменён',
        };
    }
}
