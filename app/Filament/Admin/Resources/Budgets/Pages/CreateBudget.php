<?php

namespace App\Filament\Admin\Resources\Budgets\Pages;

use App\Filament\Admin\Resources\Budgets\BudgetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->getKey();

        return $data;
    }
}
