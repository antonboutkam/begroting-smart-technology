<?php

declare(strict_types=1);

namespace Roc\SmartTech\Begroting\Classes;

final class PriceCalculator
{
    public static function fillVatValues(?float $excl, ?float $incl, float $vatRate): array
    {
        $factor = 1 + ($vatRate / 100);

        if ($excl !== null && $incl === null) {
            $incl = round($excl * $factor, 2);
        }

        if ($incl !== null && $excl === null && $factor > 0) {
            $excl = round($incl / $factor, 2);
        }

        return [
            'excl' => $excl,
            'incl' => $incl,
        ];
    }
}
