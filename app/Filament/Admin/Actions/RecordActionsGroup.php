<?php

namespace App\Filament\Admin\Actions;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

class RecordActionsGroup extends ActionGroup
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actions([
            ViewAction::make()->hiddenLabel(),
            EditAction::make()->hiddenLabel(),
            DeleteAction::make()->hiddenLabel(),
        ]);
    }
}
