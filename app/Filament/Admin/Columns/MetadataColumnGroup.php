<?php

namespace App\Filament\Admin\Columns;

use App\Helper\DateHelper;
use Closure;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;

class MetadataColumnGroup extends ColumnGroup
{
    public static function make(string|Closure|Htmlable $label, array|Closure $columns = []): static
    {
        return parent::make(
            $label,
            $columns ?: [
                TextColumn::make('created_at')
                    ->label(__('filament.created_at'))
                    ->formatStateUsing(fn ($state) => DateHelper::formatDate($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('filament.updated_at'))
                    ->formatStateUsing(fn ($state) => DateHelper::formatDate($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('filament.deleted_at'))
                    ->formatStateUsing(fn ($state) => DateHelper::formatDate($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]
        );
    }
}
