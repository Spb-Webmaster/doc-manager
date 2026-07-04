<?php

declare(strict_types=1);

namespace App\Enums;

enum ActStatus: string
{
    case Draft     = 'draft';
    case Sent      = 'sent';
    case Signed    = 'signed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Draft     => 'Черновик',
            self::Sent      => 'Отправлен',
            self::Signed    => 'Подписан',
            self::Cancelled => 'Отменён',
        };
    }

    public function toString(): string
    {
        return $this->label();
    }
}
