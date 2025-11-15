<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasLabel
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case CREDITS = 'credits';
    case INVESTMENT = 'investment';

    public function getLabel(): string
    {
        return match ($this) {
            self::CHECKING => __('account.checking'),
            self::SAVINGS => __('account.savings'),
            self::CREDITS => __('account.credit'),
            self::INVESTMENT => __('account.investment'),
        };
    }
}
