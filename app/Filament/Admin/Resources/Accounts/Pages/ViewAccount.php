<?php

namespace App\Filament\Admin\Resources\Accounts\Pages;

use App\Filament\Admin\Resources\Accounts\AccountResource;
use App\Filament\Admin\Widgets\RecordMetadataWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RecordMetadataWidget::class,
        ];
    }
}
