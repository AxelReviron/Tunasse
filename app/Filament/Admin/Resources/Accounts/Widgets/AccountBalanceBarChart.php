<?php

namespace App\Filament\Admin\Resources\Accounts\Widgets;

use App\Filament\Admin\Widgets\BarChartWidget;

class AccountBalanceBarChart extends BarChartWidget
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
        return 'account.widgets.balance_distribution';
    }

    protected function getDataLabelKey(): string
    {
        return 'account.balance';
    }

    protected function getEmptyStateKey(): string
    {
        return 'account.widgets.total_stats_no_account';
    }

    protected function getEmptyStateLabelKey(): string
    {
        return 'account.widgets.no_data';
    }
}
