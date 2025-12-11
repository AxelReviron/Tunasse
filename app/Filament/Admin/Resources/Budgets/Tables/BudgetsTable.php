<?php

namespace App\Filament\Admin\Resources\Budgets\Tables;

use App\Filament\Admin\Actions\RecordActionsGroup;
use App\Filament\Admin\Columns\MetadataColumnGroup;
use App\Helper\DateHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BudgetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereBelongsTo(auth()->user()))
            ->emptyStateHeading(__('budget.no_budgets_yet'))
            ->emptyStateIcon(Heroicon::OutlinedChartPie)
            ->columns([
                ColumnGroup::make(__('filament.infos'), [
                    TextColumn::make('label')
                        ->label(__('budget.label'))
                        ->searchable(),
                    TextColumn::make('amount')
                        ->label(__('budget.amount'))
                        ->suffix(fn ($record) => ' '.$record->currency?->symbol)
                        ->numeric()
                        ->sortable(),
                    TextColumn::make('start_date')
                        ->label(__('budget.start_date'))
                        ->formatStateUsing(fn ($state) => DateHelper::formatDate($state, false))
                        ->sortable(),
                    TextColumn::make('end_date')
                        ->label(__('budget.end_date'))
                        ->formatStateUsing(fn ($state) => DateHelper::formatDate($state, false))
                        ->sortable(),
                    ColorColumn::make('color')
                        ->label(__('budget.color'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('currency.name')
                        ->label(__('currency.currency'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('user.name')
                        ->label(__('budget.user'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                MetadataColumnGroup::make(__('filament.metadata')),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions(RecordActionsGroup::make([]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
