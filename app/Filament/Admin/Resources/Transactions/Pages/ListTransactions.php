<?php

namespace App\Filament\Admin\Resources\Transactions\Pages;

use App\Filament\Admin\Resources\Transactions\TransactionResource;
use App\Filament\Admin\Resources\Transactions\Widgets\CurrentMonthTransactions;
use App\Filament\Admin\Resources\Transactions\Widgets\RecurringIncomingExpensesOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            RecurringIncomingExpensesOverview::class,
            CurrentMonthTransactions::class,
        ];
    }
}
