<?php

namespace App\Enums;

enum RecurringTransactionUnit: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
}
