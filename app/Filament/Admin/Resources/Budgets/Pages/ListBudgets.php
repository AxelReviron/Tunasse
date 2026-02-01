<?php

namespace App\Filament\Admin\Resources\Budgets\Pages;

use App\Filament\Admin\Resources\Budgets\BudgetResource;
use App\Filament\Admin\Resources\Budgets\Widgets\BudgetAmountBarChart;
use App\Filament\Admin\Resources\Budgets\Widgets\BudgetPieDistribution;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            BudgetPieDistribution::class,
            BudgetAmountBarChart::class,
        ];
    }
}
