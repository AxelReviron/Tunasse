<?php

namespace App\Helper;

use Carbon\Carbon;

class DateHelper
{
    public static function formatDate(?Carbon $date): string
    {
        return $date?->locale(app()->getLocale())->isoFormat('L LT') ?? '-';
    }
}
