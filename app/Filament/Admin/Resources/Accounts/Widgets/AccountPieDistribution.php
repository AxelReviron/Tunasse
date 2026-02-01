<?php

namespace App\Filament\Admin\Resources\Accounts\Widgets;

use App\Filament\Admin\Widgets\PieDistributionWidget;

class AccountPieDistribution extends PieDistributionWidget
{
    protected function getRelationName(): string
    {
        return 'accounts';
    }

    protected function getValueField(): string
    {
        return 'balance';
    }

    protected function getDescriptionKey(): string
    {
        return 'account.widgets.total_stats';
    }

    protected function getEmptyStateKey(): string
    {
        return 'account.widgets.total_stats_no_account';
    }
}
