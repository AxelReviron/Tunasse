<?php

namespace App\Filament\Admin\Resources\Transactions\Pages;

use App\Filament\Admin\Resources\Transactions\TransactionResource;
use App\Models\Account;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->getKey();

        return $data;
    }

    protected function getFormActions(): array
    {
        $hasAccounts = Account::query()
            ->whereBelongsTo(auth()->user())
            ->exists();

        if (! $hasAccounts) {
            return [];
        }

        return parent::getFormActions();
    }
}
