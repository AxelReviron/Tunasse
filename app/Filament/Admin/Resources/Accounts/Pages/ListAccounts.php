<?php

namespace App\Filament\Admin\Resources\Accounts\Pages;

use App\Filament\Admin\Resources\Accounts\AccountResource;
use App\Filament\Admin\Resources\Accounts\Widgets\AccountBalanceBarChart;
use App\Filament\Admin\Resources\Accounts\Widgets\AccountPieDistribution;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            AccountPieDistribution::class,
            AccountBalanceBarChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}
