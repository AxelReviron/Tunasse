<?php

namespace App\Helper;

use Carbon\Carbon;

class DateHelper
{
    public static function formatDate(Carbon|string|null $date, bool $withTime = true): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (! $date) {
            return '-';
        }

        $format = $withTime ? 'L LT' : 'L';

        return $date->locale(app()->getLocale())->isoFormat($format);
    }
}
