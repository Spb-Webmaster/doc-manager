<?php

declare(strict_types=1);

namespace App\Helpers;

class MoneyHelper
{
    private const ONES = [
        '', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',
        'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать',
        'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать',
    ];

    private const ONES_F = [
        '', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять',
        'десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать',
        'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать',
    ];

    private const TENS = [
        '', '', 'двадцать', 'тридцать', 'сорок', 'пятьдесят',
        'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто',
    ];

    private const HUNDREDS = [
        '', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот',
        'шестьсот', 'семьсот', 'восемьсот', 'девятьсот',
    ];

    // [именительный ед, родительный ед, родительный мн]
    private const THOUSANDS = ['тысяча', 'тысячи', 'тысяч'];
    private const MILLIONS  = ['миллион', 'миллиона', 'миллионов'];
    private const BILLIONS  = ['миллиард', 'миллиарда', 'миллиардов'];

    private const RUBLES = ['рубль', 'рубля', 'рублей'];
    private const KOPEKS = ['копейка', 'копейки', 'копеек'];

    public static function rubles(float $amount): string
    {
        $rubles = (int) abs($amount);
        $kopeks = (int) round((abs($amount) - $rubles) * 100);

        $rublesStr = self::number($rubles, false);
        $kopeksStr = sprintf('%02d', $kopeks);

        return ucfirst($rublesStr) . ' ' . self::plural($rubles, self::RUBLES)
            . ' ' . $kopeksStr . ' ' . self::plural($kopeks, self::KOPEKS);
    }

    private static function number(int $n, bool $feminine): string
    {
        if ($n === 0) return 'ноль';

        $parts = [];

        if ($n >= 1_000_000_000) {
            $b = (int) ($n / 1_000_000_000);
            $parts[] = self::chunk($b, false) . ' ' . self::plural($b, self::BILLIONS);
            $n %= 1_000_000_000;
        }

        if ($n >= 1_000_000) {
            $m = (int) ($n / 1_000_000);
            $parts[] = self::chunk($m, false) . ' ' . self::plural($m, self::MILLIONS);
            $n %= 1_000_000;
        }

        if ($n >= 1_000) {
            $t = (int) ($n / 1_000);
            $parts[] = self::chunk($t, true) . ' ' . self::plural($t, self::THOUSANDS);
            $n %= 1_000;
        }

        if ($n > 0) {
            $parts[] = self::chunk($n, $feminine);
        }

        return implode(' ', array_filter($parts));
    }

    private static function chunk(int $n, bool $feminine): string
    {
        $parts = [];

        $h = (int) ($n / 100);
        if ($h) $parts[] = self::HUNDREDS[$h];

        $r = $n % 100;
        if ($r >= 20) {
            $parts[] = self::TENS[(int) ($r / 10)];
            $o = $r % 10;
            if ($o) $parts[] = $feminine ? self::ONES_F[$o] : self::ONES[$o];
        } elseif ($r > 0) {
            $parts[] = $feminine ? self::ONES_F[$r] : self::ONES[$r];
        }

        return implode(' ', array_filter($parts));
    }

    private static function plural(int $n, array $forms): string
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;

        if ($n > 10 && $n < 20) return $forms[2];
        if ($n1 === 1)           return $forms[0];
        if ($n1 >= 2 && $n1 <= 4) return $forms[1];
        return $forms[2];
    }
}
