<?php

namespace App\Enums;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case CREDITS = 'credits';
    case INVESTMENT = 'investment';
}
