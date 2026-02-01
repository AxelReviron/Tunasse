<?php

namespace App\Filament\Admin\Resources\Budgets\Widgets;

use App\Filament\Admin\Widgets\BarChartWidget;

class BudgetAmountBarChart extends BarChartWidget
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
        return 'budget.widgets.amount_distribution';
    }

    protected function getDataLabelKey(): string
    {
        return 'budget.amount';
    }

    protected function getEmptyStateKey(): string
    {
        return 'budget.widgets.total_stats_no_budget';
    }

    protected function getEmptyStateLabelKey(): string
    {
        return 'budget.widgets.no_data';
    }
}
