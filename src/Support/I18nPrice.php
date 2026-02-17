<?php

declare(strict_types=1);

namespace App\Support;

final class I18nPrice
{
    public static function format(float $amount): string
    {
        $currency = I18n::t('lang.currency');

        $lang = I18n::lang();

        // Basic locale formatting
        switch ($lang)
        {
            case 'en':
                $formatted = number_format($amount, 2, '.', ',');
                return $currency . ' ' . $formatted;

            default:
                // sl, de, hr
                $formatted = number_format($amount, 2, ',', '.');
                return $formatted . ' ' . $currency;
        }
    }
}
