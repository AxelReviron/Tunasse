<?php

namespace App\Filament\Admin\Resources\Accounts\Pages;

use App\Filament\Admin\Resources\Accounts\AccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->user()->getKey();

        return $data;
    }
}
