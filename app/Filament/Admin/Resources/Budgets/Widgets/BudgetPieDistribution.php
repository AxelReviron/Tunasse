<?php

namespace App\Filament\Admin\Resources\Budgets\Widgets;

use App\Filament\Admin\Widgets\PieDistributionWidget;

class BudgetPieDistribution extends PieDistributionWidget
{
    protected function getRelationName(): string
    {
        return 'budgets';
    }

    protected function getValueField(): string
    {
        return 'amount';
    }

    protected function getDescriptionKey(): string
    {
        return 'budget.widgets.total_stats';
    }

    protected function getEmptyStateKey(): string
    {
        return 'budget.widgets.total_stats_no_budget';
    }
}
