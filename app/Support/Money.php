<?php

declare(strict_types=1);

namespace App\Support;

use App\Settings\GeneralSettings;

final class Money
{
    /** Formats with the currency symbol from General settings; whole amounts drop the decimals. */
    public static function format(float|int|string|null $amount): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        $amount = (float) $amount;
        $symbol = app(GeneralSettings::class)->currency_symbol;

        return trim($symbol.' '.number_format($amount, $amount === floor($amount) ? 0 : 2));
    }
}
