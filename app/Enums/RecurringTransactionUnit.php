<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RecurringTransactionUnit: string implements HasLabel
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';

    public function getLabel(): string
    {
        return match ($this) {
            self::DAY => __('transaction.units.day'),
            self::WEEK => __('transaction.units.week'),
            self::MONTH => __('transaction.units.month'),
            self::YEAR => __('transaction.units.year'),
        };
    }
}
