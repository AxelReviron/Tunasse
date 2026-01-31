<?php

namespace App\Filament\Admin\Resources\Accounts\Pages;

use App\Filament\Admin\Resources\Accounts\AccountResource;
use App\Filament\Admin\Widgets\RecordMetadataWidget;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RecordMetadataWidget::class,
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->user()->getKey();

        return $data;
    }
}
